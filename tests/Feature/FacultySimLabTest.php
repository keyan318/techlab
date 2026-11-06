<?php

namespace Tests\Feature;

use App\Models\CourseAssignment;
use App\Models\User;
use App\Services\CourseProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class FacultySimLabTest extends TestCase
{
    use RefreshDatabase;

    protected bool $skipQuizGate = true;

    private function faculty(string $catalog = 'networking-fundamentals', string $planet = 'networking'): User
    {
        $crew = $this->crewFor($planet, $catalog);
        CourseAssignment::firstOrCreate(['planet' => $planet, 'course_slug' => $catalog], ['faculty_id' => $crew->teacher_id]);

        return $crew->faculty;
    }

    private function url(string $name, string $module = 'm2', string $lesson = 'lesson03', string $course = 'networking'): string
    {
        return route("faculty.courses.{$name}", ['course' => $course, 'module' => $module, 'lesson' => $lesson]);
    }

    /** The editor's document turned back into what the editor saves. */
    private function body(array $doc, callable $change): array
    {
        $body = [
            'title' => $doc['title'],
            'steps' => array_map(fn ($s) => [
                'title' => $s['title'],
                'body' => $s['body'],
                'objectives' => array_map(fn ($o) => [
                    'label' => $o['label'],
                    'values' => collect($o['fields'])->mapWithKeys(fn ($f) => [$f['path'] => $f['value']])->all(),
                ], $s['objectives']),
            ], $doc['steps']),
        ];
        if ($doc['blurb'] !== null) {
            $body['blurb'] = $doc['blurb'];
        }
        $body['story'] = $doc['story'];
        $body['files'] = collect($doc['files'])->mapWithKeys(fn ($f) => [$f['path'] => $f['content']])->all();

        return $change($body);
    }

    /** Play the version through in the lab editor's simulator (a draft the faculty passes), then save it. */
    private function playAndSave(User $faculty, callable $url, array $body): TestResponse
    {
        $draft = $this->actingAs($faculty)->postJson($url('sim-lab.test'), $body)->assertOk()->json();
        $this->actingAs($faculty)->getJson($draft['file'])->assertOk();
        $this->actingAs($faculty)->postJson(preg_replace('/\.json$/', '/passed', $draft['file']))->assertOk();

        return $this->actingAs($faculty)->putJson($url('sim-lab.update'), $body + ['draft' => $draft['token']]);
    }

    public function test_the_editor_shows_the_netsim_labs_instructions_and_checks(): void
    {
        $doc = $this->actingAs($this->faculty())->getJson($this->url('sim-lab'))->assertOk()->json();

        $this->assertSame('netsim', $doc['sim']);
        $this->assertFalse($doc['edited']);
        $this->assertSame('Fix the address', $doc['steps'][0]['title']);
        $subnet = collect($doc['steps'][0]['objectives'][0]['fields'])->firstWhere('path', 'inSubnet');
        $this->assertSame('192.168.0.0/24', $subnet['value']);
        // Devices are picked from the lab's network, results from what the check allows.
        $device = collect($doc['steps'][0]['objectives'][0]['fields'])->firstWhere('path', 'device');
        $this->assertSame('select', $device['input']);
        $this->assertContains('rivet', $device['options']);
        $expect = collect($doc['steps'][0]['objectives'][1]['fields'])->firstWhere('path', 'expect');
        $this->assertSame(['success', 'fail'], $expect['options']);
    }

    public function test_a_saved_lab_is_what_the_simulator_loads(): void
    {
        $faculty = $this->faculty();
        $doc = $this->actingAs($faculty)->getJson($this->url('sim-lab'))->json();

        $this->playAndSave($faculty, fn ($n) => $this->url($n), $this->body($doc, function ($b) {
            $b['title'] = 'Lesson 2.3: Rivet moves house';
            $b['steps'][0]['body'] = 'Put Rivet on `192.168.5.0/24`.';
            $b['steps'][0]['objectives'][0]['label'] = 'rivet is in 192.168.5.0/24';
            $b['steps'][0]['objectives'][0]['values']['inSubnet'] = '192.168.5.0/24';

            return $b;
        }))->assertOk();

        $student = $this->studentIn('networking', 'networking-fundamentals');
        foreach (array_slice(CourseProgressService::order('networking'), 0, 8) as $item) {
            CourseProgressService::markComplete($student, $item['module'], $item['lesson'], 'networking');
        }
        $page = $this->actingAs($student)->get(route('student.planet.lab', ['slug' => 'networking', 'module' => 'm2', 'lesson' => 'lesson03']))->assertOk()->getContent();
        $this->assertStringContainsString('&file='.rawurlencode('/student/planet/networking/lab/m2/lesson03/file.json'), $page);

        $file = $this->actingAs($student)->getJson('/student/planet/networking/lab/m2/lesson03/file.json')->assertOk()->json();
        $this->assertSame('Lesson 2.3: Rivet moves house', $file['lab']['title']);
        $check = $file['lab']['steps'][0]['objectives'][0];
        $this->assertSame('rivet is in 192.168.5.0/24', $check['label']);
        $this->assertSame(['type' => 'iface-ip', 'device' => 'rivet', 'iface' => 'eth0', 'inSubnet' => '192.168.5.0/24'], $check['check']);
        // The network itself is still the file's.
        $this->assertNotEmpty($file['devices']);

        $this->actingAs($faculty)->deleteJson($this->url('sim-lab.reset'))->assertOk();
        $this->assertStringNotContainsString('&file=', $this->actingAs($student)->get(route('student.planet.lab', ['slug' => 'networking', 'module' => 'm2', 'lesson' => 'lesson03']))->getContent());
    }

    public function test_relay_console_labs_edit_the_answer_and_the_victory_message(): void
    {
        $faculty = $this->faculty('networking-2');
        $url = fn ($n) => $this->url($n, 'm2', 'lesson04', 'networking-2');
        $doc = $this->actingAs($faculty)->getJson($url('sim-lab'))->assertOk()->json();
        $this->assertSame('console', $doc['sim']);
        $this->assertArrayHasKey('victory', $doc['story']);

        $this->playAndSave($faculty, $url, $this->body($doc, function ($b) {
            $b['story']['victory'] = 'Peering is up. Well done, astronaut.';
            foreach ($b['steps'] as $i => $s) {
                foreach ($s['objectives'] as $k => $o) {
                    if (($o['values']['value'] ?? null) === 'EBGP=203.0.113.1') {
                        $b['steps'][$i]['objectives'][$k]['values']['value'] = 'EBGP=198.51.100.1';
                    }
                }
            }

            return $b;
        }))->assertOk();

        $file = $this->actingAs($this->studentIn('networking', 'networking-2'))->getJson('/student/planet/networking-2/lab/m2/lesson04/file.json')->assertOk()->json();
        $this->assertSame('Peering is up. Well done, astronaut.', $file['story']['victory']);
        $answers = collect($file['steps'])->flatMap(fn ($s) => $s['objectives'])->pluck('check.value')->filter()->all();
        $this->assertContains('EBGP=198.51.100.1', $answers);
        $this->assertNotContains('EBGP=203.0.113.1', $answers);
    }

    public function test_citadel_labs_edit_categories_flags_files_and_the_story(): void
    {
        $faculty = $this->faculty('information-security-1', 'cybersecurity');
        $url = fn ($n) => $this->url($n, 'm1', 'lesson01', 'cybersecurity');
        $doc = $this->actingAs($faculty)->getJson($url('sim-lab'))->assertOk()->json();

        $this->assertSame('citadel', $doc['sim']);
        $this->assertSame('Citadel Sim', $doc['simulator']);
        $this->assertSame(['alert', 'vex', 'victory'], array_keys($doc['story']));
        $this->assertContains('C:\\Citadel\\inventory.txt', array_column($doc['files'], 'path'));
        // Items, files and accounts are picked from the lab's own.
        $item = collect($doc['steps'][0]['objectives'][1]['fields'])->firstWhere('path', 'item');
        $this->assertSame('select', $item['input']);
        $this->assertContains('starmap', $item['options']);

        $this->playAndSave($faculty, $url, $this->body($doc, function ($b) {
            $b['story']['vex'] = 'Your walls are still paper, astronaut.';
            $b['files']['C:\Citadel\temp\note.txt'] = 'Remember the code: FLAG{NEW-WALLS}';
            foreach ($b['steps'] as $i => $s) {
                foreach ($s['objectives'] as $k => $o) {
                    if (($o['values']['value'] ?? null) === 'FLAG{WALLS-ARE-PAPER}') {
                        $b['steps'][$i]['objectives'][$k]['values']['value'] = 'FLAG{NEW-WALLS}';
                    }
                    if (($o['values']['item'] ?? null) === 'starmap') {
                        $b['steps'][$i]['objectives'][$k]['values']['as'] = 'Vuln';   // shorthand, any case
                    }
                }
            }

            return $b;
        }))->assertOk();

        $student = $this->studentIn('cybersecurity', 'information-security-1', ['role' => 'student', 'planets' => ['cybersecurity']]);
        $page = $this->actingAs($student)->get(route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm1', 'lesson' => 'lesson01']))->assertOk()->getContent();
        $this->assertStringContainsString('citadel-sim/index.html', $page);
        $this->assertStringContainsString('&file='.rawurlencode('/student/planet/cybersecurity/lab/m1/lesson01/file.json'), $page);

        $file = $this->actingAs($student)->getJson('/student/planet/cybersecurity/lab/m1/lesson01/file.json')->assertOk()->json();
        $this->assertSame('Your walls are still paper, astronaut.', $file['story']['vex']);
        $this->assertSame('Remember the code: FLAG{NEW-WALLS}', $file['files']['C:\Citadel\temp\note.txt']);
        // The simulator only accepts a flag listed in the lab, so the new one replaces the old one there too.
        $this->assertSame(['FLAG{NEW-WALLS}'], $file['flags']);
        $checks = collect($file['steps'])->flatMap(fn ($s) => $s['objectives'])->pluck('check');
        $this->assertSame('vulnerability', $checks->firstWhere('item', 'starmap')['as']);
    }

    public function test_a_citadel_category_must_be_one_word(): void
    {
        $faculty = $this->faculty('information-security-1', 'cybersecurity');
        $url = fn ($n) => $this->url($n, 'm1', 'lesson01', 'cybersecurity');
        $doc = $this->actingAs($faculty)->getJson($url('sim-lab'))->json();

        $this->actingAs($faculty)->putJson($url('sim-lab.update'), $this->body($doc, function ($b) {
            $b['steps'][0]['objectives'][1]['values']['as'] = 'an asset';

            return $b;
        }))->assertStatus(422);
    }

    public function test_new_wording_saves_but_new_checks_must_be_passed_first(): void
    {
        $faculty = $this->faculty();
        $doc = $this->actingAs($faculty)->getJson($this->url('sim-lab'))->json();
        $this->assertSame('netsim', $doc['source']);
        $this->assertStringEndsWith('netsim-app/app.html', $doc['frame']);

        // Wording only: nothing to play through.
        $this->actingAs($faculty)->putJson($this->url('sim-lab.update'), $this->body($doc, function ($b) {
            $b['steps'][0]['body'] = 'Give Rivet a working address.';

            return $b;
        }))->assertOk();

        $moved = $this->body($doc, function ($b) {
            $b['steps'][0]['objectives'][0]['values']['inSubnet'] = '192.168.5.0/24';

            return $b;
        });
        // A new check, never tested: refused.
        $this->actingAs($faculty)->putJson($this->url('sim-lab.update'), $moved)->assertStatus(422)->assertJson(['untested' => true]);

        // Tested but not passed: refused.
        $draft = $this->actingAs($faculty)->postJson($this->url('sim-lab.test'), $moved)->assertOk()->json();
        $this->assertSame('192.168.5.0/24', $this->actingAs($faculty)->getJson($draft['file'])->json('lab.steps.0.objectives.0.check.inSubnet'));
        $this->actingAs($faculty)->putJson($this->url('sim-lab.update'), $moved + ['draft' => $draft['token']])->assertStatus(422);

        // Passed, but then the checks were changed again: refused.
        $this->actingAs($faculty)->postJson(preg_replace('/\.json$/', '/passed', $draft['file']))->assertOk();
        $other = $moved;
        $other['steps'][0]['objectives'][0]['values']['inSubnet'] = '192.168.9.0/24';
        $this->actingAs($faculty)->putJson($this->url('sim-lab.update'), $other + ['draft' => $draft['token']])->assertStatus(422);

        // The version that was passed: saved.
        $this->actingAs($faculty)->putJson($this->url('sim-lab.update'), $moved + ['draft' => $draft['token']])->assertOk();

        // A draft belongs to the faculty member who made it.
        $this->actingAs(User::factory()->create(['role' => 'faculty']))->getJson($draft['file'])->assertForbidden();
    }

    public function test_bad_values_are_refused(): void
    {
        $faculty = $this->faculty();
        $doc = $this->actingAs($faculty)->getJson($this->url('sim-lab'))->json();

        $this->actingAs($faculty)->putJson($this->url('sim-lab.update'), $this->body($doc, function ($b) {
            $b['steps'][0]['objectives'][1]['values']['expect'] = 'maybe';

            return $b;
        }))->assertStatus(422);
        $this->actingAs($faculty)->putJson($this->url('sim-lab.update'), $this->body($doc, function ($b) {
            $b['steps'][0]['objectives'][0]['values']['device'] = 'nobody';

            return $b;
        }))->assertStatus(422);
        $this->actingAs($faculty)->putJson($this->url('sim-lab.update'), $this->body($doc, function ($b) {
            $b['steps'][0]['body'] = '  ';

            return $b;
        }))->assertStatus(422);
    }

    public function test_only_the_courses_faculty_and_not_programming(): void
    {
        $this->faculty();
        $other = User::factory()->create(['role' => 'faculty']);
        $this->actingAs($other)->getJson($this->url('sim-lab'))->assertForbidden();

        $crew = $this->crewFor('programming', 'computer-programming-1');
        CourseAssignment::firstOrCreate(['planet' => 'programming', 'course_slug' => 'computer-programming-1'], ['faculty_id' => $crew->teacher_id]);
        $this->actingAs($crew->faculty)->getJson($this->url('sim-lab', 'm1', 'lesson01', 'computer-programming-1'))->assertNotFound();
    }
}
