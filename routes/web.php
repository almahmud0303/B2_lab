<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\FeeController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\LibraryController;
use App\Http\Controllers\Staff\HallController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Debug route to check session state (remove in production)
Route::get('/debug-session', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->user() ? [
            'id' => auth()->user()->id,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'role' => auth()->user()->role,
        ] : null,
        'session_id' => session()->getId(),
    ]);
});

// Debug route to check dashboard routes (remove in production)
Route::get('/debug-routes', function () {
    return response()->json([
        'admin_dashboard' => route('admin.dashboard'),
        'student_dashboard' => route('student.dashboard'),
        'teacher_dashboard' => route('teacher.dashboard'),
        'staff_dashboard' => route('staff.dashboard'),
        'main_dashboard' => route('dashboard'),
    ]);
});

// Redirect to role-based dashboard
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if (!$user) {
        return redirect()->route('login')->with('error', 'Please log in to access the dashboard.');
    }
    
    // Debug: Log the user info
    \Log::info('Dashboard access attempt', [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'user_email' => $user->email,
        'user_role' => $user->role,
        'is_admin' => $user->isAdmin(),
        'is_teacher' => $user->isTeacher(),
        'is_student' => $user->isStudent(),
        'is_staff' => $user->isStaff(),
    ]);
    
    // Enhanced role checking with explicit string comparison
    try {
        if ($user->role === 'admin' || $user->isAdmin()) {
            \Log::info('Redirecting admin to admin dashboard');
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'teacher' || $user->isTeacher()) {
            \Log::info('Redirecting teacher to teacher dashboard');
            return redirect()->route('teacher.dashboard');
        } elseif ($user->role === 'student' || $user->isStudent()) {
            \Log::info('Redirecting student to student dashboard');
            return redirect()->route('student.dashboard');
        } elseif ($user->role === 'staff' || $user->isStaff()) {
            \Log::info('Redirecting staff to staff dashboard');
            return redirect()->route('staff.dashboard');
        }
    } catch (\Exception $e) {
        \Log::error('Error redirecting user to dashboard', [
            'error' => $e->getMessage(),
            'user' => $user->toArray()
        ]);
        return redirect()->route('login')->with('error', 'Error accessing dashboard. Please try again.');
    }
    
    // If no role matches, redirect to login
    \Log::warning('User has no valid role', ['user' => $user->toArray()]);
    return redirect()->route('login')->with('error', 'Invalid user role. Please contact administrator.');
})->middleware(['auth'])->name('dashboard');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin', 'prevent.back'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
    
    Route::resource('departments', DepartmentController::class);
    Route::post('departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
    
    Route::resource('teachers', TeacherController::class);
    Route::get('teachers/{teacher}/credentials', [App\Http\Controllers\Admin\TeacherController::class, 'credentials'])->name('teachers.credentials');
    Route::resource('students', StudentController::class);
    Route::get('students/{student}/credentials', [App\Http\Controllers\Admin\StudentController::class, 'credentials'])->name('students.credentials');
    
    // Course Management Routes
    Route::get('courses/organize', [App\Http\Controllers\Admin\CourseManagementController::class, 'organize'])->name('courses.organize');
    Route::post('courses/bulk-assign', [App\Http\Controllers\Admin\CourseManagementController::class, 'bulkAssign'])->name('courses.bulk-assign');
    Route::get('courses/department/{departmentId}', [App\Http\Controllers\Admin\CourseManagementController::class, 'getByDepartment'])->name('courses.by-department');
    Route::resource('courses', App\Http\Controllers\Admin\CourseManagementController::class);
    Route::resource('exams', ExamController::class);
    Route::resource('results', ResultController::class);
    Route::resource('fees', FeeController::class);
    Route::patch('fees/{fee}/mark-paid', [FeeController::class, 'markPaid'])->name('fees.mark-paid');
    Route::resource('books', BookController::class);
    Route::post('books/{book}/issue', [BookController::class, 'issueBook'])->name('books.issue');
    Route::patch('book-issues/{bookIssue}/return', [BookController::class, 'returnBook'])->name('book-issues.return');
    Route::resource('notices', NoticeController::class);
    Route::patch('notices/{notice}/publish', [NoticeController::class, 'publish'])->name('notices.publish');
    Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class);
    
    // Hall Management Routes
    Route::resource('halls', \App\Http\Controllers\Admin\HallController::class);
    Route::post('halls/{hall}/assign-student', [\App\Http\Controllers\Admin\HallController::class, 'assignStudent'])->name('halls.assign-student');
    Route::delete('students/{student}/remove-from-hall', [\App\Http\Controllers\Admin\HallController::class, 'removeStudent'])->name('halls.remove-student');
});

