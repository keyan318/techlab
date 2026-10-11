<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // What Astro was grounded in for this answer — titles and links only, never the
            // page text: [{kind: lesson|material|web, title, url?}]. A few hundred bytes, so
            // the sources survive a page refresh instead of living only in the browser.
            $table->json('sources')->nullable()->after('attachments');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('sources');
        });
    }
};
