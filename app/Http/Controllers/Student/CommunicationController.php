<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;

class CommunicationController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get recent conversations (placeholder - you might want to create a messages table)
        $recentConversations = collect([
            [
                'id' => 1,
                'user' => User::where('role', 'teacher')->first(),
                'last_message' => 'Please submit your assignment by Friday',
                'last_message_time' => now()->subHours(2),
                'unread_count' => 2,
            ],
            [
                'id' => 2,
                'user' => User::where('role', 'admin')->first(),
                'last_message' => 'Your fee payment has been received',
                'last_message_time' => now()->subDays(1),
                'unread_count' => 0,
            ],
        ]);

        // Get system notifications
        $notifications = collect([
            [
                'id' => 1,
                'title' => 'New Result Published',
                'message' => 'Your result for Data Structures has been published',
                'type' => 'result',
                'created_at' => now()->subHours(1),
                'is_read' => false,
            ],
            [
                'id' => 2,
                'title' => 'Fee Payment Reminder',
                'message' => 'Your semester fee is due in 3 days',
                'type' => 'fee',
                'created_at' => now()->subDays(2),
                'is_read' => false,
            ],
        ]);

        return view('student.communication.index', compact(
            'recentConversations',
            'notifications'
        ));
    }

    public function conversations()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get all conversations (placeholder)
        $conversations = collect([
            [
                'id' => 1,
                'user' => User::where('role', 'teacher')->first(),
                'last_message' => 'Please submit your assignment by Friday',
                'last_message_time' => now()->subHours(2),
                'unread_count' => 2,
                'status' => 'active',
            ],
            [
                'id' => 2,
                'user' => User::where('role', 'admin')->first(),
                'last_message' => 'Your fee payment has been received',
                'last_message_time' => now()->subDays(1),
                'unread_count' => 0,
                'status' => 'active',
            ],
        ]);

        return view('student.communication.conversations', compact('conversations'));
    }

    public function showConversation($userId)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $otherUser = User::findOrFail($userId);
        
        // Check if user can communicate with this person
        if (!$this->canCommunicateWith($student, $otherUser)) {
            abort(403, 'You cannot communicate with this user');
        }

        // Get conversation messages (placeholder)
        $messages = collect([
            [
                'id' => 1,
                'from_user_id' => $otherUser->id,
                'to_user_id' => $student->user_id,
                'message' => 'Hello, I wanted to discuss your recent assignment submission.',
                'created_at' => now()->subHours(3),
                'is_read' => true,
            ],
            [
                'id' => 2,
                'from_user_id' => $student->user_id,
                'to_user_id' => $otherUser->id,
                'message' => 'Thank you for reaching out. I submitted it yesterday.',
                'created_at' => now()->subHours(2),
                'is_read' => true,
            ],
            [
                'id' => 3,
                'from_user_id' => $otherUser->id,
                'to_user_id' => $student->user_id,
                'message' => 'Great! I will review it and get back to you.',
                'created_at' => now()->subHours(1),
                'is_read' => false,
            ],
        ]);

        return view('student.communication.conversation', compact('otherUser', 'messages'));
    }

    public function sendMessage(Request $request, $userId)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $request->validate([
            'message' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $otherUser = User::findOrFail($userId);
        
        // Check if user can communicate with this person
        if (!$this->canCommunicateWith($student, $otherUser)) {
            abort(403, 'You cannot communicate with this user');
        }

        // In a real implementation, you would:
        // 1. Create a message record
        // 2. Handle file uploads
        // 3. Send notifications
        // 4. Update conversation status

        // For now, return success
        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully'
        ]);
    }

    public function newConversation()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get available contacts (teachers, admins, staff)
        $teachers = Teacher::with('user')
            ->where('department_id', $student->department_id)
            ->get();

        $admins = User::where('role', 'admin')->get();
        $staff = User::where('role', 'staff')->get();

        return view('student.communication.new-conversation', compact(
            'teachers',
            'admins',
            'staff'
        ));
    }

    public function startConversation(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        $otherUser = User::findOrFail($request->user_id);
        
        // Check if user can communicate with this person
        if (!$this->canCommunicateWith($student, $otherUser)) {
            abort(403, 'You cannot communicate with this user');
        }

        // In a real implementation, you would:
        // 1. Create a conversation record
        // 2. Create the first message
        // 3. Send notifications

        return redirect()->route('student.communication.conversation', $otherUser->id)
            ->with('success', 'Conversation started successfully');
    }

    public function notifications()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get system notifications (placeholder)
        $notifications = collect([
            [
                'id' => 1,
                'title' => 'New Result Published',
                'message' => 'Your result for Data Structures has been published',
                'type' => 'result',
                'created_at' => now()->subHours(1),
                'is_read' => false,
                'data' => ['exam_id' => 1, 'course_id' => 1],
            ],
            [
                'id' => 2,
                'title' => 'Fee Payment Reminder',
                'message' => 'Your semester fee is due in 3 days',
                'type' => 'fee',
                'created_at' => now()->subDays(2),
                'is_read' => false,
                'data' => ['fee_id' => 1],
            ],
            [
                'id' => 3,
                'title' => 'Library Book Due',
                'message' => 'Your book "Introduction to Algorithms" is due tomorrow',
                'type' => 'library',
                'created_at' => now()->subDays(3),
                'is_read' => true,
                'data' => ['book_id' => 1],
            ],
        ]);

        return view('student.communication.notifications', compact('notifications'));
    }

    public function markNotificationAsRead($notificationId)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // In a real implementation, you would:
        // 1. Find the notification
        // 2. Mark it as read
        // 3. Update the database

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsAsRead()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // In a real implementation, you would:
        // 1. Mark all notifications as read for this student
        // 2. Update the database

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return response()->json(['count' => 0]);
        }

        // In a real implementation, you would count unread notifications
        // For now, return a placeholder
        return response()->json(['count' => 2]);
    }

    public function contactTeachers()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get teachers from student's department
        $teachers = Teacher::with(['user', 'courses'])
            ->where('department_id', $student->department_id)
            ->get();

        // Get teachers from enrolled courses
        $enrolledTeachers = Teacher::with(['user', 'courses'])
            ->whereHas('courses.enrollments', function($query) use ($student) {
                $query->where('student_id', $student->id)
                      ->where('status', 'enrolled');
            })
            ->get();

        $allTeachers = $teachers->merge($enrolledTeachers)->unique('id');

        return view('student.communication.contact-teachers', compact('allTeachers'));
    }

    public function contactStaff()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get staff members
        $staff = User::where('role', 'staff')->get();

        return view('student.communication.contact-staff', compact('staff'));
    }

    public function contactAdmin()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get admin users
        $admins = User::where('role', 'admin')->get();

        return view('student.communication.contact-admin', compact('admins'));
    }

    public function support()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get support categories
        $supportCategories = [
            'academic' => 'Academic Issues',
            'technical' => 'Technical Support',
            'fee' => 'Fee Related',
            'library' => 'Library Services',
            'general' => 'General Inquiry',
        ];

        return view('student.communication.support', compact('supportCategories'));
    }

    public function submitSupport(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $request->validate([
            'category' => 'required|in:academic,technical,fee,library,general',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        // In a real implementation, you would:
        // 1. Create a support ticket
        // 2. Send notification to admin/staff
        // 3. Store the ticket in database

        return redirect()->route('student.communication.support')
            ->with('success', 'Support ticket submitted successfully');
    }

    private function canCommunicateWith($student, $otherUser)
    {
        // Students can communicate with:
        // - Teachers from their department
        // - Teachers of their enrolled courses
        // - Admin users
        // - Staff users

        if ($otherUser->role === 'admin' || $otherUser->role === 'staff') {
            return true;
        }

        if ($otherUser->role === 'teacher') {
            $teacher = $otherUser->teacher;
            
            if (!$teacher) {
                return false;
            }

            // Check if teacher is from same department
            if ($teacher->department_id === $student->department_id) {
                return true;
            }

            // Check if teacher teaches any enrolled course
            $hasEnrolledCourse = $student->enrollments()
                ->whereHas('course', function($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                })
                ->where('status', 'enrolled')
                ->exists();

            return $hasEnrolledCourse;
        }

        return false;
    }
}
