<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add indexes to improve search and filter performance.
     * Addresses missing indexes found in security/performance audit (2026-04-13).
     */
    public function up(): void
    {
        // Index on articles.title for search queries (LIKE '%...%' benefits from pg_trgm, but basic index helps ORDER BY)
        Schema::table('articles', function (Blueprint $table) {
            $table->index('title', 'articles_title_index');
        });

        // Index on users.username for profile lookups and search
        Schema::table('users', function (Blueprint $table) {
            $table->index('username', 'users_username_index');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('articles_title_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_username_index');
        });
    }
};
