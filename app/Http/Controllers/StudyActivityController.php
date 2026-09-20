<?php

namespace App\Http\Controllers;

use App\Models\StudyActivity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StudyActivityController extends Controller
{
    /** Minimum gap between two counted heartbeats, so a client can't inflate its hours. */
    private const MIN_GAP_SECONDS = 55;

    /**
     * POST /student/activity — the shell pings once a minute while the tab is
     * visible; each accepted ping is one active minute for today.
     */
    public function ping(Request $request): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->noContent(401);
        }

        $activity = StudyActivity::firstOrCreate(
            ['user_id' => $user->id, 'date' => today()->toDateString()],
            ['minutes' => 0]
        );

        if (! $activity->last_ping_at || $activity->last_ping_at->diffInSeconds(now(), true) >= self::MIN_GAP_SECONDS) {
            $activity->minutes++;
            $activity->last_ping_at = now();
            $activity->save();
        }

        return response()->noContent();
    }
}
