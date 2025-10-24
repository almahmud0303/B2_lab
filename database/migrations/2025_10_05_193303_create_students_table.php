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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('student_id')->unique();
            $table->string('admission_number')->unique();
            $table->date('admission_date');
            $table->enum('academic_year', ['1st', '2nd', '3rd', '4th']);
            $table->enum('semester', ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th']);
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->text('guardian_address')->nullable();
            $table->decimal('cgpa', 3, 2)->nullable();
            $table->enum('status', ['active', 'inactive', 'graduated', 'suspended'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['student_id', 'is_active']);
            $table->index(['department_id', 'academic_year']);
            $table->index(['status', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
