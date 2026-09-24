<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Astro for faculty: a teaching-assistant chat (lesson plans, quizzes, rubrics…).
 * Reuses the chat pipeline (streaming, persistence, web research) but with the
 * faculty persona and no planet-lesson sources.
 */
class FacultyChatController extends ChatController
{
    public function index(Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') !== 'faculty') {
            return redirect(route('student.chat'));
        }

        return view('student.chat', [
            'chatMode' => 'faculty',
            'messageUrl' => route('faculty.chat.message'),
            'userName' => Auth::user()->name ?? 'Captain',
            'userInitial' => mb_strtoupper(mb_substr(Auth::user()->name ?? 'C', 0, 1)),
            'userRole' => 'Captain',
            'modelLabel' => config('nvidia_nim.model_label', 'Astro · NVIDIA NIM'),
            'planetCatalog' => [],
            'preselectSource' => null,
            'conversations' => $this->formatConversations(
                Auth::user()->conversations()->latest('updated_at')->get()
            ),
        ]);
    }

    public function send(Request $request): JsonResponse|StreamedResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        if ((Auth::user()->role ?? 'student') !== 'faculty') {
            return response()->json(['error' => 'Faculty only.'], 403);
        }

        $this->nimService = $this->nimService->forPersona('faculty');

        return parent::send($request);
    }
}