// Student Routes
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student', 'prevent.back', 'auto.enroll'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('profile.index');
        Route::get('/edit', [App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/change-password', [App\Http\Controllers\Student\ProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::put('/password', [App\Http\Controllers\Student\ProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::get('/academic', [App\Http\Controllers\Student\ProfileController::class, 'academic'])->name('profile.academic');
        Route::get('/transcript', [App\Http\Controllers\Student\ProfileController::class, 'transcript'])->name('profile.transcript');
        Route::get('/notifications', [App\Http\Controllers\Student\ProfileController::class, 'notifications'])->name('profile.notifications');
        Route::put('/notifications', [App\Http\Controllers\Student\ProfileController::class, 'updateNotifications'])->name('profile.update-notifications');
    });
    
    // Course routes
    Route::prefix('courses')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\CourseEnrollmentController::class, 'index'])->name('courses.index');
        Route::get('/catalog', [App\Http\Controllers\Student\CourseEnrollmentController::class, 'catalog'])->name('courses.catalog');
        Route::post('/{course}/enroll', [App\Http\Controllers\Student\CourseEnrollmentController::class, 'enroll'])->name('courses.enroll');
        Route::delete('/{course}/drop', [App\Http\Controllers\Student\CourseEnrollmentController::class, 'drop'])->name('courses.drop');
        Route::get('/{course}', [App\Http\Controllers\Student\CourseController::class, 'show'])->name('courses.show');
        Route::get('/schedule', [App\Http\Controllers\Student\CourseController::class, 'schedule'])->name('courses.schedule');
    });
    
    // Fee routes
    Route::prefix('fees')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\FeeController::class, 'index'])->name('fees.index');
        Route::get('/{fee}', [App\Http\Controllers\Student\FeeController::class, 'show'])->name('fees.show');
        Route::get('/{fee}/payment', [App\Http\Controllers\Student\FeeController::class, 'payment'])->name('fees.payment');
        Route::post('/{fee}/payment', [App\Http\Controllers\Student\FeeController::class, 'processPayment'])->name('fees.process-payment');
        Route::get('/{fee}/receipt', [App\Http\Controllers\Student\FeeController::class, 'receipt'])->name('fees.receipt');
        Route::get('/history', [App\Http\Controllers\Student\FeeController::class, 'history'])->name('fees.history');
    });
    
    // Exam routes
    Route::prefix('exams')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\ExamController::class, 'index'])->name('exams.index');
        Route::get('/{exam}', [App\Http\Controllers\Student\ExamController::class, 'show'])->name('exams.show');
        Route::get('/results', [App\Http\Controllers\Student\ExamController::class, 'results'])->name('exams.results');
        Route::get('/grade-sheet', [App\Http\Controllers\Student\ExamController::class, 'gradeSheet'])->name('exams.grade-sheet');
        Route::get('/calendar', [App\Http\Controllers\Student\ExamController::class, 'calendar'])->name('exams.calendar');
    });
    
    // Results routes
    Route::prefix('results')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\ResultController::class, 'index'])->name('results.index');
        Route::get('/{result}', [App\Http\Controllers\Student\ResultController::class, 'show'])->name('results.show');
    });
    
    // Library routes
    Route::prefix('library')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\LibraryController::class, 'index'])->name('library.index');
        Route::get('/search', [App\Http\Controllers\Student\LibraryController::class, 'search'])->name('library.search');
        Route::get('/books/{book}', [App\Http\Controllers\Student\LibraryController::class, 'show'])->name('library.show');
        Route::post('/books/{book}/request', [App\Http\Controllers\Student\LibraryController::class, 'requestBook'])->name('library.request-book');
        Route::get('/my-books', [App\Http\Controllers\Student\LibraryController::class, 'myBooks'])->name('library.my-books');
        Route::get('/history', [App\Http\Controllers\Student\LibraryController::class, 'history'])->name('library.history');
        Route::get('/rules', [App\Http\Controllers\Student\LibraryController::class, 'rules'])->name('library.rules');
        Route::post('/book-issues/{bookIssue}/return', [App\Http\Controllers\Student\LibraryController::class, 'returnBook'])->name('library.return-book');
        Route::post('/book-issues/{bookIssue}/renew', [App\Http\Controllers\Student\LibraryController::class, 'renewBook'])->name('library.renew-book');
        Route::get('/fines', [App\Http\Controllers\Student\LibraryController::class, 'fines'])->name('library.fines');
        Route::get('/categories', [App\Http\Controllers\Student\LibraryController::class, 'categories'])->name('library.categories');
        Route::get('/categories/{categoryId}/books', [App\Http\Controllers\Student\LibraryController::class, 'booksByCategory'])->name('library.books-by-category');
    });
    
    // Notice routes
    Route::prefix('notices')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\NoticeController::class, 'index'])->name('notices.index');
        Route::get('/{notice}', [App\Http\Controllers\Student\NoticeController::class, 'show'])->name('notices.show');
        Route::get('/type/{type}', [App\Http\Controllers\Student\NoticeController::class, 'byType'])->name('notices.by-type');
        Route::get('/urgent', [App\Http\Controllers\Student\NoticeController::class, 'urgent'])->name('notices.urgent');
        Route::get('/search', [App\Http\Controllers\Student\NoticeController::class, 'search'])->name('notices.search');
        Route::get('/{notice}/download', [App\Http\Controllers\Student\NoticeController::class, 'downloadAttachment'])->name('notices.download');
        Route::post('/{notice}/mark-read', [App\Http\Controllers\Student\NoticeController::class, 'markAsRead'])->name('notices.mark-read');
        Route::get('/unread-count', [App\Http\Controllers\Student\NoticeController::class, 'getUnreadCount'])->name('notices.unread-count');
    });
    
    // Report routes
    Route::prefix('reports')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\ReportController::class, 'index'])->name('reports.index');
        Route::get('/academic', [App\Http\Controllers\Student\ReportController::class, 'academicReport'])->name('reports.academic');
        Route::get('/transcript', [App\Http\Controllers\Student\ReportController::class, 'downloadTranscript'])->name('reports.transcript');
        Route::get('/grade-report', [App\Http\Controllers\Student\ReportController::class, 'gradeReport'])->name('reports.grade-report');
        Route::get('/grade-report/download', [App\Http\Controllers\Student\ReportController::class, 'downloadGradeReport'])->name('reports.grade-report-download');
        Route::get('/fee-report', [App\Http\Controllers\Student\ReportController::class, 'feeReport'])->name('reports.fee-report');
        Route::get('/fee-report/download', [App\Http\Controllers\Student\ReportController::class, 'downloadFeeReport'])->name('reports.fee-report-download');
        Route::get('/library-report', [App\Http\Controllers\Student\ReportController::class, 'libraryReport'])->name('reports.library-report');
        Route::get('/library-report/download', [App\Http\Controllers\Student\ReportController::class, 'downloadLibraryReport'])->name('reports.library-report-download');
        Route::get('/comprehensive', [App\Http\Controllers\Student\ReportController::class, 'comprehensiveReport'])->name('reports.comprehensive');
        Route::get('/comprehensive/download', [App\Http\Controllers\Student\ReportController::class, 'downloadComprehensiveReport'])->name('reports.comprehensive-download');
    });
    
    // Communication routes
    Route::prefix('communication')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\CommunicationController::class, 'index'])->name('communication.index');
        Route::get('/conversations', [App\Http\Controllers\Student\CommunicationController::class, 'conversations'])->name('communication.conversations');
        Route::get('/conversations/{userId}', [App\Http\Controllers\Student\CommunicationController::class, 'showConversation'])->name('communication.conversation');
        Route::post('/conversations/{userId}/send', [App\Http\Controllers\Student\CommunicationController::class, 'sendMessage'])->name('communication.send-message');
        Route::get('/new-conversation', [App\Http\Controllers\Student\CommunicationController::class, 'newConversation'])->name('communication.new-conversation');
        Route::post('/start-conversation', [App\Http\Controllers\Student\CommunicationController::class, 'startConversation'])->name('communication.start-conversation');
        Route::get('/notifications', [App\Http\Controllers\Student\CommunicationController::class, 'notifications'])->name('communication.notifications');
        Route::post('/notifications/{notificationId}/mark-read', [App\Http\Controllers\Student\CommunicationController::class, 'markNotificationAsRead'])->name('communication.mark-notification-read');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\Student\CommunicationController::class, 'markAllNotificationsAsRead'])->name('communication.mark-all-notifications-read');
        Route::get('/unread-count', [App\Http\Controllers\Student\CommunicationController::class, 'getUnreadCount'])->name('communication.unread-count');
        Route::get('/contact-teachers', [App\Http\Controllers\Student\CommunicationController::class, 'contactTeachers'])->name('communication.contact-teachers');
        Route::get('/contact-staff', [App\Http\Controllers\Student\CommunicationController::class, 'contactStaff'])->name('communication.contact-staff');
        Route::get('/contact-admin', [App\Http\Controllers\Student\CommunicationController::class, 'contactAdmin'])->name('communication.contact-admin');
        Route::get('/support', [App\Http\Controllers\Student\CommunicationController::class, 'support'])->name('communication.support');
        Route::post('/support', [App\Http\Controllers\Student\CommunicationController::class, 'submitSupport'])->name('communication.submit-support');
    });
    
    // Calendar routes
    Route::prefix('calendar')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\CalendarController::class, 'index'])->name('calendar.index');
        Route::get('/events', [App\Http\Controllers\Student\CalendarController::class, 'events'])->name('calendar.events');
        Route::get('/export', [App\Http\Controllers\Student\CalendarController::class, 'export'])->name('calendar.export');
    });
    
    // Hall/Hostel routes
    Route::prefix('halls')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\HallController::class, 'index'])->name('halls.index');
        Route::get('/history', [App\Http\Controllers\Student\HallController::class, 'history'])->name('halls.history');
        Route::get('/{hall}', [App\Http\Controllers\Student\HallController::class, 'show'])->name('halls.show');
        Route::post('/{hall}/reserve', [App\Http\Controllers\Student\HallController::class, 'reserve'])->name('halls.reserve');
        Route::get('/reservation/{reservation}', [App\Http\Controllers\Student\HallController::class, 'reservation'])->name('halls.reservation');
        Route::post('/reservation/{reservation}/cancel', [App\Http\Controllers\Student\HallController::class, 'cancel'])->name('halls.cancel');
    });
    
    // Info routes (Department, Teacher, Staff Information)
    Route::prefix('info')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\InfoController::class, 'index'])->name('info.index');
        Route::get('/departments', [App\Http\Controllers\Student\InfoController::class, 'departments'])->name('info.departments');
        Route::get('/departments/{department}', [App\Http\Controllers\Student\InfoController::class, 'departmentShow'])->name('info.department-show');
        Route::get('/teachers', [App\Http\Controllers\Student\InfoController::class, 'teachers'])->name('info.teachers');
        Route::get('/teachers/{teacher}', [App\Http\Controllers\Student\InfoController::class, 'teacherShow'])->name('info.teacher-show');
        Route::get('/staff', [App\Http\Controllers\Student\InfoController::class, 'staff'])->name('info.staff');
        Route::get('/staff/{staff}', [App\Http\Controllers\Student\InfoController::class, 'staffShow'])->name('info.staff-show');
        Route::get('/search', [App\Http\Controllers\Student\InfoController::class, 'search'])->name('info.search');
    });
    
    // Settings routes
    Route::prefix('settings')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/update-profile', [App\Http\Controllers\Student\SettingsController::class, 'updateProfile'])->name('settings.update-profile');
        Route::post('/update-password', [App\Http\Controllers\Student\SettingsController::class, 'updatePassword'])->name('settings.update-password');
        Route::post('/update-security', [App\Http\Controllers\Student\SettingsController::class, 'updateSecurity'])->name('settings.update-security');
        Route::post('/update-preferences', [App\Http\Controllers\Student\SettingsController::class, 'updatePreferences'])->name('settings.update-preferences');
        Route::post('/deactivate-account', [App\Http\Controllers\Student\SettingsController::class, 'deactivateAccount'])->name('settings.deactivate-account');
        Route::get('/export-data', [App\Http\Controllers\Student\SettingsController::class, 'exportData'])->name('settings.export-data');
        Route::get('/login-history', [App\Http\Controllers\Student\SettingsController::class, 'loginHistory'])->name('settings.login-history');
        Route::get('/connected-devices', [App\Http\Controllers\Student\SettingsController::class, 'connectedDevices'])->name('settings.connected-devices');
        Route::post('/connected-devices/{deviceId}/revoke', [App\Http\Controllers\Student\SettingsController::class, 'revokeDevice'])->name('settings.revoke-device');
    });
    
    // Support routes
    Route::prefix('support')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\SupportController::class, 'index'])->name('support.index');
        Route::post('/submit-ticket', [App\Http\Controllers\Student\SupportController::class, 'submitTicket'])->name('support.submit-ticket');
        Route::get('/knowledge-base', [App\Http\Controllers\Student\SupportController::class, 'knowledgeBase'])->name('support.knowledge-base');
        Route::get('/article/{id}', [App\Http\Controllers\Student\SupportController::class, 'showArticle'])->name('support.article');
        Route::get('/tutorials', [App\Http\Controllers\Student\SupportController::class, 'tutorials'])->name('support.tutorials');
        Route::get('/contact', [App\Http\Controllers\Student\SupportController::class, 'contact'])->name('support.contact');
        Route::post('/contact', [App\Http\Controllers\Student\SupportController::class, 'submitContact'])->name('support.submit-contact');
    });
    
    // Payment routes
    Route::prefix('payments')->group(function () {
        Route::get('/history', [PaymentController::class, 'history'])->name('payments.history');
        Route::get('/{paymentId}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('/course/{courseId}/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/course/{courseId}/store', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/{paymentId}/instructions', [PaymentController::class, 'instructions'])->name('payments.instructions');
        Route::get('/{paymentId}/success', [PaymentController::class, 'success'])->name('payments.success');
        Route::get('/{paymentId}/failed', [PaymentController::class, 'failed'])->name('payments.failed');
    });
});

