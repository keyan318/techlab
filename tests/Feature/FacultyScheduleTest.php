<?php

namespace Tests\Feature;

use App\Exceptions\NvidiaNimException;
use App\Models\Crew;
use App\Models\TeacherClass;
use App\Models\User;
use App\Services\Quiz\QuizNimService;
use App\Services\Schedule\ScheduleExtractor;
use App\Services\Schedule\VisionCaller;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FacultyScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function faculty(): User
    {
        $t = User::factory()->create(['role' => 'faculty']);
        $crew = Crew::create(['name' => 'Alpha', 'code' => 'ABC123', 'teacher_id' => $t->id]);
        $t->update(['crew_id' => $crew->id]);

        return $t;
    }

    /** A real drawn timetable-like picture (grid + text). fake()->image() is a flat colour and would (rightly) be rejected as blank. */
    private function drawn(string $name = 'timetable.png', bool $blurry = false): UploadedFile
    {
        $im = imagecreatetruecolor(1100, 500);
        imagefill($im, 0, 0, imagecolorallocate($im, 255, 255, 255));
        $ink = imagecolorallocate($im, 0, 0, 0);
        for ($i = 0; $i < 5; $i++) {
            imageline($im, 20, 40 + $i * 90, 1080, 40 + $i * 90, $ink);
            imagestring($im, 5, 40, 70 + $i * 90, "Monday 0{$i}:30 Math 8-A", $ink);
        }
        if ($blurry) {
            for ($i = 0; $i < 30; $i++) {
                imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR);
            }
        }
        $path = tempnam(sys_get_temp_dir(), 'tt').'.png';
        imagepng($im, $path);

        return new UploadedFile($path, $name, 'image/png', null, true);
    }

    private function modelReturns(array $json): void
    {
        $this->mock(QuizNimService::class, fn ($m) => $m->shouldReceive('completeJson')->once()->andReturn($json));
    }

    public function test_normalize_cleans_times_days_and_drops_junk(): void
    {
        $rows = app(ScheduleExtractor::class)->normalize(['classes' => [
            ['subject' => 'UI/UX Design', 'class' => 'Class 01', 'day' => 'monday', 'start' => '9:30 PM', 'end' => '10:15 PM'],
            ['subject' => 'Math', 'day' => 'Tue', 'start' => '0800', 'end' => '07:00'],       // "0800" = 08:00; end before start -> null
            ['subject' => 'Physics', 'day' => 'Tues', 'start' => '08:00', 'end' => '07:00'],   // end before start -> null
            ['subject' => 'Physics', 'day' => 'Tuesday', 'start' => '8.00 am'],                // duplicate slot -> kept once
            ['subject' => '', 'day' => 'Friday', 'start' => '09:00'],                          // no subject
            ['subject' => 'Art', 'day' => 'Someday', 'start' => '09:00'],                      // bad day
            ['subject' => 'PE', 'day' => 'Friday', 'start' => '25:00'],                        // bad time
        ]]);

        $this->assertSame(
            [['UI/UX Design', 1, '21:30', '22:15'], ['Math', 2, '08:00', null], ['Physics', 2, '08:00', null]],
            array_map(fn ($r) => [$r['subject'], $r['day'], $r['starts_at'], $r['ends_at']], $rows)
        );
        $this->assertSame('Class 01', $rows[0]['class_name']);
    }

    public function test_faculty_uploads_a_text_schedule_and_it_replaces_the_old_one(): void
    {
        $t = $this->faculty();
        TeacherClass::create(['user_id' => $t->id, 'subject' => 'Old', 'day' => 1, 'starts_at' => '07:00']);
        $this->modelReturns(['classes' => [
            ['subject' => 'Front-end Development', 'class' => 'Class 02', 'day' => 'Monday', 'start' => '10:15', 'end' => '11:00'],
            ['subject' => 'UI/UX Design', 'class' => 'Class 01', 'day' => 'Monday', 'start' => '09:30', 'end' => '10:15'],
        ]]);

        $res = $this->actingAs($t)->post(route('faculty.schedule.store'), [
            'file' => UploadedFile::fake()->createWithContent('schedule.txt', 'Monday: I teach UI/UX design at half past nine, then front-end after'),
        ], ['Accept' => 'application/json']);

        $res->assertOk()->assertJsonPath('classes.0.subject', 'UI/UX Design')->assertJsonPath('classes.1.start', '10:15');
        $this->assertSame(2, TeacherClass::where('user_id', $t->id)->count());
        $this->assertDatabaseMissing('teacher_classes', ['subject' => 'Old']);
    }

    public function test_an_image_schedule_is_transcribed_by_the_vision_model_then_structured(): void
    {
        $t = $this->faculty();
        $this->mock(VisionCaller::class, fn ($m) => $m->shouldReceive('transcribe')->once()
            ->withArgs(fn ($prompt, $url) => str_contains($prompt, 'Day|start-end') && str_starts_with($url, 'data:image/'))
            ->andReturn("| Time | Friday |\n| 13:00 | Biology |"));
        $this->mock(QuizNimService::class, fn ($m) => $m->shouldReceive('completeJson')->once()
            ->withArgs(fn ($messages) => str_contains($messages[1]['content'], '| 13:00 | Biology |'))
            ->andReturn(['classes' => [['subject' => 'Biology', 'day' => 'Friday', 'start' => '13:00']]]));

        $this->actingAs($t)->post(route('faculty.schedule.store'), ['file' => $this->drawn()], ['Accept' => 'application/json'])
            ->assertOk()->assertJsonPath('classes.0.subject', 'Biology');
    }

    public function test_blurry_and_blank_images_are_rejected_locally_without_any_ai_call(): void
    {
        $t = $this->faculty();
        $this->mock(VisionCaller::class, fn ($m) => $m->shouldNotReceive('transcribe'));
        $this->mock(QuizNimService::class, fn ($m) => $m->shouldNotReceive('completeJson'));

        $this->actingAs($t)->post(route('faculty.schedule.store'), ['file' => $this->drawn('blur.png', blurry: true)], ['Accept' => 'application/json'])
            ->assertStatus(422)->assertJsonPath('error', fn ($e) => str_contains($e, 'sharper'));

        $this->actingAs($t)->post(route('faculty.schedule.store'), ['file' => UploadedFile::fake()->image('blank.png', 800, 600)], ['Accept' => 'application/json'])
            ->assertStatus(422)->assertJsonPath('error', fn ($e) => str_contains($e, 'blank'));
    }

    public function test_vision_saying_unreadable_stops_early_but_a_good_table_with_a_stray_word_still_works(): void
    {
        $t = $this->faculty();
        $this->mock(VisionCaller::class, fn ($m) => $m->shouldReceive('transcribe')->twice()->andReturn('UNREADABLE', "| Time | Friday |\n| 13:00 | Biology |\n\nUNREADABLE"));
        $this->mock(QuizNimService::class, fn ($m) => $m->shouldReceive('completeJson')->once()
            ->andReturn(['classes' => [['subject' => 'Biology', 'day' => 'Friday', 'start' => '13:00']]]));

        $this->actingAs($t)->post(route('faculty.schedule.store'), ['file' => $this->drawn()], ['Accept' => 'application/json'])->assertStatus(422);
        $this->actingAs($t)->post(route('faculty.schedule.store'), ['file' => $this->drawn()], ['Accept' => 'application/json'])
            ->assertOk()->assertJsonPath('classes.0.subject', 'Biology');
    }

    public function test_a_file_with_no_days_or_times_is_rejected_without_any_ai_call(): void
    {
        $t = $this->faculty();
        $this->mock(VisionCaller::class, fn ($m) => $m->shouldNotReceive('transcribe'));
        $this->mock(QuizNimService::class, fn ($m) => $m->shouldNotReceive('completeJson'));

        $this->actingAs($t)->post(route('faculty.schedule.store'), ['file' => UploadedFile::fake()->createWithContent('notes.txt', 'Remember to buy milk and call mum')], ['Accept' => 'application/json'])
            ->assertStatus(422)->assertJsonPath('error', fn ($e) => str_contains($e, "doesn't look like a weekly timetable"));
    }

    public function test_nothing_found_or_unreadable_file_keeps_the_existing_schedule(): void
    {
        $t = $this->faculty();
        TeacherClass::create(['user_id' => $t->id, 'subject' => 'Keep me', 'day' => 3, 'starts_at' => '09:00']);

        $this->modelReturns(['classes' => []]);
        $this->actingAs($t)->post(route('faculty.schedule.store'), ['file' => UploadedFile::fake()->createWithContent('a.txt', 'Monday: sometime around nine')], ['Accept' => 'application/json'])
            ->assertStatus(422);

        $this->actingAs($t)->post(route('faculty.schedule.store'), ['file' => UploadedFile::fake()->create('a.exe', 5)], ['Accept' => 'application/json'])
            ->assertStatus(422);

        $this->assertDatabaseHas('teacher_classes', ['subject' => 'Keep me']);
    }

    public function test_only_facultys_can_upload_and_the_classes_page_shows_the_schedule(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student)->post(route('faculty.schedule.store'), ['file' => UploadedFile::fake()->createWithContent('a.txt', 'x')], ['Accept' => 'application/json'])->assertForbidden();

        $t = $this->faculty();
        TeacherClass::create(['user_id' => $t->id, 'subject' => 'Robotics Lab', 'class_name' => 'Class 07', 'day' => 2, 'starts_at' => '09:30']);
        $this->actingAs($t)->get(route('faculty.classes'))->assertOk()
            ->assertSee('Upload schedule')->assertSee('Robotics Lab')->assertSee('Class 07');

        $this->actingAs($t)->deleteJson(route('faculty.schedule.destroy'))->assertOk();
        $this->assertSame(0, TeacherClass::count());
    }

    public function test_grid_parser_reads_days_across_and_days_down_and_csv(): void
    {
        $x = app(ScheduleExtractor::class);
        $across = $x->normalize(['classes' => $x->parseGrid("| Time | Monday | Tuesday |\n| --- | --- | --- |\n| 8:00 - 8:45 | Math 8-A | Physics 9-B |\n| 9:00 - 9:45 | Break | Computer Science 10 |\n| 1:00 - 1:45 pm | Art | |")]);
        $this->assertSame(
            [[1, '08:00', 'Math', '8-A'], [1, '13:00', 'Art', null], [2, '08:00', 'Physics', '9-B'], [2, '09:00', 'Computer Science', '10']],
            array_map(fn ($r) => [$r['day'], $r['starts_at'], $r['subject'], $r['class_name']], $across)
        );

        $down = $x->normalize(['classes' => $x->parseGrid("| Day | 08:00-08:45 | 09:00-09:45 |\n|---|---|---|\n| Mon | Math | Lunch |\n| Wed | Biology 7 | Chemistry |")]);
        $this->assertSame([[1, '08:00', 'Math'], [3, '08:00', 'Biology'], [3, '09:00', 'Chemistry']], array_map(fn ($r) => [$r['day'], $r['starts_at'], $r['subject']], $down));

        $csv = $x->normalize(['classes' => $x->parseGrid("Time,Monday,Friday\n09:30-10:15,UI/UX Design,Robotics\n")]);
        $this->assertCount(2, $csv);

        $this->assertSame([], $x->parseGrid('Monday: math at 9, tuesday: art at 10'));   // not a grid → falls back to the AI
    }

    public function test_a_clean_grid_from_a_photo_skips_the_second_ai_call(): void
    {
        $t = $this->faculty();
        $this->mock(VisionCaller::class, fn ($m) => $m->shouldReceive('transcribe')->once()
            ->andReturn("| Time | Monday | Tuesday |\n| --- | --- | --- |\n| 8:00 - 8:45 | Math 8-A | Physics 9-B |"));
        $this->mock(QuizNimService::class, fn ($m) => $m->shouldNotReceive('completeJson'));

        $this->actingAs($t)->post(route('faculty.schedule.store'), ['file' => $this->drawn()], ['Accept' => 'application/json'])
            ->assertOk()->assertJsonCount(2, 'classes')->assertJsonPath('classes.0.subject', 'Math');
    }

    public function test_compact_lines_from_a_photo_are_read_locally_with_no_second_ai_call(): void
    {
        $t = $this->faculty();
        $this->mock(VisionCaller::class, fn ($m) => $m->shouldReceive('transcribe')->once()
            ->andReturn("Mon|09:30-10:15|Mathematics|8-A\nMon|10:15-11:00|English|\nFri|13:00-13:45|Biology|Class 03"));
        $this->mock(QuizNimService::class, fn ($m) => $m->shouldNotReceive('completeJson'));

        $this->actingAs($t)->post(route('faculty.schedule.store'), ['file' => $this->drawn()], ['Accept' => 'application/json'])
            ->assertOk()->assertJsonCount(3, 'classes')
            ->assertJsonPath('classes.0.subject', 'Mathematics')->assertJsonPath('classes.0.class', '8-A')
            ->assertJsonPath('classes.1.class', null)->assertJsonPath('classes.2.day', 5)->assertJsonPath('classes.2.class', 'Class 03');
    }

    public function test_line_parser_reads_prose_csv_and_ignores_grids(): void
    {
        $x = app(ScheduleExtractor::class);

        $prose = $x->normalize(['classes' => $x->parseLines("Monday 9:30–10:15 Mathematics Grade 8-A\n- Tue: 1:00 - 1:45 pm Art\nWednesday, 08:00, Chemistry\nnotes: bring markers")]);
        $this->assertSame(
            [[1, '09:30', '10:15', 'Mathematics', 'Grade 8-A'], [2, '13:00', '13:45', 'Art', null], [3, '08:00', null, 'Chemistry', null]],
            array_map(fn ($r) => [$r['day'], $r['starts_at'], $r['ends_at'], $r['subject'], $r['class_name']], $prose)
        );

        $csv = $x->normalize(['classes' => $x->parseLines("Day,Time,Subject\nMonday,09:30,UI/UX Design\nTuesday,10:15-11:00,Robotics,Class 7")]);
        $this->assertSame([['UI/UX Design', null], ['Robotics', 'Class 7']], array_map(fn ($r) => [$r['subject'], $r['class_name']], $csv));

        // grids (either orientation) are NOT lines — parseGrid owns those
        $this->assertSame([], $x->parseLines("| Time | Monday | Tuesday |\n| --- | --- | --- |\n| 8:00 - 8:45 | Math | Art |"));
        $this->assertSame([], $x->parseLines("| Day | 08:00-08:45 | 09:00-09:45 |\n|---|---|---|\n| Mon | Math | Lunch |"));
    }

    public function test_a_csv_schedule_never_touches_the_ai(): void
    {
        $t = $this->faculty();
        $this->mock(VisionCaller::class, fn ($m) => $m->shouldNotReceive('transcribe'));
        $this->mock(QuizNimService::class, fn ($m) => $m->shouldNotReceive('completeJson'));

        $this->actingAs($t)->post(route('faculty.schedule.store'), [
            'file' => UploadedFile::fake()->createWithContent('schedule.csv', "Day,Time,Subject\nMonday,09:30,UI/UX Design\nMonday,10:15,Front-end Development\n"),
        ], ['Accept' => 'application/json'])->assertOk()->assertJsonCount(2, 'classes')->assertJsonPath('classes.1.subject', 'Front-end Development');
    }

    public function test_the_same_file_uploaded_again_is_answered_from_cache(): void
    {
        $t = $this->faculty();
        $this->mock(VisionCaller::class, fn ($m) => $m->shouldReceive('transcribe')->once()->andReturn('Mon|09:30-10:15|Mathematics|8-A'));

        for ($i = 0; $i < 2; $i++) {
            $this->actingAs($t)->post(route('faculty.schedule.store'), ['file' => $this->drawn()], ['Accept' => 'application/json'])
                ->assertOk()->assertJsonPath('classes.0.subject', 'Mathematics');
        }
    }

    public function test_live_mode_streams_stages_then_the_result(): void
    {
        $t = $this->faculty();
        $this->mock(VisionCaller::class, fn ($m) => $m->shouldReceive('transcribe')->once()->andReturn('Mon|09:30-10:15|Mathematics|8-A'));

        $body = $this->actingAs($t)->post(route('faculty.schedule.store'), ['file' => $this->drawn()], ['Accept' => 'application/x-ndjson'])
            ->assertOk()->streamedContent();
        $lines = array_map(fn ($l) => json_decode($l, true), array_filter(explode("\n", $body)));

        $this->assertSame(['stage' => 'reading'], $lines[0]);
        $last = end($lines);
        $this->assertTrue($last['done']);
        $this->assertSame('Mathematics', $last['classes'][0]['subject']);
        $this->assertSame(1, TeacherClass::where('user_id', $t->id)->count());
    }

    public function test_live_mode_reports_errors_as_a_final_line(): void
    {
        $t = $this->faculty();
        $body = $this->actingAs($t)->post(route('faculty.schedule.store'), [
            'file' => UploadedFile::fake()->createWithContent('notes.txt', 'Remember to buy milk'),
        ], ['Accept' => 'application/x-ndjson'])->streamedContent();
        $last = json_decode(trim(last(explode("\n", trim($body)))), true);

        $this->assertFalse($last['done']);
        $this->assertSame(422, $last['status']);
        $this->assertStringContainsString("doesn't look like a weekly timetable", $last['error']);
    }

    public function test_index_returns_only_the_facultys_own_classes(): void
    {
        $t = $this->faculty();
        $other = User::factory()->create(['role' => 'faculty']);
        TeacherClass::create(['user_id' => $t->id, 'subject' => 'Mine', 'day' => 2, 'starts_at' => '09:30']);
        TeacherClass::create(['user_id' => $other->id, 'subject' => 'Theirs', 'day' => 2, 'starts_at' => '09:30']);

        $this->actingAs($t)->getJson(route('faculty.schedule.index'))->assertOk()
            ->assertJsonCount(1, 'classes')->assertJsonPath('classes.0.subject', 'Mine');
        $this->actingAs(User::factory()->create(['role' => 'student']))->getJson(route('faculty.schedule.index'))->assertForbidden();
    }

    private function visionWith(array $responses, array $models = ['model-a', 'model-b']): VisionCaller
    {
        config(['schedule.vision_models' => $models, 'schedule.hedge_after' => 0.05, 'nvidia_nim.api_key' => 'k']);

        return new VisionCaller(new Client(['handler' => HandlerStack::create(new MockHandler($responses))]));
    }

    private function nim(string $text): Response
    {
        return new Response(200, [], json_encode(['choices' => [['message' => ['content' => $text]]]]));
    }

    public function test_vision_caller_returns_the_first_good_answer_and_survives_one_failure(): void
    {
        $this->assertSame('Mon|09:30|Math|', $this->visionWith([$this->nim('Mon|09:30|Math|'), $this->nim('later')])->transcribe('p', 'data:image/png;base64,AA'));

        // attempt 1 comes back empty → the hedge answers
        $this->assertSame('Tue|10:00|Art|', $this->visionWith([$this->nim(''), $this->nim('Tue|10:00|Art|')])->transcribe('p', 'data:image/png;base64,AA'));
        // attempt 1 is a 503 → the hedge answers
        $this->assertSame('ok', $this->visionWith([new Response(503), $this->nim('ok')])->transcribe('p', 'data:image/png;base64,AA'));
    }

    public function test_vision_caller_throws_a_classified_error_when_both_attempts_fail(): void
    {
        try {
            $this->visionWith([new Response(503), new Response(503)])->transcribe('p', 'data:image/png;base64,AA');
            $this->fail('expected NvidiaNimException');
        } catch (NvidiaNimException $e) {
            $this->assertSame('NIM_MODEL_ERROR', $e->category);
            $this->assertSame(503, $e->status);
        }
    }

    public function test_faculty_can_add_edit_and_delete_a_class_and_it_repeats_from_its_start_date(): void
    {
        $t = $this->faculty();

        $res = $this->actingAs($t)->postJson(route('faculty.schedule.class.store'), [
            'subject' => 'Robotics Lab', 'class' => '8-A', 'day' => 2, 'start' => '09:30', 'end' => '10:30', 'room' => 'Lab 2', 'from' => '2026-09-20',
        ])->assertCreated()->assertJsonPath('class.subject', 'Robotics Lab')->assertJsonPath('class.from', '2026-09-20');
        $id = $res->json('class.id');

        $this->actingAs($t)->putJson(route('faculty.schedule.class.update', $id), [
            'subject' => 'Robotics', 'day' => 3, 'start' => '10:00', 'end' => '11:00',
        ])->assertOk()->assertJsonPath('class.day', 3)->assertJsonPath('class.start', '10:00')->assertJsonPath('class.class', null);

        $this->actingAs($t)->getJson(route('faculty.schedule.index'))->assertOk()->assertJsonCount(1, 'classes');

        $this->actingAs($t)->deleteJson(route('faculty.schedule.class.destroy', $id))->assertOk()->assertJsonCount(0, 'classes');
        $this->assertDatabaseCount('teacher_classes', 0);
    }

    public function test_class_input_is_validated_and_other_facultys_classes_are_off_limits(): void
    {
        $t = $this->faculty();
        $this->actingAs($t)->postJson(route('faculty.schedule.class.store'), ['subject' => '', 'day' => 9, 'start' => 'x'])->assertStatus(422);
        $this->actingAs($t)->postJson(route('faculty.schedule.class.store'), ['subject' => 'Math', 'day' => 1, 'start' => '10:00', 'end' => '09:00'])->assertStatus(422);

        $mine = TeacherClass::create(['user_id' => $t->id, 'subject' => 'Math', 'day' => 1, 'starts_at' => '08:00']);
        $other = User::factory()->create(['role' => 'faculty']);
        $this->actingAs($other)->putJson(route('faculty.schedule.class.update', $mine->id), ['subject' => 'Hacked', 'day' => 1, 'start' => '08:00'])->assertNotFound();
        $this->actingAs($other)->deleteJson(route('faculty.schedule.class.destroy', $mine->id))->assertNotFound();
        $this->assertSame('Math', $mine->fresh()->subject);

        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student)->postJson(route('faculty.schedule.class.store'), ['subject' => 'X', 'day' => 1, 'start' => '08:00'])->assertForbidden();
    }

    public function test_new_classes_default_to_the_browsers_today(): void
    {
        $t = $this->faculty();
        $today = now()->toDateString();
        $this->actingAs($t)->postJson(route('faculty.schedule.class.store'), ['subject' => 'Math', 'day' => 1, 'start' => '08:00'])
            ->assertCreated()->assertJsonPath('class.from', $today);
    }

    public function test_facultys_get_the_calendar_rail_item_and_drawer_on_every_page_students_do_not(): void
    {
        $t = $this->faculty();
        $crew = Crew::where('teacher_id', $t->id)->firstOrFail();
        TeacherClass::create(['user_id' => $t->id, 'subject' => 'Robotics Lab', 'day' => 2, 'starts_at' => '09:30']);

        foreach ([route('faculty.dashboard'), route('faculty.classes'), route('faculty.course', $crew), route('faculty.chat')] as $url) {
            $this->actingAs($t)->get($url)->assertOk()
                ->assertSee('aria-label="Calendar"', false)                      // the rail item
                ->assertSee('calendarDrawer', false)                             // the drawer
                ->assertSee(json_encode(route('faculty.schedule.index')), false);  // where it lazy-loads from (@json escapes slashes)
        }

        // The schedule card lives on the Classes page (own rail icon), not on the dashboard.
        $this->actingAs($t)->get(route('faculty.classes'))->assertOk()->assertSee('View Calendar')->assertSee('aria-label="Classes"', false);
        $this->actingAs($t)->get(route('faculty.dashboard'))->assertOk()->assertDontSee('Upload schedule')->assertDontSee('Robotics Lab');

        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student)->get(route('student.dashboard'))->assertOk()
            ->assertDontSee('calendarDrawer', false)->assertDontSee('aria-label="Calendar"', false);
    }

    public function test_classes_page_is_faculty_only_and_needs_no_crew(): void
    {
        $this->get(route('faculty.classes'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => 'student']))->get(route('faculty.classes'))->assertRedirect(route('student.dashboard'));

        // a faculty member who hasn't launched a crew yet can still keep a schedule
        $this->actingAs(User::factory()->create(['role' => 'faculty']))->get(route('faculty.classes'))->assertOk()->assertSee('Upload schedule');
    }
}
