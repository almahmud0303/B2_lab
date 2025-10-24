<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $exams = Exam::with('course.department')
            ->whereHas('course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->orderBy('exam_date', 'desc')
            ->paginate(15);

        return view('teacher.exams.index', compact('exams', 'teacher'));
    }

    public function create()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $courses = Course::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->get();

        return view('teacher.exams.create', compact('courses', 'teacher'));
    }

    public function store(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:quiz,assignment,midterm', // Teachers can only create quiz, assignment, midterm
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_marks' => 'required|integer|min:1',
            'venue' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        // Verify the course belongs to this teacher
        $course = Course::where('id', $request->course_id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        Exam::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'type' => $request->type,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_marks' => $request->total_marks,
            'venue' => $request->venue,
            'description' => $request->description,
            'status' => 'scheduled',
        ]);

        $typeName = ucfirst($request->type);
        return redirect()->route('teacher.exams.index')->with('success', "$typeName created successfully.");
    }

    public function show($id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $exam = Exam::with(['course.department', 'results.student.user'])
            ->whereHas('course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->findOrFail($id);

        return view('teacher.exams.show', compact('exam', 'teacher'));
    }

    public function edit($id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $exam = Exam::with('course')
            ->whereHas('course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->findOrFail($id);

        $courses = Course::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->get();

        return view('teacher.exams.edit', compact('exam', 'courses', 'teacher'));
    }

    public function update(Request $request, $id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $exam = Exam::whereHas('course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->findOrFail($id);

        // Prevent teachers from creating/editing final exams
        if ($exam->type === 'final' && $request->type !== 'final') {
            return redirect()->back()->with('error', 'Cannot change final exam type. Contact admin.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:quiz,assignment,midterm,final', // Allow final in validation but check above
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_marks' => 'required|integer|min:1',
            'venue' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        // Teachers cannot change type to final
        if ($request->type === 'final' && $exam->type !== 'final') {
            return redirect()->back()->with('error', 'Only admin can set final exams.');
        }

        $exam->update($request->all());

        $typeName = ucfirst($exam->type);
        return redirect()->route('teacher.exams.index')->with('success', "$typeName updated successfully.");
    }

    public function destroy($id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $exam = Exam::whereHas('course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->findOrFail($id);

        // Can only delete if no results are entered
        if ($exam->results()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete exam with existing results.');
        }

        $exam->delete();

        return redirect()->route('teacher.exams.index')->with('success', 'Exam deleted successfully.');
    }

    public function enterMarks($id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $exam = Exam::with(['course.enrollments.student.user', 'results'])
            ->whereHas('course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->findOrFail($id);

        $students = $exam->course->enrollments()
            ->where('status', 'enrolled')
            ->with('student.user')
            ->get()
            ->pluck('student');

        // Get existing results
        $existingResults = $exam->results->keyBy('student_id');

        return view('teacher.exams.enter-marks', compact('exam', 'students', 'existingResults', 'teacher'));
    }

    public function saveMarks(Request $request, $id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $exam = Exam::whereHas('course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->findOrFail($id);

        $request->validate([
            'marks' => 'required|array',
            'marks.*' => 'nullable|numeric|min:0|max:' . $exam->total_marks,
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->marks as $studentId => $marks) {
                if ($marks !== null && $marks !== '') {
                    // Calculate grade
                    $percentage = ($marks / $exam->total_marks) * 100;
                    $grade = $this->calculateGrade($percentage);
                    $gradePoints = $this->calculateGradePoints($percentage);

                    Result::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'exam_id' => $exam->id,
                        ],
                        [
                            'marks_obtained' => $marks,
                            'total_marks' => $exam->total_marks,
                            'grade' => $grade,
                            'grade_points' => $gradePoints,
                            'is_published' => $request->has('publish'),
                            'remarks' => $request->input("remarks.$studentId"),
                        ]
                    );
                }
            }

            DB::commit();
            
            $message = $request->has('publish') 
                ? 'Marks saved and published successfully.' 
                : 'Marks saved successfully. Remember to publish when ready.';

            return redirect()->route('teacher.exams.show', $exam->id)->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error saving marks: ' . $e->getMessage());
        }
    }

    private function calculateGrade($percentage)
    {
        if ($percentage >= 80) return 'A+';
        if ($percentage >= 75) return 'A';
        if ($percentage >= 70) return 'A-';
        if ($percentage >= 65) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 55) return 'B-';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 45) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    private function calculateGradePoints($percentage)
    {
        if ($percentage >= 80) return 4.00;
        if ($percentage >= 75) return 3.75;
        if ($percentage >= 70) return 3.50;
        if ($percentage >= 65) return 3.25;
        if ($percentage >= 60) return 3.00;
        if ($percentage >= 55) return 2.75;
        if ($percentage >= 50) return 2.50;
        if ($percentage >= 45) return 2.25;
        if ($percentage >= 40) return 2.00;
        return 0.00;
    }
}