// Teacher Routes
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher', 'prevent.back'])->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\ProfileController::class, 'index'])->name('profile.index');
        Route::get('/edit', [App\Http\Controllers\Teacher\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/change-password', [App\Http\Controllers\Teacher\ProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::put('/update-password', [App\Http\Controllers\Teacher\ProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::get('/academic', [App\Http\Controllers\Teacher\ProfileController::class, 'academic'])->name('academic');
    });
    
    // Course routes
    Route::prefix('courses')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\CourseController::class, 'index'])->name('courses.index');
        Route::get('/{course}', [App\Http\Controllers\Teacher\CourseController::class, 'show'])->name('courses.show');
    });
    
    
    // Exam routes
    Route::prefix('exams')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\ExamController::class, 'index'])->name('exams.index');
        Route::get('/create', [App\Http\Controllers\Teacher\ExamController::class, 'create'])->name('exams.create');
        Route::post('/store', [App\Http\Controllers\Teacher\ExamController::class, 'store'])->name('exams.store');
        Route::get('/{exam}', [App\Http\Controllers\Teacher\ExamController::class, 'show'])->name('exams.show');
        Route::get('/{exam}/edit', [App\Http\Controllers\Teacher\ExamController::class, 'edit'])->name('exams.edit');
        Route::put('/{exam}/update', [App\Http\Controllers\Teacher\ExamController::class, 'update'])->name('exams.update');
        Route::delete('/{exam}', [App\Http\Controllers\Teacher\ExamController::class, 'destroy'])->name('exams.destroy');
        Route::get('/{exam}/enter-marks', [App\Http\Controllers\Teacher\ExamController::class, 'enterMarks'])->name('exams.enter-marks');
        Route::post('/{exam}/save-marks', [App\Http\Controllers\Teacher\ExamController::class, 'saveMarks'])->name('exams.save-marks');
    });
    
    // Result routes
    Route::prefix('results')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\ResultController::class, 'index'])->name('results.index');
        Route::post('/{result}/publish', [App\Http\Controllers\Teacher\ResultController::class, 'publish'])->name('results.publish');
        Route::post('/{result}/unpublish', [App\Http\Controllers\Teacher\ResultController::class, 'unpublish'])->name('results.unpublish');
        Route::post('/bulk-publish', [App\Http\Controllers\Teacher\ResultController::class, 'bulkPublish'])->name('results.bulk-publish');
    });
    
    // Notice routes
    Route::prefix('notices')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\NoticeController::class, 'index'])->name('notices.index');
        Route::get('/{notice}', [App\Http\Controllers\Teacher\NoticeController::class, 'show'])->name('notices.show');
    });
});

