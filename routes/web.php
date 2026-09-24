<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AstroController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CourseJoinRequestController;
use App\Http\Controllers\CourseModuleController;
use App\Http\Controllers\CourseOverviewController;
use App\Http\Controllers\CrewQuizController;
use App\Http\Controllers\FacultyChatController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\FacultyScheduleController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfographicController;
use App\Http\Controllers\KeyTermsController;
use App\Http\Controllers\LessonHintController;
use App\Http\Controllers\LessonLabController;
use App\Http\Controllers\LessonLabHelpController;
use App\Http\Controllers\LessonProgressController;
use App\Http\Controllers\LessonQuizController;
use App\Http\Controllers\PlanetController;
use App\Http\Controllers\PptController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudyActivityController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
// Faculty and admin have their own front doors. Faculty can sign up (school email only); admins are made with `php artisan admin:create`.
Route::get('/faculty', [HomeController::class, 'faculty'])->name('faculty.landing');
Route::get('/faculty/login', [AuthController::class, 'showFacultyLogin'])->name('faculty.login');
Route::post('/faculty/login', [AuthController::class, 'facultyLogin'])->middleware('throttle:5,1');
Route::get('/faculty/register', [AuthController::class, 'showFacultyRegister'])->name('faculty.register');
Route::post('/faculty/register', [AuthController::class, 'facultyRegister'])->middleware('throttle:5,1');
// Admin: throttled per IP (5/min) AND per submitted email (10/hour, see
// AppServiceProvider::boot()) so the account is protected even from a distributed
// attempt cycling source IPs. Before this ever runs in production, confirm the
// deploy .env sets SESSION_SECURE_COOKIE=true, SESSION_SAME_SITE=lax (or stricter),
// and APP_DEBUG=false.
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->middleware(['throttle:5,1', 'throttle:admin-login']);
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->middleware('role:admin')->name('admin.dashboard');
Route::get('/admin', [AdminController::class, 'index'])->middleware('role:admin')->name('admin.home');
Route::post('/admin/courses', [AdminController::class, 'assign'])->middleware('role:admin')->name('admin.courses.assign');
Route::get('/admin/courses/{crew}/roster', [AdminController::class, 'crewRoster'])->middleware('role:admin')->name('admin.courses.roster');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// chat
Route::get('/chat', [ChatController::class, 'index'])->name('student.chat');
// Student dashboard (progress, XP, crew leaderboard) and the planet picker
Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
Route::get('/progress', [StudentController::class, 'progress'])->name('student.progress');
Route::get('/planets', [StudentController::class, 'planets'])->name('student.planets');
Route::get('/student/course/{slug}/{course}', [CourseOverviewController::class, 'show'])->name('student.course.overview')->middleware('enrolled')->middleware('course.member');
Route::post('/student/activity', [StudyActivityController::class, 'ping'])->name('student.activity');
// Crew
Route::get('/student/crew', [StudentController::class, 'crew'])->name('student.crew');
Route::post('/student/crew/join', [StudentController::class, 'joinCrew'])->name('student.crew.join');
// Ask to join a code-gated course; the course's faculty member accepts (the code is emailed) or declines.
Route::post('/student/courses/{crew}/join-request', [CourseJoinRequestController::class, 'store'])->middleware(['role:student', 'throttle:10,1'])->name('student.join-request.store');

// Old bookmarks: /teacher/... now lives at /faculty/...
Route::get('/teacher/{path?}', fn (?string $path = null) => redirect('/faculty'.($path ? '/'.$path : ''), 301))->where('path', '.*');

// Faculty side: dashboard + crew creation (both were dropped in 807f262 while the
// controllers, view and every route('faculty.dashboard') call site were left in place).
Route::get('/faculty/dashboard', [FacultyController::class, 'dashboard'])->name('faculty.dashboard');
Route::get('/faculty/classes', [FacultyController::class, 'classes'])->name('faculty.classes');
Route::get('/faculty/chat', [FacultyChatController::class, 'index'])->name('faculty.chat');
Route::post('/faculty/chat/message', [FacultyChatController::class, 'send'])->name('faculty.chat.message');
Route::post('/faculty/chat/ppt', [PptController::class, 'generate'])->middleware('throttle:10,1')->name('faculty.chat.ppt');
Route::get('/faculty/schedule', [FacultyScheduleController::class, 'index'])->name('faculty.schedule.index');
Route::post('/faculty/schedule', [FacultyScheduleController::class, 'store'])->middleware('throttle:8,1')->name('faculty.schedule.store');
Route::delete('/faculty/schedule', [FacultyScheduleController::class, 'destroy'])->name('faculty.schedule.destroy');
Route::post('/faculty/schedule/classes', [FacultyScheduleController::class, 'storeClass'])->name('faculty.schedule.class.store');
Route::put('/faculty/schedule/classes/{class}', [FacultyScheduleController::class, 'updateClass'])->name('faculty.schedule.class.update');
Route::delete('/faculty/schedule/classes/{class}', [FacultyScheduleController::class, 'destroyClass'])->name('faculty.schedule.class.destroy');
Route::post('/faculty/assignments/{assignment}/code', [FacultyController::class, 'generateCode'])->name('faculty.assignments.generate-code');
Route::get('/faculty/courses/{crew}', [FacultyController::class, 'course'])->name('faculty.course');
Route::post('/faculty/join-requests/{joinRequest}/accept', [CourseJoinRequestController::class, 'accept'])->middleware('role:faculty')->name('faculty.join-requests.accept');
Route::post('/faculty/join-requests/{joinRequest}/decline', [CourseJoinRequestController::class, 'decline'])->middleware('role:faculty')->name('faculty.join-requests.decline');

