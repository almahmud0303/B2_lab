<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class ResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if results already exist
        if (Result::count() > 0) {
            $this->command->info('Results already exist. Skipping...');
            return;
        }
        
        $students = Student::where('status', 'active')->get();
        
        foreach ($students as $student) {
            // Get all enrollments for this student
            $enrollments = Enrollment::where('student_id', $student->id)->get();
            
            foreach ($enrollments as $enrollment) {
                // Get exams for this course that are completed
                $exams = Exam::where('course_id', $enrollment->course_id)
                    ->where('status', 'completed')
                    ->get();
                
                foreach ($exams as $exam) {
                    // Calculate obtained marks (70-95% for good students, 50-85% for average)
                    $performanceLevel = rand(1, 10);
                    if ($performanceLevel <= 3) {
                        // Top performers (30%)
                        $percentage = rand(85, 98);
                    } elseif ($performanceLevel <= 7) {
                        // Average performers (40%)
                        $percentage = rand(70, 84);
                    } else {
                        // Below average (30%)
                        $percentage = rand(50, 69);
                    }
                    
                    $obtainedMarks = ($exam->total_marks * $percentage) / 100;
                    
                    // Calculate grade based on percentage
                    if ($percentage >= 90) {
                        $grade = 'A+';
                    } elseif ($percentage >= 85) {
                        $grade = 'A';
                    } elseif ($percentage >= 80) {
                        $grade = 'A-';
                    } elseif ($percentage >= 75) {
                        $grade = 'B+';
                    } elseif ($percentage >= 70) {
                        $grade = 'B';
                    } elseif ($percentage >= 65) {
                        $grade = 'B-';
                    } elseif ($percentage >= 60) {
                        $grade = 'C+';
                    } elseif ($percentage >= 55) {
                        $grade = 'C';
                    } elseif ($percentage >= 50) {
                        $grade = 'C-';
                    } else {
                        $grade = 'F';
                    }
                    
                    // Remarks based on performance
                    $remarks = '';
                    if ($percentage >= 90) {
                        $remarks = 'Outstanding performance!';
                    } elseif ($percentage >= 80) {
                        $remarks = 'Excellent work!';
                    } elseif ($percentage >= 70) {
                        $remarks = 'Good job!';
                    } elseif ($percentage >= 60) {
                        $remarks = 'Satisfactory.';
                    } elseif ($percentage >= 50) {
                        $remarks = 'Needs improvement.';
                    } else {
                        $remarks = 'Failed. Please retake the exam.';
                    }
                    
                    // Most results are published, some are not
                    $isPublished = rand(1, 10) <= 8; // 80% published
                    
                    Result::create([
                        'student_id' => $student->id,
                        'exam_id' => $exam->id,
                        'obtained_marks' => round($obtainedMarks, 2),
                        'total_marks' => $exam->total_marks,
                        'percentage' => round($percentage, 2),
                        'grade' => $grade,
                        'remarks' => $remarks,
                        'is_published' => $isPublished,
                    ]);
                }
            }
        }
    }
}

