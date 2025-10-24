<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use Illuminate\Http\Request;

class HallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $halls = Hall::latest()->paginate(15);
        return view('staff.halls.index', compact('halls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('staff.halls.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|max:255',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
        ]);

        Hall::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'location' => $request->location,
            'facilities' => $request->facilities ?? [],
            'is_available' => $request->has('is_available'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('staff.halls.index')->with('success', 'Hall created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hall $hall)
    {
        return view('staff.halls.show', compact('hall'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hall $hall)
    {
        return view('staff.halls.edit', compact('hall'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hall $hall)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|max:255',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $hall->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'location' => $request->location,
            'facilities' => $request->facilities ?? [],
            'is_available' => $request->has('is_available'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('staff.halls.index')->with('success', 'Hall updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hall $hall)
    {
        $hall->delete();
        return redirect()->route('staff.halls.index')->with('success', 'Hall deleted successfully.');
    }

    /**
     * Toggle the availability status of the hall.
     */
    public function toggleAvailability(Hall $hall)
    {
        $hall->is_available = !$hall->is_available;
        $hall->save();

        $status = $hall->is_available ? 'available' : 'unavailable';
        return back()->with('success', "Hall marked as {$status} successfully.");
    }
}
