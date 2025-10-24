<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get all published results for the student (paginated)
        $results = Result::with(['exam.course.department', 'exam.course.teacher.user'])
            ->where('student_id', $student->id)
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate semester-wise GPA
        $semesterResults = Result::with('exam.course')
            ->where('student_id', $student->id)
            ->where('is_published', true)
            ->get()
            ->groupBy(function($result) {
                return $result->exam->semester ?? 'N/A';
            });

        $semesterGPAs = [];
        foreach ($semesterResults as $semester => $results) {
            $semesterGPAs[$semester] = $this->calculateGPA($results);
        }

        // Calculate overall CGPA
        $cgpa = $this->calculateOverallCGPA($student->id);

        return view('student.results.index', compact('results', 'semesterGPAs', 'cgpa', 'student'));
    }

    public function show(Result $result)
    {
        $student = Auth::user()->student;
        
        // Ensure the result belongs to the authenticated student
        if ($result->student_id !== $student->id) {
            abort(403, 'Unauthorized access');
        }

        // Check if result is published
        if (!$result->is_published) {
            abort(404, 'Result not published yet');
        }

        $result->load(['exam.course.department', 'exam.course.teacher.user']);

        return view('student.results.show', compact('result', 'student'));
    }

    private function calculateGPA($results)
    {
        if ($results->isEmpty()) {
            return 0.0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($results as $result) {
            $credits = $result->exam->course->credits ?? 3;
            $gradePoints = $this->getGradePoints($result->grade);
            
            $totalPoints += $gradePoints * $credits;
            $totalCredits += $credits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
    }

    private function calculateOverallCGPA($studentId)
    {
        $results = Result::with('exam.course')
            ->where('student_id', $studentId)
            ->where('is_published', true)
            ->whereNotNull('grade')
            ->get();

        return $this->calculateGPA($results);
    }

    private function getGradePoints($grade)
    {
        $gradePoints = [
            'A+' => 4.0, 'A' => 3.75, 'A-' => 3.5,
            'B+' => 3.25, 'B' => 3.0, 'B-' => 2.75,
            'C+' => 2.5, 'C' => 2.25, 'C-' => 2.0,
            'D+' => 1.75, 'D' => 1.5, 'D-' => 1.25,
            'F' => 0.0
        ];

        return $gradePoints[$grade] ?? 0.0;
    }
}
