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
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['general', 'academic', 'exam', 'fee', 'library', 'event']);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->json('target_roles')->nullable(); // ['student', 'teacher', 'staff']
            $table->date('publish_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'is_published']);
            $table->index(['publish_date', 'expiry_date']);
            $table->index(['priority', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
