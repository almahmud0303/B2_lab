<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if enrollments already exist
        if (Enrollment::count() > 0) {
            $this->command->info('Enrollments already exist. Skipping...');
            return;
        }
        
        $students = Student::with('department')->where('status', 'active')->get();
        
        foreach ($students as $student) {
            // Get courses for the student's department and academic year/semester
            $courses = Course::where('department_id', $student->department_id)
                ->where('academic_year', $student->academic_year)
                ->where('semester', $student->semester)
                ->get();
            
            // Also get some courses from previous semesters (completed courses)
            $yearIndex = (int) str_replace(['st', 'nd', 'rd', 'th'], '', $student->academic_year);
            $semesterIndex = (int) str_replace(['st', 'nd', 'rd', 'th'], '', $student->semester);
            
            $completedCourses = Course::where('department_id', $student->department_id)
                ->where(function ($query) use ($student, $yearIndex, $semesterIndex) {
                    // Get courses from previous years
                    for ($y = 1; $y < $yearIndex; $y++) {
                        $query->orWhere('academic_year', $y . 'th');
                    }
                    // Get courses from previous semesters in current year
                    if ($semesterIndex > 1) {
                        $query->orWhere(function ($q) use ($student, $semesterIndex) {
                            $q->where('academic_year', $student->academic_year);
                            for ($s = 1; $s < $semesterIndex; $s++) {
                                $suffix = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th'];
                                $q->orWhere('semester', $s . $suffix[$s]);
                            }
                        });
                    }
                })
                ->get();
            
            // Enroll in current semester courses
            foreach ($courses as $course) {
                // Check if enrollment already exists
                if (!Enrollment::where('student_id', $student->id)->where('course_id', $course->id)->exists()) {
                    Enrollment::create([
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'enrollment_date' => now()->subDays(rand(30, 60)),
                        'status' => 'enrolled',
                        'grade' => null,
                    ]);
                }
            }
            
            // Add completed courses with grades
            foreach ($completedCourses as $course) {
                // Check if enrollment already exists
                if (!Enrollment::where('student_id', $student->id)->where('course_id', $course->id)->exists()) {
                    $grade = rand(25, 40) / 10; // 2.5 to 4.0
                    
                    Enrollment::create([
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'enrollment_date' => now()->subMonths(rand(6, 24)),
                        'status' => 'completed',
                        'grade' => $grade,
                    ]);
                }
            }
        }
    }
}

