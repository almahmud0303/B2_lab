<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use App\Models\HallReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $halls = Hall::where('is_available', true)
            ->with('department')
            ->get();

        // Get student's current reservation
        $currentReservation = HallReservation::where('student_id', $student->id)
            ->where('status', 'active')
            ->with('hall')
            ->first();

        // Get reservation history
        $reservationHistory = HallReservation::where('student_id', $student->id)
            ->with('hall')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('student.halls.index', compact('halls', 'currentReservation', 'reservationHistory'));
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

        // Check if student has existing reservation
        $hasActiveReservation = HallReservation::where('student_id', $student->id)
            ->where('status', 'active')
            ->exists();

        // Get hall amenities
        $amenities = [
            'WiFi' => $hall->wifi_available,
            'Laundry' => $hall->laundry_available,
            'Kitchen' => $hall->kitchen_available,
            'Parking' => $hall->parking_available,
            'Security' => $hall->security_available,
        ];

        return view('student.halls.show', compact('hall', 'hasActiveReservation', 'amenities'));
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

        // Check if student already has an active reservation
        $existingReservation = HallReservation::where('student_id', $student->id)
            ->where('status', 'active')
            ->first();

        if ($existingReservation) {
            return back()->with('error', 'You already have an active hall reservation.');
        }

        $request->validate([
            'room_preference' => 'nullable|string|max:255',
            'special_requirements' => 'nullable|string|max:1000',
            'terms_accepted' => 'required|accepted',
        ]);

        try {
            DB::beginTransaction();

            // Create reservation
            $reservation = HallReservation::create([
                'student_id' => $student->id,
                'hall_id' => $hall->id,
                'room_preference' => $request->room_preference,
                'special_requirements' => $request->special_requirements,
                'status' => 'pending',
                'reservation_date' => now(),
            ]);

            DB::commit();

            return redirect()->route('student.halls.reservation', $reservation)
                ->with('success', 'Hall reservation submitted successfully! You will be notified once it\'s approved.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit reservation. Please try again.');
        }
    }

    /**
     * Show reservation details
     */
    public function reservation(HallReservation $reservation)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Ensure reservation belongs to the student
        if ($reservation->student_id !== $student->id) {
            abort(403, 'Unauthorized access to reservation.');
        }

        $reservation->load(['hall.department']);

        return view('student.halls.reservation', compact('reservation'));
    }

    /**
     * Cancel reservation
     */
    public function cancel(HallReservation $reservation)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Ensure reservation belongs to the student
        if ($reservation->student_id !== $student->id) {
            abort(403, 'Unauthorized access to reservation.');
        }

        // Check if reservation can be cancelled
        if (!in_array($reservation->status, ['pending', 'approved'])) {
            return back()->with('error', 'This reservation cannot be cancelled.');
        }

        try {
            $reservation->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            return redirect()->route('student.halls.index')
                ->with('success', 'Reservation cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel reservation. Please try again.');
        }
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

        $reservations = HallReservation::where('student_id', $student->id)
            ->with(['hall.department'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate statistics
        $stats = [
            'total_reservations' => $reservations->total(),
            'approved_reservations' => HallReservation::where('student_id', $student->id)
                ->where('status', 'approved')
                ->count(),
            'current_reservation' => HallReservation::where('student_id', $student->id)
                ->where('status', 'active')
                ->exists(),
        ];

        return view('student.halls.history', compact('reservations', 'stats'));
    }
}