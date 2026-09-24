<?php

namespace Tests\Feature;

use App\Models\CourseModule;
use App\Models\Crew;
use App\Models\ModuleMaterial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseModulesTest extends TestCase
{
    use RefreshDatabase;

    private function setUpCrew(): array
    {
        Storage::fake('local');
        $faculty = User::factory()->create(['role' => 'faculty']);
        $crew = Crew::create(['name' => 'Alpha', 'code' => 'ABC123', 'teacher_id' => $faculty->id]);
        $faculty->update(['crew_id' => $crew->id]);
        $crew->roster()->attach($faculty->id, ['role' => 'faculty']);
        $student = User::factory()->create(['role' => 'student', 'crew_id' => $crew->id]);
        $crew->roster()->attach($student->id, ['role' => 'student']);

        return [$faculty, $student, $crew];
    }

    public function test_faculty_creates_a_module_and_uploads_materials(): void
    {
        [$faculty, , $crew] = $this->setUpCrew();

        $this->actingAs($faculty)->post(route('faculty.modules.store', $crew), ['title' => 'Intro', 'description' => 'Basics'])
            ->assertRedirect();
        $module = $crew->modules()->firstOrFail();

        $this->actingAs($faculty)->post(route('faculty.materials.store', $module), [
            'files' => [
                UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'),
                UploadedFile::fake()->create('lecture.pptx', 100),
                UploadedFile::fake()->create('worksheet.docx', 100),
            ],
        ])->assertSessionHasNoErrors();

        $this->assertSame(3, $module->materials()->count());
        foreach ($module->materials as $m) {
            Storage::disk('local')->assertExists($m->path);
        }
    }

    public function test_dangerous_file_types_are_rejected(): void
    {
        [$faculty, , $crew] = $this->setUpCrew();
        $module = $crew->modules()->create(['title' => 'Intro']);

        $this->actingAs($faculty)->post(route('faculty.materials.store', $module), [
            'files' => [UploadedFile::fake()->create('shell.php', 1)],
        ])->assertSessionHasErrors();

        $this->assertSame(0, $module->materials()->count());
    }

    public function test_student_cannot_manage_modules(): void
    {
        [, $student, $crew] = $this->setUpCrew();

        $this->actingAs($student)->post(route('faculty.modules.store', $crew), ['title' => 'X'])->assertForbidden();
        $module = $crew->modules()->create(['title' => 'Intro']);
        $this->actingAs($student)->delete(route('faculty.modules.destroy', $module))->assertForbidden();
    }

    public function test_another_faculty_cannot_touch_this_crews_module(): void
    {
        [, , $crew] = $this->setUpCrew();
        $module = $crew->modules()->create(['title' => 'Intro']);
        $other = User::factory()->create(['role' => 'faculty']);
        Crew::create(['name' => 'Beta', 'code' => 'ZZZ999', 'teacher_id' => $other->id]);

        $this->actingAs($other)->delete(route('faculty.modules.destroy', $module))->assertForbidden();
        $this->assertNotNull(CourseModule::find($module->id));
    }

    public function test_student_sees_real_modules_and_downloads_materials(): void
    {
        [, $student, $crew] = $this->setUpCrew();
        $module = $crew->modules()->create(['title' => 'Real Module Title', 'description' => 'Real desc']);
        Storage::disk('local')->put('course-materials/x/notes.pdf', 'PDFDATA');
        $file = $module->materials()->create(['name' => 'notes.pdf', 'ext' => 'pdf', 'path' => 'course-materials/x/notes.pdf', 'size' => 7]);

        $this->actingAs($student)->get(route('student.crew'))
            ->assertOk()
            ->assertSee('Real Module Title')
            ->assertSee('notes.pdf')
            ->assertDontSee('Programming Fundamentals.pdf');

        $this->actingAs($student)->get(route('materials.download', $file))->assertOk();
    }

    public function test_student_with_no_modules_sees_empty_state(): void
    {
        [, $student] = $this->setUpCrew();

        $this->actingAs($student)->get(route('student.crew'))->assertOk()->assertSee('No modules yet');
    }

    public function test_outsiders_cannot_download_materials(): void
    {
        [, , $crew] = $this->setUpCrew();
        $module = $crew->modules()->create(['title' => 'Intro']);
        Storage::disk('local')->put('course-materials/x/a.pdf', 'x');
        $file = $module->materials()->create(['name' => 'a.pdf', 'ext' => 'pdf', 'path' => 'course-materials/x/a.pdf', 'size' => 1]);
        $outsider = User::factory()->create(['role' => 'student']);

        $this->actingAs($outsider)->get(route('materials.download', $file))->assertForbidden();
    }

    public function test_deleting_a_module_removes_its_files(): void
    {
        [$faculty, , $crew] = $this->setUpCrew();
        $module = $crew->modules()->create(['title' => 'Intro']);
        $this->actingAs($faculty)->post(route('faculty.materials.store', $module), [
            'files' => [UploadedFile::fake()->create('a.pdf', 10)],
        ]);
        $path = $module->materials()->first()->path;

        $this->actingAs($faculty)->delete(route('faculty.modules.destroy', $module))->assertRedirect();

        Storage::disk('local')->assertMissing($path);
        $this->assertSame(0, ModuleMaterial::count());
    }

    public function test_faculty_crew_page_lists_modules(): void
    {
        [$faculty, , $crew] = $this->setUpCrew();
        $crew->modules()->create(['title' => 'Listed Module']);

        $this->actingAs($faculty)->get(route('faculty.course', $crew))->assertOk()->assertSee('Listed Module');
    }

    public function test_faculty_sidebar_has_dashboard_and_chat_but_no_planets_or_crew(): void
    {
        [$faculty] = $this->setUpCrew();

        $this->actingAs($faculty)->get(route('faculty.dashboard'))
            ->assertOk()
            ->assertSee('aria-label="Dashboard"', false)
            ->assertSee('aria-label="Chat"', false)
            ->assertDontSee('aria-label="Crew"', false)
            ->assertDontSee('aria-label="Planets"', false)
            ->assertSee('Captain');
    }

    public function test_faculty_chat_is_its_own_route_without_student_features(): void
    {
        [$faculty] = $this->setUpCrew();

        $this->assertSame('/faculty/chat', route('faculty.chat', absolute: false));
        $this->actingAs($faculty)->get(route('student.chat'))->assertRedirect(route('faculty.chat'));
        $this->actingAs($faculty)->get(route('faculty.chat'))
            ->assertOk()
            ->assertSee('Hi Captain')
            ->assertSee('Web sources')
            ->assertSee('Saved outputs')
            ->assertSee('Add note')
            ->assertDontSee('Add from Planets')
            ->assertDontSee('>Slide deck</span>', false)
            ->assertDontSee('>Flashcards</span>', false)
            ->assertDontSee('>Infographic</span>', false)
            ->assertDontSee('>Reports</span>', false);
        $this->actingAs($faculty)->get(route('student.chat'))->assertRedirect();
    }

    public function test_students_cannot_use_the_faculty_chat(): void
    {
        [, $student] = $this->setUpCrew();

        $this->actingAs($student)->get(route('faculty.chat'))->assertRedirect(route('student.chat'));
        $this->actingAs($student)->postJson(route('faculty.chat.message'), ['message' => 'hi'])->assertForbidden();
    }
}
