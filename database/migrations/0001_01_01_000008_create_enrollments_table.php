<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->dateTime('enrolled_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('status', ['active', 'completed', 'canceled', 'expired'])->default('active');
            $table->decimal('progress_percent', 5, 2)->default(0.00);
            $table->dateTime('expires_at')->nullable();
            $table->decimal('price_paid', 10, 2)->default(0.00);
            $table->string('currency', 10)->default('IDR');
            $table->unique(['user_id', 'course_id'], 'uq_enrollment_user_course');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
