<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Categories ──────────────────────────────────────
        Schema::create('chatbot_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('icon')->nullable();          // Bootstrap icon class
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['website_id', 'is_active', 'sort_order']);
        });

        // ── Services (under categories) ─────────────────────
        Schema::create('chatbot_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('chatbot_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category_id', 'is_active', 'sort_order']);
        });

        // ── Sub-services (under services) ───────────────────
        Schema::create('chatbot_sub_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('chatbot_services')->cascadeOnDelete();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('detail_content')->nullable(); // CKEditor rich HTML
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['service_id', 'is_active', 'sort_order']);
        });

        // ── Visitor inquiries (ask-admin fallback) ──────────
        Schema::create('chatbot_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visitor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('visitor_name')->nullable();
            $table->string('visitor_email')->nullable();
            $table->text('message');
            $table->string('status')->default('pending'); // pending, replied, closed
            $table->text('admin_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();

            $table->index(['website_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_inquiries');
        Schema::dropIfExists('chatbot_sub_services');
        Schema::dropIfExists('chatbot_services');
        Schema::dropIfExists('chatbot_categories');
    }
};