// Learning modules: faculty-authored content (module + uploaded materials)
Route::post('/faculty/courses/{crew}/modules', [CourseModuleController::class, 'store'])->name('faculty.modules.store');
Route::put('/faculty/modules/{module}', [CourseModuleController::class, 'update'])->name('faculty.modules.update');
Route::delete('/faculty/modules/{module}', [CourseModuleController::class, 'destroy'])->name('faculty.modules.destroy');
Route::post('/faculty/modules/{module}/materials', [CourseModuleController::class, 'storeMaterials'])->name('faculty.materials.store');
Route::delete('/faculty/materials/{material}', [CourseModuleController::class, 'destroyMaterial'])->name('faculty.materials.destroy');
// Crew quizzes: faculty authors them, students in the crew take them
Route::post('/faculty/courses/{crew}/quizzes', [CrewQuizController::class, 'store'])->name('faculty.quizzes.store');
Route::delete('/faculty/quizzes/{quiz}', [CrewQuizController::class, 'destroy'])->name('faculty.quizzes.destroy');
Route::get('/student/quizzes/{quiz}', [CrewQuizController::class, 'show'])->name('student.quiz.show');
Route::post('/student/quizzes/{quiz}/submit', [CrewQuizController::class, 'submit'])->name('student.quiz.submit');
Route::get('/materials/{material}', [CourseModuleController::class, 'download'])->name('materials.download');

Route::post('/chat/message', [ChatController::class, 'send'])->name('chat.message');
Route::get('/chat/conversations', [ChatController::class, 'conversations'])->name('chat.conversations');
Route::get('/chat/conversations/{conversation}', [ChatController::class, 'show'])->name('chat.conversation.show');
Route::post('/chat/conversations/{conversation}/title', [ChatController::class, 'title'])->middleware('throttle:20,1')->name('chat.conversation.title');
Route::post('/chat/conversations/{conversation}/pin', [ChatController::class, 'pin'])->name('chat.conversation.pin');
Route::delete('/chat/conversations/{conversation}', [ChatController::class, 'destroy'])->name('chat.conversation.destroy');
Route::post('/chat/analogy', [AstroController::class, 'drawAnalogy'])->name('chat.analogy');
Route::post('/chat/infographic', [InfographicController::class, 'generate'])->name('chat.infographic');
Route::post('/chat/quiz', [QuizController::class, 'generate'])->name('chat.quiz');
Route::post('/chat/flashcards', [FlashcardController::class, 'generate'])->name('chat.flashcards');
Route::post('/chat/key-terms', [KeyTermsController::class, 'generate'])->name('chat.keyterms');
Route::post('/chat/report', [ReportController::class, 'generate'])->name('chat.report');
Route::get('/astro/ping', [AstroController::class, 'ping'])->name('astro.ping');

// Planet / course selection (the onboarding track choice).
// Step 1: pick a course on the planet (Python, and later Java, C++, ...).
Route::get('/student/planet/{slug}', [PlanetController::class, 'courses'])->name('student.planet')->middleware('enrolled');

// Step 2: the chosen course itself. Declared before the {module}/{lesson} route
// below, which would otherwise swallow "course/python" as a module and lesson.
Route::get('/student/planet/{slug}/course/{course}', [PlanetController::class, 'show'])->name('student.planet.play')->middleware('enrolled')->middleware('course.member');

// New route for module/lesson format: /student/planet/{slug}/{module}/{lesson}
// Both are gated: a Programming lesson stays locked until the previous lesson's
// coding challenge is completed (see EnsureLessonUnlocked).
Route::get('/student/planet/{slug}/{module}/{lesson}', [PlanetController::class, 'viewModuleLesson'])
    ->middleware('enrolled')->middleware('course.member')
    ->middleware('lesson.unlocked')
    ->name('student.planet.module.lesson');

