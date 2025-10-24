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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->decimal('obtained_marks', 5, 2);
            $table->decimal('total_marks', 5, 2);
            $table->decimal('percentage', 5, 2);
            $table->string('grade')->nullable();
            $table->decimal('grade_points', 3, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['student_id', 'exam_id']);
            $table->index(['student_id', 'is_published']);
            $table->index(['exam_id', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
