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
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('fee_amount', 10, 2)->default(0.00)->after('max_students');
            $table->string('currency', 3)->default('BDT')->after('fee_amount');
            $table->boolean('fee_required')->default(true)->after('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['fee_amount', 'currency', 'fee_required']);
        });
    }
};
