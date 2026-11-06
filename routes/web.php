<?php

use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AstroController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CourseCertificateController;
use App\Http\Controllers\CourseEnrollmentController;
use App\Http\Controllers\CourseHubController;
use App\Http\Controllers\CourseJoinRequestController;
use App\Http\Controllers\CourseModuleController;
use App\Http\Controllers\CrewQuizBuilderController;
use App\Http\Controllers\CrewQuizController;
use App\Http\Controllers\ExamsController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\FacultyCourseController;
use App\Http\Controllers\FacultyScheduleController;
use App\Http\Controllers\FinalExamController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfographicController;
use App\Http\Controllers\KeyTermsController;
use App\Http\Controllers\LessonCheckpointController;
use App\Http\Controllers\LessonHintController;
use App\Http\Controllers\LessonLabController;
use App\Http\Controllers\LessonLabHelpController;
use App\Http\Controllers\LessonProgressController;
use App\Http\Controllers\LessonQuizController;
use App\Http\Controllers\LessonQuizEditorController;
use App\Http\Controllers\MiniBossController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PlanetController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudioOutputController;
use App\Http\Controllers\StudyActivityController;
use App\Http\Controllers\TermsController;
use App\Http\Controllers\TourController;
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
Route::get('/admin/self-paced', [AdminController::class, 'selfPaced'])->middleware('role:admin')->name('admin.self-paced');
Route::get('/admin/classes', [AdminController::class, 'classes'])->middleware('role:admin')->name('admin.classes');
Route::delete('/admin/courses/assignments/{assignment}', [AdminController::class, 'unassign'])->middleware('role:admin')->name('admin.courses.unassign');
// The admin's own captain look (same builder and choices as faculty).
Route::get('/admin/avatar', [OnboardingController::class, 'adminEdit'])->middleware('role:admin')->name('admin.avatar');
Route::post('/admin/avatar', [OnboardingController::class, 'adminSave'])->middleware(['role:admin', 'throttle:20,1'])->name('admin.avatar.save');
// Settings → Admins: only the first admin adds (with their credentials) or removes other admins.
Route::post('/admin/admins', [AdminAccountController::class, 'store'])->middleware(['role:admin', 'throttle:10,1'])->name('admin.admins.store');
Route::delete('/admin/admins/{admin}', [AdminAccountController::class, 'destroy'])->middleware('role:admin')->name('admin.admins.destroy');
Route::get('/admin/courses/{crew}/roster', [AdminController::class, 'crewRoster'])->middleware('role:admin')->name('admin.courses.roster');
// Forgot password (students and faculty): email a temporary password, then choose a new one.
Route::get('/forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendTemporary'])->middleware(['throttle:5,1', 'throttle:password-reset'])->name('password.email');
Route::get('/password/new', [PasswordResetController::class, 'showNew'])->name('password.new');
Route::post('/password/new', [PasswordResetController::class, 'saveNew'])->middleware('throttle:10,1')->name('password.new.save');
// Terms and Conditions: anyone can read them; students and faculty agree here before onboarding (EnsureTermsAccepted).
Route::get('/terms', [TermsController::class, 'show'])->name('terms');
Route::post('/terms', [TermsController::class, 'accept'])->middleware(['auth', 'throttle:20,1'])->name('terms.accept');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::delete('/account', [AuthController::class, 'destroyAccount'])->name('account.destroy');
// Settings (students, faculty and admins): name, password, look, sound/theme, delete account (admins: other admins instead).
Route::get('/settings', [SettingsController::class, 'show'])->middleware('role:student,faculty,admin')->name('settings');
Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->middleware(['role:student,faculty,admin', 'throttle:20,1'])->name('settings.profile');
Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->middleware(['role:student,faculty,admin', 'throttle:10,1'])->name('settings.password');

