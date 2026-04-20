<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL-specific: drop old check constraint and migrate to new category set.
        // SQLite (used in tests) has no named CHECK constraints — skip silently.
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("ALTER TABLE articles DROP CONSTRAINT IF EXISTS articles_category_check");
        DB::statement("UPDATE articles SET category = 'tech' WHERE category IN ('proxy', 'vpn', 'tools')");
        DB::statement("ALTER TABLE articles ADD CONSTRAINT articles_category_check CHECK (category IN ('tech', 'security', 'guides', 'news', 'other'))");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("ALTER TABLE articles DROP CONSTRAINT IF EXISTS articles_category_check");
        DB::statement("UPDATE articles SET category = 'other' WHERE category NOT IN ('proxy', 'vpn', 'security', 'tools', 'other')");
        DB::statement("ALTER TABLE articles ADD CONSTRAINT articles_category_check CHECK (category IN ('proxy', 'vpn', 'security', 'tools', 'other'))");
    }
};
