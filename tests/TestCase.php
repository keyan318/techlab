<?php

namespace Tests;

use App\Models\Crew;
use App\Models\QuizAnswer;
use App\Models\User;
use App\Services\CertificatePdfService;
use App\Services\LessonQuizService;
use App\Services\QuizGateService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    /**
     * Python is hidden from students (config/course-catalog.php) but its lessons are still
     * the fullest blueprint, so the suite keeps using it as its example course. Tests about
     * hiding it switch it back off — see HiddenCourseTest.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config(['course-catalog.programming.courses.python.hidden' => false]);

        if ($this->skipQuizGate) {
            $this->partialMock(QuizGateService::class, fn ($m) => $m->shouldReceive('allows')->andReturn(true));
        }
    }

    /**
     * The certificate is a PDF whose text is compressed, so tests that read what it says get
     * the HTML Dompdf would have drawn instead. The controller still answers as a PDF.
     */
    protected function certificateAsHtml(): void
    {
        $this->app->instance(CertificatePdfService::class, new class extends CertificatePdfService
        {
            public function render(array $data): string
            {
                return view('student.course.certificate-pdf', $data)->render();
            }
        });
    }

    /**
     * A lesson's lab and "Code it yourself" open only after its quiz is passed (QuizGateService,
     * tested in QuizGateTest). Test classes about finishing lessons and labs set this to true
     * so they don't have to answer every quiz first.
     */
    protected bool $skipQuizGate = false;

    /**
     * A course only opens once a faculty captain has claimed it and accepted a student
     * — see EnsureCourseMember and PlanetController::courses(). Most Feature tests are
     * about lesson content itself, not the join-request gate, so this is the shortcut
     * every one of them uses to get a student past that gate.
     */
    protected function crewFor(string $planet, string $courseSlug): Crew
    {
        return Crew::firstOrCreate(
            ['planet' => $planet, 'course_slug' => $courseSlug, 'type' => Crew::CLASS_TYPE],
            [
                'name' => $courseSlug,
                'code' => strtoupper(Str::random(6)),
                'teacher_id' => User::factory()->create(['role' => 'faculty'])->id,
            ],
        );
    }

    /** Puts an existing user on a course's roster, as if their join request had been accepted and the code redeemed. */
    protected function enroll(User $user, string $planet, string $courseSlug): User
    {
        $crew = $this->crewFor($planet, $courseSlug);

        $user->update(['crew_id' => $crew->id]);
        if (! $crew->roster()->where('user_id', $user->id)->exists()) {
            $crew->roster()->attach($user->id, ['role' => 'student']);
        }

        return $user;
    }

    /**
     * Answers a lesson's Knowledge Check, getting `$mistakes` questions wrong. A lesson's lab and
     * "Code it yourself" open only once its quiz is passed (QuizGateService), so tests about the
     * hands-on stage pass the quiz first.
     */
    protected function answerQuiz(User $user, string $course, string $module, string $lesson, int $mistakes = 0): void
    {
        $key = LessonQuizService::key((string) LessonQuizService::source($module, $lesson, $course));
        foreach ($key as $q => $k) {
            $choice = $mistakes-- > 0 ? ($k['correct'] === 'A' ? 'B' : 'A') : $k['correct'];
            QuizAnswer::firstOrCreate(
                ['user_id' => $user->id, 'course' => $course, 'module' => $module, 'lesson' => $lesson, 'question' => $q],
                ['choice' => $choice, 'is_correct' => $choice === $k['correct'], 'xp' => 0],
            );
        }
    }

    /** A student already enrolled in a course — the default (programming/python) covers most Feature tests. */
    protected function studentIn(string $planet = 'programming', string $courseSlug = 'python', array $attrs = []): User
    {
        return $this->enroll(User::factory()->create($attrs), $planet, $courseSlug);
    }
}
