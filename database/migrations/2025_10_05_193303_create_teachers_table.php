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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('employee_id')->unique();
            $table->string('qualification')->nullable();
            $table->string('specialization')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->date('joining_date');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract'])->default('full_time');
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_department_head')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['employee_id', 'is_active']);
            $table->index(['department_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
