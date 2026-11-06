<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Astro's tour (resources/views/components/astro-tour.blade.php): one short chapter per page,
 * written in config/astro-tour.php (students' `chapters`, captains' `faculty_chapters`). A chapter
 * plays by itself the first time someone opens its page; once they finish or skip it, it's saved
 * here and only plays again from the Tour button.
 */
class TourController extends Controller
{
    public function seen(Request $request): JsonResponse
    {
        $user = $request->user();
        $chapters = config($user->role === 'faculty' ? 'astro-tour.faculty_chapters' : 'astro-tour.chapters');

        ['chapter' => $chapter] = $request->validate([
            'chapter' => ['required', 'string', Rule::in(array_keys($chapters))],
        ]);

        $seen = $user->tour_seen ?? [];
        if (! in_array($chapter, $seen, true)) {
            $seen[] = $chapter;
            $user->forceFill(['tour_seen' => $seen])->save();
        }

        return response()->json(['seen' => $seen]);
    }
}
