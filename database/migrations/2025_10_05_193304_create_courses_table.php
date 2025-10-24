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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('course_code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('credits');
            $table->enum('semester', ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th']);
            $table->enum('academic_year', ['1st', '2nd', '3rd', '4th']);
            $table->integer('max_students')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['course_code', 'is_active']);
            $table->index(['department_id', 'semester']);
            $table->index(['teacher_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
