<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $query = Result::with(['exam.course', 'student.user'])
            ->whereHas('exam.course', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            });

        // Filter by publication status
        if ($request->has('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'unpublished') {
                $query->where('is_published', false);
            }
        }

        $results = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('teacher.results.index', compact('results', 'teacher'));
    }

    public function publish($id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $result = Result::with('exam.course')
            ->whereHas('exam.course', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->findOrFail($id);

        $result->update(['is_published' => true]);

        return redirect()->back()->with('success', 'Result published successfully.');
    }

    public function unpublish($id)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $result = Result::with('exam.course')
            ->whereHas('exam.course', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->findOrFail($id);

        $result->update(['is_published' => false]);

        return redirect()->back()->with('success', 'Result unpublished successfully.');
    }

    public function bulkPublish(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $exam = Exam::whereHas('course', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->findOrFail($request->exam_id);

        Result::where('exam_id', $exam->id)->update(['is_published' => true]);

        return redirect()->back()->with('success', 'All results for this exam published successfully.');
    }
}