// chat
Route::get('/chat', [ChatController::class, 'index'])->name('student.chat');
// Student dashboard (progress, XP, crew leaderboard) and the planet picker
Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
// Onboarding right after sign-up (Astro's hello + build your astronaut), and changing your look later.
Route::get('/welcome', [OnboardingController::class, 'welcome'])->middleware('role:student')->name('student.welcome');
Route::get('/avatar', [OnboardingController::class, 'edit'])->middleware('role:student')->name('student.avatar');
Route::post('/avatar', [OnboardingController::class, 'save'])->middleware(['role:student', 'throttle:20,1'])->name('student.avatar.save');
// The Shop: frames, animals and suits for XP, and gems traded for XP.
Route::get('/shop', [ShopController::class, 'index'])->middleware('role:student')->name('student.shop');
Route::post('/shop/{kind}/{key}/buy', [ShopController::class, 'buy'])->middleware(['role:student', 'throttle:30,1'])->whereIn('kind', ['frame', 'animal', 'suit'])->name('student.shop.buy');
Route::post('/shop/equip', [ShopController::class, 'equip'])->middleware(['role:student', 'throttle:60,1'])->name('student.shop.equip');
Route::post('/shop/exchange', [ShopController::class, 'exchange'])->middleware(['role:student', 'throttle:20,1'])->name('student.shop.exchange');
Route::get('/codename/check', [OnboardingController::class, 'checkCodename'])->middleware(['role:student', 'throttle:60,1'])->name('student.codename.check');
Route::get('/progress', [StudentController::class, 'progress'])->name('student.progress');
Route::get('/me/stats', [StudentController::class, 'stats'])->middleware(['role:student', 'throttle:60,1'])->name('student.stats');
Route::get('/planets', [StudentController::class, 'planets'])->name('student.planets');
// Astro's tour: remember a chapter the student finished or skipped, so it doesn't play by itself again.
Route::post('/tour/seen', [TourController::class, 'seen'])->middleware(['role:student', 'throttle:30,1'])->name('student.tour.seen');
// The course hub: where a course card lands. Home (chapters), Materials (the faculty's
// uploads), Quiz and Crew share one header; the lessons are one click further in.
Route::middleware(['enrolled', 'course.member'])->group(function () {
    Route::get('/student/course/{slug}/{course}', [CourseHubController::class, 'home'])->name('student.course.overview');
    Route::get('/student/course/{slug}/{course}/materials', [CourseHubController::class, 'materials'])->name('student.course.materials');
    Route::get('/student/course/{slug}/{course}/quiz', [CourseHubController::class, 'quiz'])->name('student.course.quiz');
    Route::get('/student/course/{slug}/{course}/crew', [CourseHubController::class, 'crew'])->name('student.course.crew');
    // The certificate of completion, once every lesson of the course is done.
    Route::get('/student/course/{slug}/{course}/certificate', [CourseCertificateController::class, 'show'])->name('student.course.certificate');
    // The final exam ("Final Boss"): opens once every lesson is done; passing it earns the certificate.
    Route::get('/student/course/{slug}/{course}/final-exam', [FinalExamController::class, 'show'])->name('student.course.final-exam');
    // A module's mini boss: opens once the module's lessons are done (self-paced has to beat it to go on).
    Route::get('/student/course/{slug}/{course}/mini-boss/{module}', [MiniBossController::class, 'show'])->where('module', 'm\d+')->name('student.course.mini-boss');
});
Route::post('/student/activity', [StudyActivityController::class, 'ping'])->name('student.activity');
// The student's own list of courses they are on the roster of (each links into its hub).
Route::get('/student/courses', [StudentController::class, 'courses'])->name('student.courses');
Route::post('/student/crew/join', [StudentController::class, 'joinCrew'])->name('student.crew.join');
// Ask to join a code-gated course; the course's faculty member accepts (the code is emailed) or declines.
// Start a course Self-paced (or go back to it from a class): no faculty member, the default course.
Route::post('/student/courses/{planet}/{course}/self-paced', [CourseEnrollmentController::class, 'selfPaced'])->middleware(['role:student', 'throttle:10,1'])->name('student.course.self-paced');
Route::post('/student/courses/{crew}/join-request', [CourseJoinRequestController::class, 'store'])->middleware(['role:student', 'throttle:10,1'])->name('student.join-request.store');

// Old bookmarks: /teacher/... now lives at /faculty/...
Route::get('/teacher/{path?}', fn (?string $path = null) => redirect('/faculty'.($path ? '/'.$path : ''), 301))->where('path', '.*');