// Staff Routes
Route::prefix('staff')->name('staff.')->middleware(['auth', 'role:staff', 'prevent.back'])->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [App\Http\Controllers\Staff\ProfileController::class, 'index'])->name('profile.index');
        Route::get('/edit', [App\Http\Controllers\Staff\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [App\Http\Controllers\Staff\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/change-password', [App\Http\Controllers\Staff\ProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::put('/update-password', [App\Http\Controllers\Staff\ProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::get('/academic', [App\Http\Controllers\Staff\ProfileController::class, 'academic'])->name('academic');
    });
    
    // Library routes
    Route::prefix('library')->group(function () {
        Route::get('/', [App\Http\Controllers\Staff\LibraryController::class, 'index'])->name('library.index');
        Route::get('/create', [App\Http\Controllers\Staff\LibraryController::class, 'create'])->name('library.create');
        Route::post('/store', [App\Http\Controllers\Staff\LibraryController::class, 'store'])->name('library.store');
        Route::get('/{book}', [App\Http\Controllers\Staff\LibraryController::class, 'show'])->name('library.show');
        Route::get('/{book}/edit', [App\Http\Controllers\Staff\LibraryController::class, 'edit'])->name('library.edit');
        Route::put('/{book}/update', [App\Http\Controllers\Staff\LibraryController::class, 'update'])->name('library.update');
        Route::delete('/{book}', [App\Http\Controllers\Staff\LibraryController::class, 'destroy'])->name('library.destroy');
    });
    
    // Book Issue routes
    Route::prefix('book-issues')->group(function () {
        Route::get('/', [App\Http\Controllers\Staff\BookIssueController::class, 'index'])->name('book-issues.index');
        Route::get('/create', [App\Http\Controllers\Staff\BookIssueController::class, 'create'])->name('book-issues.create');
        Route::post('/store', [App\Http\Controllers\Staff\BookIssueController::class, 'store'])->name('book-issues.store');
        Route::get('/{bookIssue}', [App\Http\Controllers\Staff\BookIssueController::class, 'show'])->name('book-issues.show');
        Route::post('/{bookIssue}/approve', [App\Http\Controllers\Staff\BookIssueController::class, 'approve'])->name('book-issues.approve');
        Route::post('/{bookIssue}/reject', [App\Http\Controllers\Staff\BookIssueController::class, 'reject'])->name('book-issues.reject');
        Route::post('/{bookIssue}/return', [App\Http\Controllers\Staff\BookIssueController::class, 'return'])->name('book-issues.return');
        Route::post('/{bookIssue}/renew', [App\Http\Controllers\Staff\BookIssueController::class, 'renew'])->name('book-issues.renew');
    });
    
    // Student routes
    Route::prefix('students')->group(function () {
        Route::get('/', [App\Http\Controllers\Staff\StudentController::class, 'index'])->name('students.index');
        Route::get('/{student}', [App\Http\Controllers\Staff\StudentController::class, 'show'])->name('students.show');
    });
    
    // Notice routes
    Route::prefix('notices')->group(function () {
        Route::get('/', [App\Http\Controllers\Staff\NoticeController::class, 'index'])->name('notices.index');
        Route::get('/{notice}', [App\Http\Controllers\Staff\NoticeController::class, 'show'])->name('notices.show');
    });
    
    // Halls routes (existing)
    Route::resource('halls', HallController::class);
    Route::patch('halls/{hall}/toggle-availability', [HallController::class, 'toggleAvailability'])->name('halls.toggle-availability');
});

