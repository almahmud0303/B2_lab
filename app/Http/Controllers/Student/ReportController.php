<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Result;
use App\Models\Fee;
use App\Models\BookIssue;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        // Get academic statistics
        $academicStats = $this->getAcademicStats($student);
        
        // Get fee statistics
        $feeStats = $this->getFeeStats($student);
        
        // Get library statistics
        $libraryStats = $this->getLibraryStats($student);

        return view('student.reports.index', compact(
            'student',
            'academicStats',
            'feeStats',
            'libraryStats'
        ));
    }

    public function academicReport()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        // Get all results
        $results = $student->results()
            ->with(['exam.course.department'])
            ->where('is_published', true)
            ->orderBy('exam.course.semester')
            ->get();

        // Group results by semester
        $semesterResults = $results->groupBy('exam.course.semester');

        // Calculate statistics
        $stats = $this->getAcademicStats($student);

        return view('student.reports.academic', compact(
            'student',
            'results',
            'semesterResults',
            'stats'
        ));
    }

    public function downloadTranscript()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if student has paid all fees
        $unpaidFees = $student->fees()
            ->whereIn('status', ['pending', 'unpaid', 'overdue'])
            ->sum('amount');

        if ($unpaidFees > 0) {
            return back()->with('error', 'Cannot download transcript. Please pay all outstanding fees first.');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        // Get all results
        $results = $student->results()
            ->with(['exam.course.department'])
            ->where('is_published', true)
            ->orderBy('exam.course.semester')
            ->get();

        // Calculate CGPA
        $cgpa = $this->calculateCGPA($student);

        // Generate PDF
        $pdf = Pdf::loadView('student.reports.transcript-pdf', compact('student', 'results', 'cgpa'));
        
        return $pdf->download('transcript-' . $student->student_id . '.pdf');
    }

    public function gradeReport()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        // Get results grouped by semester
        $results = $student->results()
            ->with(['exam.course.department'])
            ->where('is_published', true)
            ->get()
            ->groupBy('exam.course.semester');

        // Calculate semester-wise statistics
        $semesterStats = [];
        foreach ($results as $semester => $semesterResults) {
            $semesterStats[$semester] = $this->calculateSemesterStats($semesterResults);
        }

        // Calculate overall statistics
        $overallStats = $this->getAcademicStats($student);

        return view('student.reports.grade-report', compact(
            'student',
            'results',
            'semesterStats',
            'overallStats'
        ));
    }

    public function downloadGradeReport()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        // Get results grouped by semester
        $results = $student->results()
            ->with(['exam.course.department'])
            ->where('is_published', true)
            ->get()
            ->groupBy('exam.course.semester');

        // Calculate statistics
        $semesterStats = [];
        foreach ($results as $semester => $semesterResults) {
            $semesterStats[$semester] = $this->calculateSemesterStats($semesterResults);
        }

        $overallStats = $this->getAcademicStats($student);

        // Generate PDF
        $pdf = Pdf::loadView('student.reports.grade-report-pdf', compact(
            'student',
            'results',
            'semesterStats',
            'overallStats'
        ));
        
        return $pdf->download('grade-report-' . $student->student_id . '.pdf');
    }

    public function feeReport()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        // Get all fees
        $fees = $student->fees()
            ->orderBy('due_date')
            ->get();

        // Calculate fee statistics
        $stats = $this->getFeeStats($student);

        // Group fees by type
        $feesByType = $fees->groupBy('fee_type');

        return view('student.reports.fee-report', compact(
            'student',
            'fees',
            'stats',
            'feesByType'
        ));
    }

    public function downloadFeeReport()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        // Get all fees
        $fees = $student->fees()
            ->orderBy('due_date')
            ->get();

        // Calculate fee statistics
        $stats = $this->getFeeStats($student);

        // Group fees by type
        $feesByType = $fees->groupBy('fee_type');

        // Generate PDF
        $pdf = Pdf::loadView('student.reports.fee-report-pdf', compact(
            'student',
            'fees',
            'stats',
            'feesByType'
        ));
        
        return $pdf->download('fee-report-' . $student->student_id . '.pdf');
    }

    public function libraryReport()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        // Get book history
        $bookHistory = $student->bookIssues()
            ->with('book')
            ->orderBy('issue_date', 'desc')
            ->get();

        // Calculate library statistics
        $stats = $this->getLibraryStats($student);

        // Group books by status
        $booksByStatus = $bookHistory->groupBy('status');

        return view('student.reports.library-report', compact(
            'student',
            'bookHistory',
            'stats',
            'booksByStatus'
        ));
    }

    public function downloadLibraryReport()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        // Get book history
        $bookHistory = $student->bookIssues()
            ->with('book')
            ->orderBy('issue_date', 'desc')
            ->get();

        // Calculate library statistics
        $stats = $this->getLibraryStats($student);

        // Group books by status
        $booksByStatus = $bookHistory->groupBy('status');

        // Generate PDF
        $pdf = Pdf::loadView('student.reports.library-report-pdf', compact(
            'student',
            'bookHistory',
            'stats',
            'booksByStatus'
        ));
        
        return $pdf->download('library-report-' . $student->student_id . '.pdf');
    }

    public function comprehensiveReport()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        // Get all data
        $academicStats = $this->getAcademicStats($student);
        $feeStats = $this->getFeeStats($student);
        $libraryStats = $this->getLibraryStats($student);

        // Get recent activities
        $recentResults = $student->results()
            ->with(['exam.course'])
            ->where('is_published', true)
            ->latest()
            ->limit(5)
            ->get();
        
        $recentFees = $student->fees()
            ->latest()
            ->limit(5)
            ->get();
        
        $recentBooks = $student->bookIssues()
            ->with('book')
            ->latest()
            ->limit(5)
            ->get();
        
        return view('student.reports.comprehensive', compact(
            'student',
            'academicStats',
            'feeStats',
            'libraryStats',
            'recentResults',
            'recentFees',
            'recentBooks'
        ));
    }

    public function downloadComprehensiveReport()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Load student with relationships
        $student->load(['user', 'department']);

        // Get all data
        $academicStats = $this->getAcademicStats($student);
        $feeStats = $this->getFeeStats($student);
        $libraryStats = $this->getLibraryStats($student);

        // Get recent activities
        $recentResults = $student->results()
            ->with(['exam.course'])
            ->where('is_published', true)
            ->latest()
            ->limit(5)
            ->get();
        
        $recentFees = $student->fees()
            ->latest()
            ->limit(5)
            ->get();

        $recentBooks = $student->bookIssues()
            ->with('book')
                ->latest()
            ->limit(5)
                ->get();
            
        // Generate PDF
        $pdf = Pdf::loadView('student.reports.comprehensive-pdf', compact(
            'student',
            'academicStats',
            'feeStats',
            'libraryStats',
            'recentResults',
            'recentFees',
            'recentBooks'
        ));
        
        return $pdf->download('comprehensive-report-' . $student->student_id . '.pdf');
    }

    private function getAcademicStats($student)
    {
        $results = $student->results()
            ->where('is_published', true)
            ->get();
        
        if ($results->isEmpty()) {
            return [
                'total_courses' => 0,
                'cgpa' => 0.0,
                'total_credits' => 0,
                'passed_courses' => 0,
                'failed_courses' => 0,
            ];
        }

        $totalCredits = 0;
        $totalPoints = 0;
        $passedCourses = 0;
        $failedCourses = 0;

        foreach ($results as $result) {
            $credits = $result->exam->course->credits ?? 3;
            $gradePoints = $this->getGradePoints($result->grade);
            
            $totalCredits += $credits;
            $totalPoints += $gradePoints * $credits;

            if ($result->grade !== 'F') {
                $passedCourses++;
            } else {
                $failedCourses++;
            }
        }

                return [
            'total_courses' => $results->count(),
            'cgpa' => $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0,
            'total_credits' => $totalCredits,
            'passed_courses' => $passedCourses,
            'failed_courses' => $failedCourses,
        ];
    }

    private function getFeeStats($student)
    {
        $fees = $student->fees();
        
        return [
            'total_fees' => $fees->sum('amount'),
            'paid_fees' => $fees->where('status', 'paid')->sum('paid_amount'),
            'pending_fees' => $fees->whereIn('status', ['pending', 'unpaid'])->sum('amount'),
            'overdue_fees' => $fees->where('status', 'overdue')->sum('amount'),
        ];
    }

    private function getLibraryStats($student)
    {
        $bookIssues = $student->bookIssues();
        
        return [
            'total_books_borrowed' => $bookIssues->count(),
            'books_returned' => $bookIssues->where('status', 'returned')->count(),
            'books_overdue' => $bookIssues->where('status', 'overdue')->count(),
            'total_fines' => $bookIssues->sum('fine_amount'),
        ];
    }

    private function calculateCGPA($student)
    {
        $results = $student->results()
            ->where('is_published', true)
            ->get();

        if ($results->isEmpty()) {
            return 0.0;
        }

        $totalCredits = 0;
        $totalPoints = 0;

        foreach ($results as $result) {
            $credits = $result->exam->course->credits ?? 3;
            $gradePoints = $this->getGradePoints($result->grade);
            
            $totalCredits += $credits;
            $totalPoints += $gradePoints * $credits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
    }

    private function calculateSemesterStats($results)
    {
        if ($results->isEmpty()) {
            return [
                'total_courses' => 0,
                'sgpa' => 0.0,
                'total_credits' => 0,
            ];
        }

        $totalCredits = 0;
        $totalPoints = 0;

        foreach ($results as $result) {
            $credits = $result->exam->course->credits ?? 3;
            $gradePoints = $this->getGradePoints($result->grade);
            
            $totalCredits += $credits;
            $totalPoints += $gradePoints * $credits;
        }

        return [
            'total_courses' => $results->count(),
            'sgpa' => $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0,
            'total_credits' => $totalCredits,
        ];
    }

    private function getGradePoints($grade)
    {
        return match($grade) {
            'A+' => 4.0,
            'A' => 3.75,
            'A-' => 3.5,
            'B+' => 3.25,
            'B' => 3.0,
            'B-' => 2.75,
            'C+' => 2.5,
            'C' => 2.25,
            'C-' => 2.0,
            'D+' => 1.75,
            'D' => 1.5,
            'D-' => 1.25,
            'F' => 0.0,
            default => 0.0
        };
    }
}
