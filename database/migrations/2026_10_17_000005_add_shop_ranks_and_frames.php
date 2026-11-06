<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Frames, animals and suits bought with XP in the Shop. Bought once: never charged twice for the same item.
        Schema::create('shop_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item', 40);   // e.g. "frame:nova-b", "animal:dog", "suit:pilot"
            $table->unsignedInteger('cost');
            $table->timestamps();
            $table->unique(['user_id', 'item']);
        });

        // Gems traded for spendable XP at the Shop. The XP adds to the balance, never to the level.
        Schema::create('gem_exchanges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('gems');
            $table->unsignedInteger('xp');
            $table->timestamps();
            $table->index('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            // The frame the student picked; null wears their rank's default (LevelService::wornFrame).
            $table->string('frame', 40)->nullable()->after('avatar');
            // The highest rank whose "Rank up!" celebration this student has seen.
            $table->unsignedSmallInteger('rank_seen')->default(0)->after('frame');
        });

    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['frame', 'rank_seen']);
        });
        Schema::dropIfExists('gem_exchanges');
        Schema::dropIfExists('shop_purchases');
    }
};