// Department Head Routes (for teachers who are department heads)
Route::prefix('department-head')->name('department-head.')->middleware(['auth', 'prevent.back'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DepartmentHead\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes (using teacher profile controller)
    Route::prefix('profile')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\ProfileController::class, 'index'])->name('profile.index');
        Route::get('/edit', [App\Http\Controllers\Teacher\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/change-password', [App\Http\Controllers\Teacher\ProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::put('/update-password', [App\Http\Controllers\Teacher\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    });
    
    // Notice routes for department heads
    Route::prefix('notices')->group(function () {
        Route::get('/', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'index'])->name('notices.index');
        Route::get('/create', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'create'])->name('notices.create');
        Route::post('/', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'store'])->name('notices.store');
        Route::get('/{notice}', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'show'])->name('notices.show');
        Route::get('/{notice}/edit', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'edit'])->name('notices.edit');
        Route::put('/{notice}', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'update'])->name('notices.update');
        Route::delete('/{notice}', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'destroy'])->name('notices.destroy');
        Route::patch('/{notice}/toggle-status', [App\Http\Controllers\DepartmentHead\NoticeController::class, 'toggleStatus'])->name('notices.toggle-status');
    });
    
    // Course Assignment routes
    Route::prefix('course-assignment')->name('course-assignment.')->group(function () {
        Route::get('/', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'index'])->name('index');
        Route::get('/{course}/assign', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'assign'])->name('assign');
        Route::put('/{course}/update-assignment', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'updateAssignment'])->name('update-assignment');
        Route::delete('/{course}/unassign', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'unassign'])->name('unassign');
        Route::post('/bulk-assign', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'bulkAssign'])->name('bulk-assign');
        Route::get('/workload-report', [App\Http\Controllers\DepartmentHead\CourseAssignmentController::class, 'workloadReport'])->name('workload-report');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Payment callback routes (outside middleware for external access)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/bkash-test/{paymentId}', [PaymentController::class, 'bkashTest'])->name('payment.bkash-test');

// bKash API test route (for debugging)
Route::get('/test-bkash', [PaymentController::class, 'testBkash'])->name('test.bkash');

require __DIR__.'/auth.php';
