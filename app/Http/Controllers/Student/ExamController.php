<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get upcoming exams for enrolled courses
        $upcomingExams = Exam::whereHas('course.enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id)
                  ->where('status', 'enrolled');
        })
        ->where('exam_date', '>=', now())
        ->where('status', 'scheduled')
        ->with('course')
        ->orderBy('exam_date')
        ->get();

        // Get past exams with results
        $pastExams = Exam::whereHas('course.enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
        ->where('exam_date', '<', now())
        ->with(['course', 'results' => function($query) use ($student) {
            $query->where('student_id', $student->id);
        }])
        ->orderBy('exam_date', 'desc')
        ->get();

        return view('student.exams.index', compact('upcomingExams', 'pastExams', 'student'));
    }

    public function show(Exam $exam)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if student is enrolled in the exam's course
        $enrollment = $student->enrollments()
            ->where('course_id', $exam->course_id)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this course.');
        }

        // Load exam with relationships
        $exam->load(['course.department', 'course.teacher.user']);

        // Get student's result for this exam
        $result = $student->results()
            ->where('exam_id', $exam->id)
            ->first();

        return view('student.exams.show', compact('exam', 'result', 'student'));
    }

    public function schedule()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get all exams for enrolled courses
        $exams = Exam::whereHas('course.enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id)
                  ->where('status', 'enrolled');
        })
        ->with('course.department')
        ->orderBy('exam_date')
        ->get();

        // Group exams by month
        $examsByMonth = $exams->groupBy(function($exam) {
            return $exam->exam_date->format('F Y');
        });

        return view('student.exams.schedule', compact('examsByMonth', 'student'));
    }

    public function results()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get all published results
        $results = Result::where('student_id', $student->id)
            ->with(['exam.course.department'])
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $averageGrade = $results->avg(function($result) {
            $gradePoints = [
                'A+' => 4.0, 'A' => 3.75, 'A-' => 3.5,
                'B+' => 3.25, 'B' => 3.0, 'B-' => 2.75,
                'C+' => 2.5, 'C' => 2.25, 'C-' => 2.0,
                'D+' => 1.75, 'D' => 1.5, 'D-' => 1.25,
                'F' => 0.0
            ];
            return $gradePoints[$result->grade] ?? 0.0;
        });

        $overallAverage = $results->avg('percentage');
        $cgpa = $this->calculateCGPA($student);

        return view('student.exams.results', compact('results', 'averageGrade', 'overallAverage', 'cgpa', 'student'));
    }

    public function calendar(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get current month or requested month
        $currentMonth = $request->has('month') ? \Carbon\Carbon::parse($request->month) : now();
        
        // Get exams for the month
        $exams = Exam::whereHas('course.enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
        ->whereMonth('exam_date', $currentMonth->month)
        ->whereYear('exam_date', $currentMonth->year)
        ->with('course')
        ->orderBy('exam_date')
        ->get();

        // Generate calendar days
        $calendarDays = [];
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek();
        $endOfCalendar = $endOfMonth->copy()->endOfWeek();

        for ($date = $startOfCalendar->copy(); $date->lte($endOfCalendar); $date->addDay()) {
            $dayEvents = $exams->filter(function($exam) use ($date) {
                return $exam->exam_date->format('Y-m-d') === $date->format('Y-m-d');
            })->map(function($exam) {
                return [
                    'title' => $exam->title,
                    'type' => 'exam',
                    'course' => $exam->course->title
                ];
            });

            $calendarDays[] = [
                'day' => $date->day,
                'date' => $date->copy(),
                'isCurrentMonth' => $date->month === $currentMonth->month,
                'events' => $dayEvents->toArray()
            ];
        }

        // Get upcoming events for the next 30 days
        $upcomingEvents = $exams->where('exam_date', '>=', now())
            ->where('exam_date', '<=', now()->addDays(30))
            ->map(function($exam) {
                return [
                    'title' => $exam->title,
                    'type' => 'exam',
                    'course' => $exam->course->title,
                    'date' => $exam->exam_date
                ];
            })
            ->take(10);

        return view('student.exams.calendar', compact('currentMonth', 'calendarDays', 'upcomingEvents'));
    }

    public function downloadTranscript()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if student has paid all fees
        $unpaidFees = $student->fees()
            ->whereIn('status', ['pending', 'overdue'])
            ->sum('amount');

        if ($unpaidFees > 0) {
            return back()->with('error', 'Cannot download transcript. Please pay all outstanding fees first.');
        }

        // Get all results
        $results = $student->results()
            ->with(['exam.course.department'])
            ->where('is_published', true)
            ->orderBy('exam.course.semester')
            ->get();

        // Calculate CGPA
        $cgpa = $this->calculateCGPA($student);

        $pdf = \PDF::loadView('student.exams.transcript', compact('student', 'results', 'cgpa'));
        
        return $pdf->download('transcript-' . $student->student_id . '.pdf');
    }

    public function gradeReport()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get results grouped by semester
        $results = $student->results()
            ->with(['exam.course.department'])
            ->where('is_published', true)
            ->get()
            ->groupBy('exam.course.semester');

        $semesterStats = [];
        foreach ($results as $semester => $semesterResults) {
            $semesterStats[$semester] = $this->calculateSemesterStats($semesterResults);
        }

        // Calculate overall CGPA
        $overallStats = $this->calculateResultStats($student);

        return view('student.exams.grade-report', compact('results', 'semesterStats', 'overallStats', 'student'));
    }

    private function calculateResultStats($student)
    {
        $results = $student->results()
            ->where('is_published', true)
            ->get();

        if ($results->isEmpty()) {
            return [
                'total_exams' => 0,
                'cgpa' => 0,
                'grades' => [],
            ];
        }

        $totalCredits = 0;
        $totalPoints = 0;
        $grades = [];

        foreach ($results as $result) {
            $credits = $result->exam->course->credits ?? 3;
            $gradePoints = $this->getGradePoints($result->grade);
            
            $totalCredits += $credits;
            $totalPoints += $gradePoints * $credits;
            
            if (isset($grades[$result->grade])) {
                $grades[$result->grade]++;
            } else {
                $grades[$result->grade] = 1;
            }
        }

        return [
            'total_exams' => $results->count(),
            'cgpa' => $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0,
            'grades' => $grades,
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
                'sgpa' => 0,
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
            'sgpa' => $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0,
            'total_credits' => $totalCredits,
        ];
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