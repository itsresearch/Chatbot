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
        if (!Schema::hasColumn('conversations', 'status')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->enum('status', ['bot', 'human'])->default('bot');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('conversations', 'status')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
