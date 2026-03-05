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
        Schema::table('websites', function (Blueprint $table) {
            $table->string('widget_color_type', 20)->default('gradient')->after('widget_color'); // gradient | plain
            $table->string('widget_position', 20)->default('bottom-right')->after('widget_color_type'); // bottom-right | bottom-left
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn(['widget_color_type', 'widget_position']);
        });
    }
};
