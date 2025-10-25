<?php

namespace App\Http\Controllers\DepartmentHead;

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
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        // Get exams for all courses in the department
        $exams = Exam::with('course.department')
            ->whereHas('course', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->orderBy('exam_date', 'desc')
            ->paginate(15);

        return view('department-head.exams.index', compact('exams', 'teacher', 'department'));
    }

    public function create()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        // Get all courses in the department
        $courses = Course::where('department_id', $department->id)
            ->where('is_active', true)
            ->with('teacher.user')
            ->get();

        return view('department-head.exams.create', compact('courses', 'teacher', 'department'));
    }

    public function store(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:quiz,assignment,midterm,final', // Department heads can create all types
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_marks' => 'required|integer|min:1',
            'venue' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        // Verify the course belongs to this department
        $course = Course::where('id', $request->course_id)
            ->where('department_id', $department->id)
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
        return redirect()->route('department-head.exams.index')->with('success', "$typeName created successfully.");
    }

    public function show($id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        $exam = Exam::with(['course.department', 'results.student.user'])
            ->whereHas('course', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->findOrFail($id);

        return view('department-head.exams.show', compact('exam', 'teacher', 'department'));
    }

    public function edit($id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        $exam = Exam::with('course')
            ->whereHas('course', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->findOrFail($id);

        $courses = Course::where('department_id', $department->id)
            ->where('is_active', true)
            ->with('teacher.user')
            ->get();

        return view('department-head.exams.edit', compact('exam', 'courses', 'teacher', 'department'));
    }

    public function update(Request $request, $id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        $exam = Exam::whereHas('course', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:quiz,assignment,midterm,final',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_marks' => 'required|integer|min:1',
            'venue' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        $exam->update($request->all());

        $typeName = ucfirst($exam->type);
        return redirect()->route('department-head.exams.index')->with('success', "$typeName updated successfully.");
    }

    public function destroy($id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        $exam = Exam::whereHas('course', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->findOrFail($id);

        // Can only delete if no results are entered
        if ($exam->results()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete exam with existing results.');
        }

        $exam->delete();

        return redirect()->route('department-head.exams.index')->with('success', 'Exam deleted successfully.');
    }

    public function enterMarks($id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        $exam = Exam::with(['course.enrollments.student.user', 'results'])
            ->whereHas('course', function($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->findOrFail($id);

        $students = $exam->course->enrollments()
            ->where('status', 'enrolled')
            ->with('student.user')
            ->get()
            ->pluck('student');

        // Get existing results
        $existingResults = $exam->results->keyBy('student_id');

        return view('department-head.exams.enter-marks', compact('exam', 'students', 'existingResults', 'teacher', 'department'));
    }

    public function saveMarks(Request $request, $id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher || !$teacher->is_department_head) {
            abort(403, 'You are not authorized as a department head.');
        }

        $department = $teacher->department;
        
        if (!$department) {
            abort(404, 'Department not found for this department head.');
        }

        $exam = Exam::whereHas('course', function($query) use ($department) {
                $query->where('department_id', $department->id);
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

            return redirect()->route('department-head.exams.show', $exam->id)->with('success', $message);
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
