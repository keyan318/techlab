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

// Course overview: the learning-plan node path for a track.
Route::get('/student/planet/{slug}/overview', [PlanetController::class, 'overview'])->name('student.planet.overview');

// Lesson stub — placeholder target for "Start learning" until lessons exist.
Route::get('/student/planet/{slug}/lesson/{lesson}', [PlanetController::class, 'lesson'])->name('student.planet.lesson');

// Chapter story — comic-panel intro that precedes a lesson.
Route::get('/student/planet/{slug}/story/{lesson}', [PlanetController::class, 'story'])->name('student.planet.story');

// Learning-plan generation for a track. Placeholder payload until the
// Nemotron roadmap generator lands — same response contract, instant reply.
Route::post('/student/planet/{slug}/plan', [PlanetController::class, 'generatePlan'])->name('student.planet.plan');

Route::get('/teacher/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
Route::post('/teacher/crew', [TeacherController::class, 'createCrew'])->name('teacher.crew.create');

// Course-player alias for `/planets/{slug}` — keeps existing onboarding links
// working while exposing the course player at the URL referenced in the spec.
Route::get('/planets/{slug}', [PlanetController::class, 'show'])->name('student.planet.course');

use App\Http\Controllers\DeckController;

// Deck API — slides served as JSON for the Bolt Slides engine.
Route::get('/api/decks/{id}.json', [DeckController::class, 'show'])->name('decks.show');
Route::post('/api/decks', [DeckController::class, 'store'])->name('decks.store');

// Standalone Bolt Slides app (Vite + React build) — no Blade layout.
Route::get('/decks/{id}', [DeckController::class, 'showPage'])->name('decks.page');

// Generate a slide deck from a conversation (Studio PPT action).
Route::post('/conversations/{conversation}/generate-deck', [ChatController::class, 'generateDeck'])->name('conversations.generate-deck');
