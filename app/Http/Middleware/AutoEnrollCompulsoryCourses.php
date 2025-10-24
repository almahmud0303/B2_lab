<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Course;
use App\Models\Enrollment;

class AutoEnrollCompulsoryCourses
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Only process for students
        if ($user && $user->role === 'student' && $user->student) {
            $student = $user->student;
            
            // Check if student has been auto-enrolled (use a flag or check enrollment count)
            $hasCompulsoryCourses = $student->enrollments()
                ->whereHas('course', function($query) {
                    $query->where('course_type', 'compulsory');
                })
                ->exists();
            
            // If no compulsory courses enrolled, auto-enroll them
            if (!$hasCompulsoryCourses) {
                $this->autoEnrollCompulsoryCourses($student);
            }
        }

        return $next($request);
    }

    /**
     * Auto-enroll student in all compulsory courses for their department/year/semester
     */
    private function autoEnrollCompulsoryCourses($student)
    {
        // Get all compulsory courses for student's department, year, and semester
        $compulsoryCourses = Course::where('department_id', $student->department_id)
            ->where('academic_year', $student->academic_year)
            ->where('semester', $student->semester)
            ->where('course_type', 'compulsory')
            ->where('is_active', true)
            ->get();

        foreach ($compulsoryCourses as $course) {
            // Check if already enrolled
            $exists = Enrollment::where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->exists();

            if (!$exists) {
                Enrollment::create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'enrollment_date' => now(),
                    'status' => 'enrolled',
                    'grade' => null,
                ]);
            }
        }

        // Log the auto-enrollment
        \Log::info('Auto-enrolled student in compulsory courses', [
            'student_id' => $student->id,
            'courses_enrolled' => $compulsoryCourses->count(),
        ]);
    }
}