// Faculty side: dashboard + crew creation (both were dropped in 807f262 while the
// controllers, view and every route('faculty.dashboard') call site were left in place).
Route::get('/faculty/dashboard', [FacultyController::class, 'dashboard'])->name('faculty.dashboard');
// A new captain's onboarding (Astro's hello + build your captain), and changing that look later. No codename.
Route::get('/faculty/welcome', [OnboardingController::class, 'captainWelcome'])->middleware('role:faculty')->name('faculty.welcome');
Route::get('/faculty/avatar', [OnboardingController::class, 'captainEdit'])->middleware('role:faculty')->name('faculty.avatar');
// Astro's captain tour: remember a chapter the captain finished or skipped.
Route::post('/faculty/tour/seen', [TourController::class, 'seen'])->middleware(['role:faculty', 'throttle:30,1'])->name('faculty.tour.seen');
Route::post('/faculty/avatar', [OnboardingController::class, 'captainSave'])->middleware(['role:faculty', 'throttle:20,1'])->name('faculty.avatar.save');
Route::get('/faculty/classes', [FacultyController::class, 'classes'])->name('faculty.classes');
Route::get('/faculty/schedule', [FacultyScheduleController::class, 'index'])->name('faculty.schedule.index');
Route::post('/faculty/schedule', [FacultyScheduleController::class, 'store'])->middleware('throttle:8,1')->name('faculty.schedule.store');
Route::delete('/faculty/schedule', [FacultyScheduleController::class, 'destroy'])->name('faculty.schedule.destroy');
Route::post('/faculty/schedule/classes', [FacultyScheduleController::class, 'storeClass'])->name('faculty.schedule.class.store');
Route::put('/faculty/schedule/classes/{class}', [FacultyScheduleController::class, 'updateClass'])->name('faculty.schedule.class.update');
Route::delete('/faculty/schedule/classes/{class}', [FacultyScheduleController::class, 'destroyClass'])->name('faculty.schedule.class.destroy');
Route::post('/faculty/assignments/{assignment}/code', [FacultyController::class, 'generateCode'])->name('faculty.assignments.generate-code');
// Must come before /faculty/courses/{crew} below, or "create" would be swallowed as a crew id.
Route::get('/faculty/courses/create', [FacultyCourseController::class, 'create'])->middleware('role:faculty')->name('faculty.courses.create');
Route::get('/faculty/courses/{crew}', [FacultyController::class, 'course'])->name('faculty.course');
Route::get('/faculty/courses/{crew}/roster', [FacultyController::class, 'roster'])->name('faculty.course.roster');
// Sidebar shortcuts: always resolve to the faculty's one active crew server-side.
Route::get('/faculty/crew', [FacultyController::class, 'crewShortcut'])->name('faculty.crew');
Route::get('/faculty/materials', [FacultyController::class, 'materialsShortcut'])->name('faculty.materials');
Route::post('/faculty/signature', [FacultyController::class, 'saveSignature'])->middleware(['role:faculty', 'throttle:10,1'])->name('faculty.signature.save');
Route::delete('/faculty/signature', [FacultyController::class, 'deleteSignature'])->middleware('role:faculty')->name('faculty.signature.delete');
Route::post('/faculty/join-requests/{joinRequest}/accept', [CourseJoinRequestController::class, 'accept'])->middleware('role:faculty')->name('faculty.join-requests.accept');
Route::post('/faculty/join-requests/{joinRequest}/decline', [CourseJoinRequestController::class, 'decline'])->middleware('role:faculty')->name('faculty.join-requests.decline');

