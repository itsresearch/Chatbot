<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove icon, sort_order, is_active from categories
        Schema::table('chatbot_categories', function (Blueprint $table) {
            $table->dropForeign(['website_id']);
            $table->dropIndex(['website_id', 'is_active', 'sort_order']);
            $table->dropColumn(['icon', 'sort_order', 'is_active']);
            $table->index('website_id');
            $table->foreign('website_id')->references('id')->on('websites')->cascadeOnDelete();
        });

        // Remove icon, sort_order, is_active from services
        Schema::table('chatbot_services', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id', 'is_active', 'sort_order']);
            $table->dropColumn(['icon', 'sort_order', 'is_active']);
            $table->index('category_id');
            $table->foreign('category_id')->references('id')->on('chatbot_categories')->cascadeOnDelete();
        });

        // Remove icon, sort_order, is_active from sub-services
        Schema::table('chatbot_sub_services', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropIndex(['service_id', 'is_active', 'sort_order']);
            $table->dropColumn(['icon', 'sort_order', 'is_active']);
            $table->index('service_id');
            $table->foreign('service_id')->references('id')->on('chatbot_services')->cascadeOnDelete();
        });

        // Drop inquiries table entirely
        Schema::dropIfExists('chatbot_inquiries');
    }

    public function down(): void
    {
        Schema::table('chatbot_categories', function (Blueprint $table) {
            $table->dropForeign(['website_id']);
            $table->dropIndex(['website_id']);
            $table->string('icon')->nullable()->after('name');
            $table->integer('sort_order')->default(0)->after('description');
            $table->boolean('is_active')->default(true)->after('sort_order');
            $table->index(['website_id', 'is_active', 'sort_order']);
            $table->foreign('website_id')->references('id')->on('websites')->cascadeOnDelete();
        });

        Schema::table('chatbot_services', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id']);
            $table->string('icon')->nullable()->after('name');
            $table->integer('sort_order')->default(0)->after('description');
            $table->boolean('is_active')->default(true)->after('sort_order');
            $table->index(['category_id', 'is_active', 'sort_order']);
            $table->foreign('category_id')->references('id')->on('chatbot_categories')->cascadeOnDelete();
        });

        Schema::table('chatbot_sub_services', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropIndex(['service_id']);
            $table->string('icon')->nullable()->after('name');
            $table->integer('sort_order')->default(0)->after('detail_content');
            $table->boolean('is_active')->default(true)->after('sort_order');
            $table->index(['service_id', 'is_active', 'sort_order']);
            $table->foreign('service_id')->references('id')->on('chatbot_services')->cascadeOnDelete();
        });

        Schema::create('chatbot_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visitor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('visitor_name')->nullable();
            $table->string('visitor_email')->nullable();
            $table->text('message');
            $table->string('status')->default('pending');
            $table->text('admin_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
            $table->index(['website_id', 'status']);
        });
    }
};
