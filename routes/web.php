<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

// Landing page — auth-aware redirect happens inside the controller.
Route::get('/', [HomeController::class, 'index']);

// Authentication.
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Student area.
Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
Route::get('/student/crew', [StudentController::class, 'crew'])->name('student.crew');
Route::post('/student/crew/join', [StudentController::class, 'joinCrew'])->name('student.crew.join');
Route::get('/student/crew/home', [StudentController::class, 'crewHome'])->name('student.crew.home');

// Teacher area.
Route::get('/teacher/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
Route::post('/teacher/crew', [TeacherController::class, 'createCrew'])->name('teacher.crew.create');
