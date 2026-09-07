<?php

use App\Http\Controllers\AstroController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InfographicController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlanetController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
Route::get('/chat', [ChatController::class, 'index'])->name('student.chat');
Route::post('/chat/message', [ChatController::class, 'send'])->name('chat.message');
Route::get('/chat/conversations', [ChatController::class, 'conversations'])->name('chat.conversations');
Route::get('/chat/conversations/{conversation}', [ChatController::class, 'show'])->name('chat.conversation.show');
Route::delete('/chat/conversations/{conversation}', [ChatController::class, 'destroy'])->name('chat.conversation.destroy');
Route::post('/chat/analogy', [AstroController::class, 'drawAnalogy'])->name('chat.analogy');
Route::post('/chat/infographic', [InfographicController::class, 'generate'])->name('chat.infographic');
Route::post('/chat/quiz', [QuizController::class, 'generate'])->name('chat.quiz');
Route::get('/astro/ping', [AstroController::class, 'ping'])->name('astro.ping');
Route::get('/student/crew', [StudentController::class, 'crew'])->name('student.crew');
Route::post('/student/crew/join', [StudentController::class, 'joinCrew'])->name('student.crew.join');

// Planet / course selection (the onboarding track choice).
Route::get('/student/planet/{slug}', [PlanetController::class, 'show'])->name('student.planet');

// New route for module/lesson format: /student/planet/{slug}/{module}/{lesson}
Route::get('/student/planet/{slug}/{module}/{lesson}', [PlanetController::class, 'viewModuleLesson'])->name('student.planet.module.lesson');

Route::get('/student/planet/{slug}/{module}/{lesson}/fragment', [PlanetController::class, 'lessonFragment'])
    ->name('student.planet.module.lesson.fragment');

// Learning-plan generation for a track. Placeholder payload until the
// Nemotron roadmap generator lands — same response contract, instant reply.
Route::post('/student/planet/{slug}/plan', [PlanetController::class, 'generatePlan'])->name('student.planet.plan');

// View individual lesson
Route::get('/student/planet/{slug}/view/{lessonId}', [PlanetController::class, 'viewLesson'])->name('student.planet.view');

// Course-player alias for `/planets/{slug}` — keeps existing onboarding links
// working while exposing the course player at the URL referenced in the spec.
Route::get('/planets/{slug}', [PlanetController::class, 'show'])->name('student.planet.course');

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

    if (!file_exists($file)) {
        abort(404);
    }

    return view()->file($file);
});