// Learning modules: faculty-authored content (module + uploaded materials)
Route::post('/faculty/courses/{crew}/modules', [CourseModuleController::class, 'store'])->name('faculty.modules.store');
Route::put('/faculty/modules/{module}', [CourseModuleController::class, 'update'])->name('faculty.modules.update');
Route::delete('/faculty/modules/{module}', [CourseModuleController::class, 'destroy'])->name('faculty.modules.destroy');
Route::post('/faculty/modules/{module}/materials', [CourseModuleController::class, 'storeMaterials'])->name('faculty.materials.store');
Route::post('/faculty/modules/{module}/links', [CourseModuleController::class, 'storeLink'])->name('faculty.materials.link');
Route::delete('/faculty/materials/{material}', [CourseModuleController::class, 'destroyMaterial'])->name('faculty.materials.destroy');
// Crew quizzes: faculty authors them, students in the crew take them
Route::post('/faculty/courses/{crew}/quizzes', [CrewQuizController::class, 'store'])->name('faculty.quizzes.store');
Route::put('/faculty/quizzes/{quiz}', [CrewQuizController::class, 'update'])->name('faculty.quizzes.update');
// The builder: a Google Forms-style editor, with the course summary and the faculty's own material beside it.
Route::get('/faculty/courses/{crew}/quizzes/new', [CrewQuizBuilderController::class, 'create'])->name('faculty.quizzes.build');
Route::get('/faculty/quizzes/{quiz}/edit', [CrewQuizBuilderController::class, 'edit'])->name('faculty.quizzes.edit');
Route::get('/faculty/courses/{crew}/summary.pdf', [CrewQuizBuilderController::class, 'summaryPdf'])->name('faculty.course.summary-pdf');
Route::delete('/faculty/quizzes/{quiz}', [CrewQuizController::class, 'destroy'])->name('faculty.quizzes.destroy');
// Lesson Knowledge Checks: every lesson has a default quiz; the course's faculty member may rewrite or reset it.
Route::get('/faculty/courses/{crew}/lesson-quizzes', [LessonQuizEditorController::class, 'index'])->name('faculty.lesson-quizzes.index');
Route::get('/faculty/courses/{crew}/lesson-quizzes/{module}/{lesson}', [LessonQuizEditorController::class, 'edit'])->name('faculty.lesson-quizzes.edit');
Route::put('/faculty/courses/{crew}/lesson-quizzes/{module}/{lesson}', [LessonQuizEditorController::class, 'update'])->middleware('throttle:30,1')->name('faculty.lesson-quizzes.update');
Route::delete('/faculty/courses/{crew}/lesson-quizzes/{module}/{lesson}', [LessonQuizEditorController::class, 'destroy'])->name('faculty.lesson-quizzes.destroy');
Route::get('/faculty/courses/{crew}/lesson-quizzes/{module}/{lesson}/data', [LessonQuizEditorController::class, 'data'])->name('faculty.lesson-quizzes.data');
// Course editing: the faculty member edits their course inside the course player (lesson pages, images,
// the module/lesson outline) and its card on the Courses page. {course} is the course-structure key.
Route::middleware('role:faculty')->prefix('/faculty/course-editor/{course}')->name('faculty.courses.')->group(function () {
    Route::post('/card', [FacultyCourseController::class, 'updateCard'])->middleware('throttle:20,1')->name('card');
    Route::get('/outline', [FacultyCourseController::class, 'outline'])->name('outline');
    Route::put('/outline', [FacultyCourseController::class, 'updateOutline'])->middleware('throttle:30,1')->name('outline.update');
    Route::delete('/outline', [FacultyCourseController::class, 'resetOutline'])->name('outline.reset');
    Route::post('/images', [FacultyCourseController::class, 'uploadImage'])->middleware('throttle:30,1')->name('images');
    Route::get('/lessons/{module}/{lesson}', [FacultyCourseController::class, 'content'])->name('content');
    Route::put('/lessons/{module}/{lesson}', [FacultyCourseController::class, 'updateContent'])->middleware('throttle:60,1')->name('content.update');
    Route::delete('/lessons/{module}/{lesson}', [FacultyCourseController::class, 'resetContent'])->name('content.reset');
    Route::get('/lessons/{module}/{lesson}/lab', [FacultyCourseController::class, 'lab'])->name('lab');
    Route::put('/lessons/{module}/{lesson}/lab', [FacultyCourseController::class, 'updateLab'])->middleware('throttle:60,1')->name('lab.update');
    Route::delete('/lessons/{module}/{lesson}/lab', [FacultyCourseController::class, 'resetLab'])->name('lab.reset');
    Route::get('/lessons/{module}/{lesson}/sim-lab', [FacultyCourseController::class, 'simLab'])->name('sim-lab');
    Route::put('/lessons/{module}/{lesson}/sim-lab', [FacultyCourseController::class, 'updateSimLab'])->middleware('throttle:60,1')->name('sim-lab.update');
    Route::delete('/lessons/{module}/{lesson}/sim-lab', [FacultyCourseController::class, 'resetSimLab'])->name('sim-lab.reset');
    // Play a draft of the lab in the simulator before saving it (a draft that changes the checks must be passed).
    Route::post('/lessons/{module}/{lesson}/sim-lab/test', [FacultyCourseController::class, 'testSimLab'])->middleware('throttle:60,1')->name('sim-lab.test');
    Route::get('/sim-lab-draft/{token}.json', [FacultyCourseController::class, 'draftSimLab'])->where('token', '[A-Za-z0-9]{40}')->name('sim-lab.draft');
    Route::post('/sim-lab-draft/{token}/passed', [FacultyCourseController::class, 'draftPassed'])->where('token', '[A-Za-z0-9]{40}')->middleware('throttle:60,1')->name('sim-lab.draft.passed');
    // A custom lesson (no lab of its own yet) picks a default lab to start from.
    Route::get('/sim-templates', [FacultyCourseController::class, 'simTemplates'])->name('sim-templates');
    Route::put('/lessons/{module}/{lesson}/sim-lab/template', [FacultyCourseController::class, 'attachSimTemplate'])->middleware('throttle:30,1')->name('sim-lab.template');
});
Route::get('/faculty/courses', [FacultyCourseController::class, 'index'])->middleware('role:faculty')->name('faculty.courses.index');
Route::post('/faculty/courses', [FacultyCourseController::class, 'store'])->middleware(['role:faculty', 'throttle:10,1'])->name('faculty.courses.store');
Route::post('/faculty/course-editor/{course}/publish', [FacultyCourseController::class, 'publish'])->middleware(['role:faculty', 'throttle:10,1'])->name('faculty.courses.publish');
Route::get('/student/quizzes/{quiz}', [CrewQuizController::class, 'show'])->name('student.quiz.show');
Route::post('/student/quizzes/{quiz}/start', [CrewQuizController::class, 'start'])->middleware('throttle:10,1,crew-quiz-start')->name('student.quiz.start');
Route::put('/student/quizzes/{quiz}/answers', [CrewQuizController::class, 'saveAnswers'])->middleware('throttle:120,1,crew-quiz-answers')->name('student.quiz.answers');
Route::post('/student/quizzes/{quiz}/submit', [CrewQuizController::class, 'submit'])->name('student.quiz.submit');
// Final exams: started, reported on and submitted from the full-screen exam page; faculty can allow a retake
Route::post('/student/final-exams/{quiz}/start', [FinalExamController::class, 'start'])->middleware('throttle:10,1,final-exam-start')->name('student.final-exam.start');
Route::post('/student/final-exams/{quiz}/violation', [FinalExamController::class, 'violation'])->middleware('throttle:30,1,final-exam-violation')->name('student.final-exam.violation');
Route::post('/student/final-exams/{quiz}/submit', [FinalExamController::class, 'submit'])->middleware('throttle:10,1,final-exam-submit')->name('student.final-exam.submit');
Route::post('/faculty/final-exams/attempts/{attempt}/retake', [FinalExamController::class, 'retake'])->name('faculty.final-exam.retake');
// Mini bosses: the same full-screen rules, one per module; the faculty member edits their class's on the Mini bosses tab.
Route::post('/student/mini-bosses/{quiz}/start', [MiniBossController::class, 'start'])->middleware('throttle:10,1,mini-boss-start')->name('student.mini-boss.start');
Route::post('/student/mini-bosses/{quiz}/violation', [MiniBossController::class, 'violation'])->middleware('throttle:30,1,mini-boss-violation')->name('student.mini-boss.violation');
Route::post('/student/mini-bosses/{quiz}/submit', [MiniBossController::class, 'submit'])->middleware('throttle:10,1,mini-boss-submit')->name('student.mini-boss.submit');
Route::get('/faculty/courses/{crew}/exams', [ExamsController::class, 'index'])->name('faculty.exams.index');
Route::post('/faculty/mini-bosses/{quiz}/reset', [ExamsController::class, 'reset'])->name('faculty.mini-bosses.reset');
Route::get('/materials/{material}', [CourseModuleController::class, 'download'])->name('materials.download');
Route::get('/materials/{material}/preview', [CourseModuleController::class, 'preview'])->name('materials.preview');

