<?php

use App\Http\Controllers\AstroController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CourseModuleController;
use App\Http\Controllers\CrewQuizController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfographicController;
use App\Http\Controllers\KeyTermsController;
use App\Http\Controllers\LessonHintController;
use App\Http\Controllers\LessonProgressController;
use App\Http\Controllers\LessonQuizController;
use App\Http\Controllers\PlanetController;
use App\Http\Controllers\PptController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudyActivityController;
use App\Http\Controllers\TeacherChatController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// chat
Route::get('/chat', [ChatController::class, 'index'])->name('student.chat');
// Student dashboard (progress, XP, crew leaderboard) and the planet picker
Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
Route::get('/planets', [StudentController::class, 'planets'])->name('student.planets');
Route::post('/student/activity', [StudyActivityController::class, 'ping'])->name('student.activity');
// Crew
Route::get('/student/crew', [StudentController::class, 'crew'])->name('student.crew');
Route::post('/student/crew/join', [StudentController::class, 'joinCrew'])->name('student.crew.join');

// Teacher side: dashboard + crew creation (both were dropped in 807f262 while the
// controllers, view and every route('teacher.dashboard') call site were left in place).
Route::get('/teacher/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
Route::get('/teacher/classes', [TeacherController::class, 'classes'])->name('teacher.classes');
Route::get('/teacher/chat', [TeacherChatController::class, 'index'])->name('teacher.chat');
Route::post('/teacher/chat/message', [TeacherChatController::class, 'send'])->name('teacher.chat.message');
Route::post('/teacher/chat/ppt', [PptController::class, 'generate'])->middleware('throttle:10,1')->name('teacher.chat.ppt');
Route::get('/teacher/schedule', [TeacherScheduleController::class, 'index'])->name('teacher.schedule.index');
Route::post('/teacher/schedule', [TeacherScheduleController::class, 'store'])->middleware('throttle:8,1')->name('teacher.schedule.store');
Route::delete('/teacher/schedule', [TeacherScheduleController::class, 'destroy'])->name('teacher.schedule.destroy');
Route::post('/teacher/schedule/classes', [TeacherScheduleController::class, 'storeClass'])->name('teacher.schedule.class.store');
Route::put('/teacher/schedule/classes/{class}', [TeacherScheduleController::class, 'updateClass'])->name('teacher.schedule.class.update');
Route::delete('/teacher/schedule/classes/{class}', [TeacherScheduleController::class, 'destroyClass'])->name('teacher.schedule.class.destroy');
Route::get('/teacher/crew', [TeacherController::class, 'crew'])->name('teacher.crew');
Route::post('/teacher/crew', [TeacherController::class, 'createCrew'])->name('teacher.crew.create');

// Learning modules: teacher-authored content (module + uploaded materials)
Route::post('/teacher/modules', [CourseModuleController::class, 'store'])->name('teacher.modules.store');
Route::put('/teacher/modules/{module}', [CourseModuleController::class, 'update'])->name('teacher.modules.update');
Route::delete('/teacher/modules/{module}', [CourseModuleController::class, 'destroy'])->name('teacher.modules.destroy');
Route::post('/teacher/modules/{module}/materials', [CourseModuleController::class, 'storeMaterials'])->name('teacher.materials.store');
Route::delete('/teacher/materials/{material}', [CourseModuleController::class, 'destroyMaterial'])->name('teacher.materials.destroy');
// Crew quizzes: teacher authors them, students in the crew take them
Route::post('/teacher/quizzes', [CrewQuizController::class, 'store'])->name('teacher.quizzes.store');
Route::delete('/teacher/quizzes/{quiz}', [CrewQuizController::class, 'destroy'])->name('teacher.quizzes.destroy');
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
Route::get('/student/planet/{slug}', [PlanetController::class, 'courses'])->name('student.planet');

// Step 2: the chosen course itself. Declared before the {module}/{lesson} route
// below, which would otherwise swallow "course/python" as a module and lesson.
Route::get('/student/planet/{slug}/course/{course}', [PlanetController::class, 'show'])->name('student.planet.play');

// New route for module/lesson format: /student/planet/{slug}/{module}/{lesson}
// Both are gated: a Programming lesson stays locked until the previous lesson's
// coding challenge is completed (see EnsureLessonUnlocked).
Route::get('/student/planet/{slug}/{module}/{lesson}', [PlanetController::class, 'viewModuleLesson'])
    ->middleware('lesson.unlocked')
    ->name('student.planet.module.lesson');

Route::get('/student/planet/{slug}/{module}/{lesson}/fragment', [PlanetController::class, 'lessonFragment'])
    ->middleware('lesson.unlocked')
    ->name('student.planet.module.lesson.fragment');

// Editor reports a finished challenge; the server verifies it against the answer key.
Route::post('/student/planet/{slug}/lesson/{module}/{lesson}/complete', [LessonProgressController::class, 'complete'])
    ->name('student.planet.lesson.complete');

// A networking lesson's simulator lab reports a pass (the lab id must match the lesson's blueprint entry).
Route::post('/student/planet/{slug}/lab/{module}/{lesson}/complete', [LessonProgressController::class, 'completeLab'])
    ->middleware('throttle:30,1')
    ->name('student.planet.lab.complete');

// The full-page simulator for a lesson's lab (the lesson's "Configure it yourself" button opens it).
Route::get('/student/planet/{slug}/lab/{module}/{lesson}', [\App\Http\Controllers\LessonLabController::class, 'show'])
    ->middleware('lesson.unlocked')
    ->name('student.planet.lab');

// Interactive lesson quiz: restore this student's answers, and check + score one answer (first attempt earns XP).
Route::get('/student/planet/{slug}/quiz/{module}/{lesson}', [LessonQuizController::class, 'status'])->name('student.planet.quiz.status');
Route::post('/student/planet/{slug}/quiz/{module}/{lesson}', [LessonQuizController::class, 'answer'])->middleware('throttle:60,1')->name('student.planet.quiz.answer');

// Astro's hint in the editor is bought with XP: status (price / balance / hint if already bought) and buy.
Route::get('/student/planet/{slug}/hint/{module}/{lesson}', [LessonHintController::class, 'status'])->name('student.planet.hint.status');
Route::post('/student/planet/{slug}/hint/{module}/{lesson}', [LessonHintController::class, 'buy'])->middleware('throttle:30,1')->name('student.planet.hint.buy');

// Learning-plan generation for a track. Placeholder payload until the
// Nemotron roadmap generator lands — same response contract, instant reply.
Route::post('/student/planet/{slug}/plan', [PlanetController::class, 'generatePlan'])->name('student.planet.plan');

// Course-player alias for `/planets/{slug}` — keeps existing onboarding links
// working while exposing the course player at the URL referenced in the spec.
Route::get('/planets/{slug}', [PlanetController::class, 'courses'])->name('student.planet.course');

// Python interactive editor page.
// NOTE: {lessonId} was dropped from this route on purpose — the "Code it
// yourself" button links to /student/planet/{slug}/editor exactly, with no
// trailing segment. Which lesson/exercise to preload (if any) now comes from
// optional query params instead: ?module=m1&lesson=01
// e.g. http://127.0.0.1:8000/student/planet/programming/editor?module=m1&lesson=01
Route::get('/student/planet/{slug}/editor', [PlanetController::class, 'editor'])->name('student.planet.editor');

use App\Http\Controllers\DeckController;

// Deck API — slides served as JSON for the Bolt Slides engine.
Route::get('/api/decks/{id}.json', [DeckController::class, 'show'])->name('decks.show');
Route::post('/api/decks', [DeckController::class, 'store'])->name('decks.store');

// Standalone Bolt Slides app (Vite + React build) — no Blade layout.
Route::get('/decks/{id}', [DeckController::class, 'showPage'])->name('decks.page');

// Generate a slide deck from a conversation (Studio PPT action).
Route::post('/conversations/{conversation}/generate-deck', [ChatController::class, 'generateDeck'])->name('conversations.generate-deck');

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
    ->name('student.planet.editor.launch');
