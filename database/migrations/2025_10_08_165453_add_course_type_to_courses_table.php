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
            $table->enum('course_type', ['compulsory', 'optional'])->default('compulsory')->after('max_students');
            $table->text('prerequisites')->nullable()->after('course_type');
            $table->integer('max_enrollments')->nullable()->after('prerequisites');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['course_type', 'prerequisites', 'max_enrollments']);
        });
    }
};
