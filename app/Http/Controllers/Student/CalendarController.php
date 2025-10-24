<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Notice;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get current month or requested month
        $currentMonth = $request->has('month') ? Carbon::parse($request->month) : now();
        
        // Get exams for the month
        $exams = Exam::whereHas('course.enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
        ->whereMonth('exam_date', $currentMonth->month)
        ->whereYear('exam_date', $currentMonth->year)
        ->with('course')
        ->orderBy('exam_date')
        ->get();

        // Get notices for the month
        $notices = Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'student')
                      ->orWhereJsonContains('target_roles', 'all')
                      ->orWhereNull('target_roles');
            })
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get general events (if Event model exists)
        $events = collect(); // Placeholder for events
        
        // Generate calendar days
        $calendarDays = $this->generateCalendarDays($currentMonth, $exams, $notices, $events);

        // Get upcoming events for the next 30 days
        $upcomingEvents = $this->getUpcomingEvents($student, 30);

        return view('student.calendar.index', compact(
            'currentMonth', 
            'calendarDays', 
            'upcomingEvents',
            'exams',
            'notices'
        ));
    }

    public function events(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $events = $this->getUpcomingEvents($student, $request->get('days', 30));

        return response()->json($events);
    }

    public function export(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $format = $request->get('format', 'ics');
        $events = $this->getUpcomingEvents($student, 365); // Export full year

        if ($format === 'ics') {
            return $this->exportICS($events);
        }

        return response()->json($events);
    }

    private function generateCalendarDays($currentMonth, $exams, $notices, $events)
    {
        $calendarDays = [];
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek();
        $endOfCalendar = $endOfMonth->copy()->endOfWeek();

        for ($date = $startOfCalendar->copy(); $date->lte($endOfCalendar); $date->addDay()) {
            $dayEvents = [];

            // Add exams for this day
            $dayExams = $exams->filter(function($exam) use ($date) {
                return $exam->exam_date->format('Y-m-d') === $date->format('Y-m-d');
            });
            
            foreach ($dayExams as $exam) {
                $dayEvents[] = [
                    'title' => $exam->title,
                    'type' => 'exam',
                    'course' => $exam->course->title,
                    'time' => $exam->start_time->format('H:i'),
                    'color' => 'red'
                ];
            }

            // Add notices for this day
            $dayNotices = $notices->filter(function($notice) use ($date) {
                return $notice->created_at->format('Y-m-d') === $date->format('Y-m-d');
            });
            
            foreach ($dayNotices as $notice) {
                $dayEvents[] = [
                    'title' => $notice->title,
                    'type' => 'notice',
                    'priority' => $notice->priority,
                    'color' => $notice->priority === 'high' ? 'orange' : 'blue'
                ];
            }

            $calendarDays[] = [
                'day' => $date->day,
                'date' => $date->copy(),
                'isCurrentMonth' => $date->month === $currentMonth->month,
                'isToday' => $date->isToday(),
                'events' => $dayEvents
            ];
        }

        return $calendarDays;
    }

    private function getUpcomingEvents($student, $days = 30)
    {
        $startDate = now();
        $endDate = now()->addDays($days);

        // Get upcoming exams
        $exams = Exam::whereHas('course.enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
        ->whereBetween('exam_date', [$startDate, $endDate])
        ->with('course')
        ->orderBy('exam_date')
        ->get();

        // Get recent notices
        $notices = Notice::where('is_published', true)
            ->where(function($query) {
                $query->whereJsonContains('target_roles', 'student')
                      ->orWhereJsonContains('target_roles', 'all')
                      ->orWhereNull('target_roles');
            })
            ->whereBetween('created_at', [$startDate->subDays(7), $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $events = collect();

        // Format exams
        foreach ($exams as $exam) {
            $events->push([
                'id' => 'exam_' . $exam->id,
                'title' => $exam->title,
                'type' => 'exam',
                'date' => $exam->exam_date->format('Y-m-d'),
                'time' => $exam->start_time->format('H:i'),
                'description' => $exam->description,
                'course' => $exam->course->title,
                'venue' => $exam->venue,
                'color' => 'red',
                'priority' => 'high'
            ]);
        }

        // Format notices
        foreach ($notices as $notice) {
            $events->push([
                'id' => 'notice_' . $notice->id,
                'title' => $notice->title,
                'type' => 'notice',
                'date' => $notice->created_at->format('Y-m-d'),
                'time' => $notice->created_at->format('H:i'),
                'description' => $notice->content,
                'priority' => $notice->priority,
                'color' => $notice->priority === 'high' ? 'orange' : 'blue'
            ]);
        }

        return $events->sortBy('date');
    }

    private function exportICS($events)
    {
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//UMS//Student Calendar//EN\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";

        foreach ($events as $event) {
            $ics .= "BEGIN:VEVENT\r\n";
            $ics .= "UID:" . $event['id'] . "@ums.com\r\n";
            $ics .= "DTSTART:" . $event['date'] . "T" . ($event['time'] ?? '00:00') . "00Z\r\n";
            $ics .= "DTEND:" . $event['date'] . "T" . ($event['time'] ?? '23:59') . "00Z\r\n";
            $ics .= "SUMMARY:" . $event['title'] . "\r\n";
            $ics .= "DESCRIPTION:" . ($event['description'] ?? '') . "\r\n";
            $ics .= "END:VEVENT\r\n";
        }

        $ics .= "END:VCALENDAR\r\n";

        return response($ics)
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'attachment; filename="student-calendar.ics"');
    }
}
