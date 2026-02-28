<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add role and company info to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('client')->after('email'); // superadmin, client
            $table->string('company_name')->nullable()->after('role');
            $table->string('phone')->nullable()->after('company_name');
            $table->boolean('is_active')->default(true)->after('phone');
        });

        // Add owner (user_id) and settings to websites
        Schema::table('websites', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
            $table->string('welcome_message')->default('Hi there! How can I help you today?')->after('api_key');
            $table->string('widget_color')->default('#ff7a18')->after('welcome_message');
            $table->boolean('is_active')->default(true)->after('widget_color');
        });
    }

    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'welcome_message', 'widget_color', 'is_active']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'company_name', 'phone', 'is_active']);
        });
    }
};
