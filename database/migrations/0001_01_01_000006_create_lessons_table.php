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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('course_modules')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('title', 255);
            $table->string('slug', 255)->nullable();
            $table->enum('content_type', ['video', 'article', 'pdf', 'audio', 'quiz', 'other'])->default('video');
            $table->text('content')->nullable();
            $table->string('media_url', 1024)->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->boolean('is_downloadable')->default(0);
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