Route::post('/chat/message', [ChatController::class, 'send'])->name('chat.message');
Route::get('/chat/xp', [ChatController::class, 'xp'])->name('chat.xp');
Route::get('/chat/conversations', [ChatController::class, 'conversations'])->name('chat.conversations');
Route::get('/chat/conversations/{conversation}', [ChatController::class, 'show'])->name('chat.conversation.show');
Route::post('/chat/conversations/{conversation}/title', [ChatController::class, 'title'])->middleware('throttle:20,1')->name('chat.conversation.title');
Route::post('/chat/conversations/{conversation}/pin', [ChatController::class, 'pin'])->name('chat.conversation.pin');
Route::delete('/chat/conversations/{conversation}', [ChatController::class, 'destroy'])->name('chat.conversation.destroy');
Route::post('/chat/analogy', [AstroController::class, 'drawAnalogy'])->middleware('ai.xp:analogy')->name('chat.analogy');
Route::post('/chat/infographic', [InfographicController::class, 'generate'])->middleware('ai.xp:infographic')->name('chat.infographic');
Route::post('/chat/quiz', [QuizController::class, 'generate'])->middleware('ai.xp:quiz')->name('chat.quiz');
Route::get('/chat/studio-outputs', [StudioOutputController::class, 'index'])->name('chat.studio.index');
Route::post('/chat/studio-outputs', [StudioOutputController::class, 'store'])->name('chat.studio.store');
Route::delete('/chat/studio-outputs/{uid}', [StudioOutputController::class, 'destroy'])->name('chat.studio.destroy');
Route::post('/chat/flashcards', [FlashcardController::class, 'generate'])->middleware('ai.xp:flashcards')->name('chat.flashcards');
Route::post('/chat/key-terms', [KeyTermsController::class, 'generate'])->middleware('ai.xp:keyterms')->name('chat.keyterms');
Route::post('/chat/report', [ReportController::class, 'generate'])->middleware('ai.xp:report')->name('chat.report');
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
// The lab file the simulator loads when the course's faculty member rewrote the lab (SimLabService).
Route::get('/student/planet/{slug}/lab/{module}/{lesson}/file.json', [LessonLabController::class, 'file'])
    ->middleware('enrolled')->middleware('course.member')
    ->name('student.planet.lab.file');

