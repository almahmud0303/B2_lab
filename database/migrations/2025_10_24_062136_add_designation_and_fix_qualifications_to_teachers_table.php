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
        Schema::table('teachers', function (Blueprint $table) {
            // Add designation column
            $table->string('designation')->nullable()->after('employee_id');
            
            // Rename qualification to qualifications
            $table->renameColumn('qualification', 'qualifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Remove designation column
            $table->dropColumn('designation');
            
            // Rename qualifications back to qualification
            $table->renameColumn('qualifications', 'qualification');
        });
    }
};
