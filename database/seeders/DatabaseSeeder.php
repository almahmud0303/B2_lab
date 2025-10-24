<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core data - Users and Departments
            DepartmentSeeder::class,
            KUETUserSeeder::class, // Creates users for all roles
            
            // Related data - Teachers and Students
            TeacherSeeder::class,
            StudentSeeder::class,
            
            // Academic data
            CourseSeeder::class,
            EnrollmentSeeder::class,
            ExamSeeder::class,
            ResultSeeder::class,
            
            // Financial data
            FeeSeeder::class,
            
            // Library data
            BookSeeder::class,
            BookIssueSeeder::class,
            
            // Facilities and Communication
            HallSeeder::class,
            NoticeSeeder::class,
        ]);
    }
}