Route::get('/student/planet/{slug}/{module}/{lesson}/fragment', [PlanetController::class, 'lessonFragment'])
    ->middleware('enrolled')->middleware('course.member')
    ->middleware('lesson.unlocked')
    ->name('student.planet.module.lesson.fragment');

// Editor reports a finished challenge; the server verifies it against the answer key.
Route::post('/student/planet/{slug}/lesson/{module}/{lesson}/complete', [LessonProgressController::class, 'complete'])
    ->middleware('enrolled')->middleware('course.member')
    ->name('student.planet.lesson.complete');

// A networking lesson's simulator lab reports a pass (the lab id must match the lesson's blueprint entry).
Route::post('/student/planet/{slug}/lab/{module}/{lesson}/complete', [LessonProgressController::class, 'completeLab'])
    ->middleware('enrolled')->middleware('course.member')
    ->middleware('throttle:30,1')
    ->name('student.planet.lab.complete');

// The full-page simulator for a lesson's lab (the lesson's "Configure it yourself" button opens it).
Route::get('/student/planet/{slug}/lab/{module}/{lesson}', [LessonLabController::class, 'show'])
    ->middleware('enrolled')->middleware('course.member')
    ->middleware('lesson.unlocked')
    ->name('student.planet.lab');

// Interactive lesson quiz: restore this student's answers, and check + score one answer (first attempt earns XP).
Route::get('/student/planet/{slug}/quiz/{module}/{lesson}', [LessonQuizController::class, 'status'])->name('student.planet.quiz.status')->middleware('enrolled')->middleware('course.member');
Route::post('/student/planet/{slug}/quiz/{module}/{lesson}', [LessonQuizController::class, 'answer'])->middleware('throttle:60,1')->name('student.planet.quiz.answer')->middleware('enrolled')->middleware('course.member');

// Astro's hint in the editor is bought with XP: status (price / balance / hint if already bought) and buy.
Route::get('/student/planet/{slug}/hint/{module}/{lesson}', [LessonHintController::class, 'status'])->name('student.planet.hint.status')->middleware('enrolled')->middleware('course.member');
Route::post('/student/planet/{slug}/hint/{module}/{lesson}', [LessonHintController::class, 'buy'])->middleware('throttle:30,1')->name('student.planet.hint.buy')->middleware('enrolled')->middleware('course.member');

// Astro's live help chat inside a Citadel Sim lab: status, unlock (XP, once per attempt), then unlimited ask().
Route::get('/student/planet/{slug}/lab-help/{module}/{lesson}', [LessonLabHelpController::class, 'status'])->name('student.planet.lab-help.status')->middleware('enrolled')->middleware('course.member');
Route::post('/student/planet/{slug}/lab-help/{module}/{lesson}/unlock', [LessonLabHelpController::class, 'unlock'])->middleware('throttle:30,1')->name('student.planet.lab-help.unlock')->middleware('enrolled')->middleware('course.member');
Route::post('/student/planet/{slug}/lab-help/{module}/{lesson}/ask', [LessonLabHelpController::class, 'ask'])->middleware('throttle:20,1')->name('student.planet.lab-help.ask')->middleware('enrolled')->middleware('course.member');

// Learning-plan generation for a track. Placeholder payload until the
// Nemotron roadmap generator lands — same response contract, instant reply.
Route::post('/student/planet/{slug}/plan', [PlanetController::class, 'generatePlan'])->name('student.planet.plan')->middleware('enrolled')->middleware('course.member');

// Course-player alias for `/planets/{slug}` — keeps existing onboarding links
// working while exposing the course player at the URL referenced in the spec.
Route::get('/planets/{slug}', [PlanetController::class, 'courses'])->name('student.planet.course')->middleware('enrolled');

// Python interactive editor page.
// NOTE: {lessonId} was dropped from this route on purpose — the "Code it
// yourself" button links to /student/planet/{slug}/editor exactly, with no
// trailing segment. Which lesson/exercise to preload (if any) now comes from
// optional query params instead: ?module=m1&lesson=01
// e.g. http://127.0.0.1:8000/student/planet/programming/editor?module=m1&lesson=01
Route::get('/student/planet/{slug}/editor', [PlanetController::class, 'editor'])->name('student.planet.editor')->middleware('enrolled')->middleware('course.member');

Route::get('/python/{module}/lesson{lesson}', function ($module, $lesson) {

    $file = resource_path(
        "views/planets/programming/python_course/{$module}/lesson-{$lesson}.blade.php"
    );

    if (! file_exists($file)) {
        abort(404);
    }

    return view()->file($file);
});

// ADD this POST route directly below it:
Route::post('/student/planet/{slug}/editor/launch', [PlanetController::class, 'launchEditor'])
    ->middleware('enrolled')->middleware('course.member')
    ->name('student.planet.editor.launch');
