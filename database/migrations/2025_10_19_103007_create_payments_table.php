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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('payment_id')->unique(); // bKash payment ID
            $table->string('transaction_id')->nullable(); // bKash transaction ID
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('BDT');
            $table->enum('payment_method', ['bkash', 'nagad', 'rocket', 'bank_transfer', 'cash']);
            $table->string('phone_number')->nullable(); // For mobile banking
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->string('gateway_response')->nullable(); // Store API response
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Store additional payment data
            $table->timestamps();
            
            $table->index(['student_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index(['payment_id']);
            $table->index(['transaction_id']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
