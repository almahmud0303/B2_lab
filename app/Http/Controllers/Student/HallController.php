<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get available halls
        $halls = Hall::where('is_available', true)->get();

        return view('student.halls.index', compact('halls'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Hall $hall)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        return view('student.halls.show', compact('hall'));
    }

    /**
     * Reserve a hall
     */
    public function reserve(Request $request, Hall $hall)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Check if hall is available
        if (!$hall->is_available) {
            return back()->with('error', 'This hall is not available for reservation.');
        }

        return back()->with('info', 'Hall reservation feature is currently under development.');
    }

    /**
     * Show reservation history
     */
    public function history()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        return back()->with('info', 'Hall reservation history feature is currently under development.');
    }
}