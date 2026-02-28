<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            // Drop the old unique index on visitor_token alone
            $table->dropUnique('visitors_visitor_token_unique');

            // Add composite unique index on (visitor_token, website_id)
            // Same visitor can exist across different websites
            $table->unique(['visitor_token', 'website_id'], 'visitors_token_website_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropUnique('visitors_token_website_unique');
            $table->unique('visitor_token', 'visitors_visitor_token_unique');
        });
    }
};
