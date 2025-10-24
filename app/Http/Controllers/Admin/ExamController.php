<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Course;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = Exam::with(['course.department', 'course.teacher.user'])
            ->latest()
            ->paginate(15);

        return view('admin.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::where('is_active', true)->with(['department', 'teacher.user'])->get();
        return view('admin.exams.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:quiz,midterm,final,assignment',
            'exam_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'total_marks' => 'required|integer|min:1|max:100',
            'course_id' => 'required|exists:courses,id',
            'description' => 'nullable|string',
            'venue' => 'nullable|string|max:255',
        ]);

        Exam::create([
            'title' => $request->title,
            'type' => $request->type,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_marks' => $request->total_marks,
            'course_id' => $request->course_id,
            'description' => $request->description,
            'venue' => $request->venue,
            'status' => 'scheduled',
        ]);

        return redirect()->route('admin.exams.index')->with('success', 'Exam created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        $exam->load(['course.department', 'course.teacher.user', 'results.enrollment.student.user']);
        
        // Get exam statistics
        $stats = [
            'total_students' => $exam->results->count(),
            'average_marks' => $exam->results->avg('marks_obtained') ?? 0,
            'highest_marks' => $exam->results->max('marks_obtained') ?? 0,
            'lowest_marks' => $exam->results->min('marks_obtained') ?? 0,
        ];

        // Get results by grade
        $gradeDistribution = $exam->results->groupBy('grade')->map->count();

        // Get recent results
        $recentResults = $exam->results()
            ->with('enrollment.student.user')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.exams.show', compact('exam', 'stats', 'gradeDistribution', 'recentResults'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        $courses = Course::where('is_active', true)->with(['department', 'teacher.user'])->get();
        return view('admin.exams.edit', compact('exam', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:quiz,midterm,final,assignment',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'total_marks' => 'required|integer|min:1|max:100',
            'course_id' => 'required|exists:courses,id',
            'description' => 'nullable|string',
            'venue' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        $exam->update([
            'title' => $request->title,
            'type' => $request->type,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_marks' => $request->total_marks,
            'course_id' => $request->course_id,
            'description' => $request->description,
            'venue' => $request->venue,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.exams.index')->with('success', 'Exam updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted successfully.');
    }

    /**
     * Toggle the status of the specified exam.
     */
    public function toggleStatus(Exam $exam)
    {
        $currentStatus = $exam->status;
        
        // Cycle through statuses: scheduled -> ongoing -> completed
        switch ($currentStatus) {
            case 'scheduled':
                $exam->status = 'ongoing';
                break;
            case 'ongoing':
                $exam->status = 'completed';
                break;
            case 'completed':
                $exam->status = 'scheduled';
                break;
            default:
                $exam->status = 'scheduled';
        }
        
        $exam->save();

        return back()->with('success', 'Exam status updated successfully.');
    }
}