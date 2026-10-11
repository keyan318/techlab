<?php

namespace Tests\Feature;

use App\Http\Controllers\CourseHubController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The Materials tab and its red "new material" badge.
 */
class CourseMaterialsBadgeTest extends TestCase
{
    use RefreshDatabase;

    private function setUp2(): array
    {
        Storage::fake('local');
        $student = $this->studentIn('programming', 'python');
        $crew = $this->crewFor('programming', 'python');

        return [$student, $crew];
    }

    public function test_the_badge_counts_new_materials_and_clears_when_the_tab_is_opened(): void
    {
        [$student, $crew] = $this->setUp2();
        $module = $crew->modules()->create(['title' => 'Intro']);
        $module->materials()->create([
            'name' => 'notes.pdf', 'ext' => 'pdf', 'path' => 'course-materials/x/notes.pdf', 'size' => 7,
        ]);
        $module->materials()->create([
            'name' => 'slides.pptx', 'ext' => 'pptx', 'path' => 'course-materials/x/slides.pptx', 'size' => 7,
        ]);

        $this->assertSame(2, CourseHubController::unseenMaterialCount($crew, $student->id));

        // The badge shows on the Home tab before the Materials tab is opened.
        $this->actingAs($student)->get(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()->assertSee('2 new materials');

        // Opening the Materials tab is what marks them seen.
        $this->actingAs($student)->get(route('student.course.materials', ['slug' => 'programming', 'course' => 'python']))->assertOk();
        $this->assertSame(0, CourseHubController::unseenMaterialCount($crew, $student->id));

        // The badge no longer shows once cleared.
        $this->actingAs($student)->get(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()->assertDontSee('new materials');
    }

    public function test_the_faculty_upload_endpoint_also_counts_towards_the_badge(): void
    {
        [$student, $crew] = $this->setUp2();
        $module = $crew->modules()->create(['title' => 'Intro']);

        $this->actingAs($crew->faculty)->post(route('faculty.materials.store', $module), [
            'files' => [UploadedFile::fake()->create('handout.pdf', 10)],
        ])->assertSessionHasNoErrors();

        $this->assertSame(1, CourseHubController::unseenMaterialCount($crew, $student->id));
    }
}