// Interactive lesson quiz: restore this student's answers, and check + score one answer (first attempt earns XP).
Route::get('/student/planet/{slug}/quiz/{module}/{lesson}', [LessonQuizController::class, 'status'])->name('student.planet.quiz.status')->middleware('enrolled')->middleware('course.member');
Route::post('/student/planet/{slug}/quiz/{module}/{lesson}', [LessonQuizController::class, 'answer'])->middleware('throttle:60,1')->name('student.planet.quiz.answer')->middleware('enrolled')->middleware('course.member');
// In-lesson checkpoints: each one unlocks the next stage of the lesson (the Knowledge Check waits for all of them).
Route::get('/student/planet/{slug}/checkpoint/{module}/{lesson}', [LessonCheckpointController::class, 'status'])->name('student.planet.checkpoint.status')->middleware('enrolled')->middleware('course.member');
Route::post('/student/planet/{slug}/checkpoint/{module}/{lesson}', [LessonCheckpointController::class, 'answer'])->middleware('throttle:30,1')->name('student.planet.checkpoint.answer')->middleware('enrolled')->middleware('course.member');
// Failed the quiz (more than 2 mistakes): review the mistakes with Astro, then retry the ones missed.
Route::post('/student/planet/{slug}/quiz/{module}/{lesson}/review', [LessonQuizController::class, 'review'])->middleware('throttle:6,1')->name('student.planet.quiz.review')->middleware('enrolled')->middleware('course.member');
Route::post('/student/planet/{slug}/quiz/{module}/{lesson}/retake', [LessonQuizController::class, 'retake'])->middleware('throttle:10,1')->name('student.planet.quiz.retake')->middleware('enrolled')->middleware('course.member');

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
