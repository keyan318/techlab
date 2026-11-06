<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Animals now cost XP and the cat is the free one: every student starts over as a cat, keeping the same place
     * in the coat list (a second coat stays a second coat). Captains keep theirs.
     */
    public function up(): void
    {
        $cat = array_keys(config('avatar.animals.cat.coats'));

        DB::table('users')->where('role', 'student')->whereNotNull('avatar')->orderBy('id')
            ->each(function ($user) use ($cat) {
                $look = json_decode($user->avatar, true);
                if (! is_array($look) || ($look['animal'] ?? 'cat') === 'cat') {
                    return;
                }
                $coats = array_keys((array) config("avatar.animals.{$look['animal']}.coats"));
                $at = array_search($look['coat'] ?? null, $coats, true);
                $look['animal'] = 'cat';
                $look['coat'] = $cat[min($at === false ? 0 : $at, count($cat) - 1)];
                DB::table('users')->where('id', $user->id)->update(['avatar' => json_encode($look)]);
            });
    }

    public function down(): void
    {
        // Nothing to undo: the animals they had before aren't kept.
    }
};
