# 👨‍🏫 Days 10-12: Teacher Module - Complete Guide

## 📋 **OVERVIEW**

This guide provides complete code for implementing the Teacher Module (Days 10-12) following the same detailed structure as Days 1-9.

---

# 📅 **DAY 10: DATABASE SEEDING FOR TESTING**

## **Goals:**
- ✅ Create comprehensive seeders
- ✅ Generate test data for all modules
- ✅ Setup teacher and course data

## **Time Estimate:** 3-4 hours

---

## **STEP 42: Create Seeders** (60 mins)

```bash
php artisan make:seeder TeacherSeeder
php artisan make:seeder CourseSeeder
php artisan make:seeder StudentSeeder
php artisan make:seeder EnrollmentSeeder
```

---

## **STEP 43: Teacher Seeder** (30 mins)

**File:** `database/seeders/TeacherSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\{User, Teacher, Department};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();

        if ($departments->count() == 0) {
            $this->command->error('No departments found! Run DepartmentSeeder first.');
            return;
        }

        $teachers = [
            [
                'name' => 'Dr. Abdul Karim',
                'email' => 'karim@kuet.ac.bd',
                'department_code' => 'CSE',
                'designation' => 'Professor',
                'qualification' => 'PhD in Computer Science',
                'specialization' => 'Machine Learning, AI',
                'is_department_head' => true,
            ],
            [
                'name' => 'Dr. Fatema Rahman',
                'email' => 'fatema@kuet.ac.bd',
                'department_code' => 'CSE',
                'designation' => 'Associate Professor',
                'qualification' => 'PhD in Software Engineering',
                'specialization' => 'Software Architecture',
            ],
            [
                'name' => 'Md. Rafiq Ahmed',
                'email' => 'rafiq@kuet.ac.bd',
                'department_code' => 'CSE',
                'designation' => 'Assistant Professor',
                'qualification' => 'MSc in Computer Science',
                'specialization' => 'Database Systems',
            ],
            [
                'name' => 'Dr. Mahmuda Khatun',
                'email' => 'mahmuda@kuet.ac.bd',
                'department_code' => 'EEE',
                'designation' => 'Professor',
                'qualification' => 'PhD in Electrical Engineering',
                'specialization' => 'Power Systems',
                'is_department_head' => true,
            ],
            [
                'name' => 'Eng. Kamal Hossain',
                'email' => 'kamal@kuet.ac.bd',
                'department_code' => 'EEE',
                'designation' => 'Lecturer',
                'qualification' => 'MSc in Electrical Engineering',
                'specialization' => 'Electronics',
            ],
        ];

        foreach ($teachers as $index => $teacherData) {
            $department = Department::where('code', $teacherData['department_code'])->first();

            if (!$department) {
                $this->command->warn("Department {$teacherData['department_code']} not found! Skipping...");
                continue;
            }

            // Create user
            $user = User::create([
                'name' => $teacherData['name'],
                'email' => $teacherData['email'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'phone' => '01700' . str_pad($index + 100, 6, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            // Create teacher profile
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'department_id' => $department->id,
                'employee_id' => 'T-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'designation' => $teacherData['designation'],
                'qualification' => $teacherData['qualification'],
                'salary' => rand(50000, 150000),
                'joining_date' => now()->subYears(rand(1, 10)),
                'employment_type' => 'full-time',
                'specialization' => $teacherData['specialization'],
                'is_department_head' => $teacherData['is_department_head'] ?? false,
                'is_active' => true,
            ]);

            // Set as department head if specified
            if (isset($teacherData['is_department_head']) && $teacherData['is_department_head']) {
                $department->update(['head_user_id' => $user->id]);
            }

            $this->command->info("Created teacher: {$teacherData['name']} ({$teacherData['email']})");
        }
    }
}
```

---

## **STEP 44: Course Seeder** (45 mins)

**File:** `database/seeders/CourseSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\{Course, Department, Teacher};
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            // CSE Courses
            [
                'course_code' => 'CSE 1101',
                'course_name' => 'Computer Fundamentals',
                'department_code' => 'CSE',
                'credit_hours' => 3.0,
                'academic_year' => '1st Year',
                'semester' => '1st',
                'course_type' => 'theory',
                'description' => 'Introduction to computers and programming',
                'max_students' => 60,
            ],
            [
                'course_code' => 'CSE 1102',
                'course_name' => 'Computer Fundamentals Lab',
                'department_code' => 'CSE',
                'credit_hours' => 1.5,
                'academic_year' => '1st Year',
                'semester' => '1st',
                'course_type' => 'lab',
                'description' => 'Practical programming exercises',
                'max_students' => 30,
            ],
            [
                'course_code' => 'CSE 1201',
                'course_name' => 'Data Structures',
                'department_code' => 'CSE',
                'credit_hours' => 3.0,
                'academic_year' => '1st Year',
                'semester' => '2nd',
                'course_type' => 'theory',
                'description' => 'Study of data structures and algorithms',
                'max_students' => 60,
            ],
            [
                'course_code' => 'CSE 2101',
                'course_name' => 'Object Oriented Programming',
                'department_code' => 'CSE',
                'credit_hours' => 3.0,
                'academic_year' => '2nd Year',
                'semester' => '1st',
                'course_type' => 'theory',
                'description' => 'OOP concepts and implementation',
                'max_students' => 60,
            ],
            [
                'course_code' => 'CSE 2201',
                'course_name' => 'Database Management Systems',
                'department_code' => 'CSE',
                'credit_hours' => 3.0,
                'academic_year' => '2nd Year',
                'semester' => '2nd',
                'course_type' => 'theory',
                'description' => 'Database design and SQL',
                'max_students' => 60,
            ],
            // EEE Courses
            [
                'course_code' => 'EEE 1101',
                'course_name' => 'Electrical Circuits',
                'department_code' => 'EEE',
                'credit_hours' => 3.0,
                'academic_year' => '1st Year',
                'semester' => '1st',
                'course_type' => 'theory',
                'description' => 'Basic electrical circuit analysis',
                'max_students' => 60,
            ],
            [
                'course_code' => 'EEE 1201',
                'course_name' => 'Electronics',
                'department_code' => 'EEE',
                'credit_hours' => 3.0,
                'academic_year' => '1st Year',
                'semester' => '2nd',
                'course_type' => 'theory',
                'description' => 'Electronic devices and circuits',
                'max_students' => 60,
            ],
        ];

        foreach ($courses as $courseData) {
            $department = Department::where('code', $courseData['department_code'])->first();

            if (!$department) {
                $this->command->warn("Department {$courseData['department_code']} not found! Skipping...");
                continue;
            }

            // Assign a random teacher from the department
            $teacher = Teacher::where('department_id', $department->id)
                ->where('is_active', true)
                ->inRandomOrder()
                ->first();

            Course::create([
                'course_code' => $courseData['course_code'],
                'course_name' => $courseData['course_name'],
                'department_id' => $department->id,
                'teacher_id' => $teacher?->id,
                'credit_hours' => $courseData['credit_hours'],
                'academic_year' => $courseData['academic_year'],
                'semester' => $courseData['semester'],
                'course_type' => $courseData['course_type'],
                'description' => $courseData['description'],
                'max_students' => $courseData['max_students'],
                'is_active' => true,
            ]);

            $this->command->info("Created course: {$courseData['course_code']} - {$courseData['course_name']}");
        }
    }
}
```

---

## **STEP 45: Enhanced Student Seeder** (45 mins)

**File:** `database/seeders/StudentSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\{User, Student, Department, Hall};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();
        $halls = Hall::all();

        if ($departments->count() == 0) {
            $this->command->error('No departments found! Run DepartmentSeeder first.');
            return;
        }

        // Create 20 students
        for ($i = 1; $i <= 20; $i++) {
            $department = $departments->random();
            $hall = $halls->count() > 0 ? $halls->random() : null;

            // Create user
            $user = User::create([
                'name' => 'Student ' . $i,
                'email' => 'student' . $i . '@kuet.ac.bd',
                'password' => Hash::make('password'),
                'role' => 'student',
                'phone' => '01800' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            // Generate student IDs
            $year = date('y');
            $deptCode = substr($department->code, 0, 2);
            
            Student::create([
                'user_id' => $user->id,
                'department_id' => $department->id,
                'student_id' => $year . $deptCode . str_pad($i, 3, '0', STR_PAD_LEFT),
                'roll_number' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'registration_number' => 'REG-' . $year . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'session' => '2024-25',
                'academic_year' => ['1st Year', '2nd Year', '3rd Year', '4th Year'][rand(0, 3)],
                'semester' => ['1st', '2nd'][rand(0, 1)],
                'admission_date' => now()->subMonths(rand(1, 48)),
                'hall_id' => $hall?->id,
                'blood_group' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'][rand(0, 7)],
                'cgpa' => round(rand(250, 400) / 100, 2),
                'total_credits' => rand(0, 120),
                'is_active' => true,
            ]);

            $this->command->info("Created student: Student $i (student{$i}@kuet.ac.bd)");
        }
    }
}
```

---

## **STEP 46: Update DatabaseSeeder** (10 mins)

**File:** `database/seeders/DatabaseSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            HallSeeder::class,
            AdminSeeder::class,
            TeacherSeeder::class,
            CourseSeeder::class,
            StudentSeeder::class,
        ]);
    }
}
```

---

## **STEP 47: Run Seeders** (10 mins)

```bash
# Fresh database with seeders
php artisan migrate:fresh --seed
```

**✅ EXPECTED OUTPUT:**
```
Dropping all tables ................................................. DONE
Running migrations .................................................. DONE
Seeding database
  Database\Seeders\DepartmentSeeder ................................ DONE
  Database\Seeders\HallSeeder ...................................... DONE
  Database\Seeders\AdminSeeder ...................................... DONE
  Database\Seeders\TeacherSeeder .................................... DONE
  Database\Seeders\CourseSeeder ..................................... DONE
  Database\Seeders\StudentSeeder .................................... DONE
```

---

## **STEP 48: Test Login with New Users** (15 mins)

```bash
php artisan serve

# Test logins:
# Admin: admin@kuet.ac.bd / password
# Teacher: karim@kuet.ac.bd / password
# Student: student1@kuet.ac.bd / password
```

---

# 📅 **DAY 11: TEACHER MODULE - SETUP & DASHBOARD**

## **Goals:**
- ✅ Create teacher controllers
- ✅ Build teacher dashboard
- ✅ Create teacher layout

## **Time Estimate:** 5-6 hours

---

## **STEP 49: Verify Git Status** (1 min)

```bash
# Make sure you're on main branch
git branch
# Should show: * main

# Check you're up to date
git status
```

**Note:** We work directly on main branch - no feature branches needed!

---

## **STEP 50: Create Teacher Controllers** (15 mins)

```bash
php artisan make:controller Teacher/DashboardController
php artisan make:controller Teacher/ProfileController
php artisan make:controller Teacher/CourseController
php artisan make:controller Teacher/AttendanceController
php artisan make:controller Teacher/ExamController --resource
php artisan make:controller Teacher/ResultController
```

---

## **STEP 51: Teacher Dashboard Controller** (45 mins)

**File:** `app/Http/Controllers/Teacher/DashboardController.php`

```php
<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\{Course, Attendance, Exam};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            abort(404, 'Teacher profile not found');
        }

        // Course statistics
        $totalCourses = $teacher->courses()->count();
        $activeCourses = $teacher->courses()
            ->where('is_active', true)
            ->count();

        // Get courses with student count
        $courseStats = collect();
        
        $courses = $teacher->courses()
            ->withCount('enrollments')
            ->with('department')
            ->where('is_active', true)
            ->get();

        foreach ($courses as $course) {
            $courseStats->push([
                'course' => $course,
                'enrolled_count' => $course->enrollments_count,
            ]);
        }

        // Calculate total students
        $totalStudents = $courses->sum('enrollments_count');

        // Upcoming exams
        $upcomingExams = Exam::whereIn('course_id', $courses->pluck('id'))
            ->where('exam_date', '>=', now())
            ->with('course')
            ->orderBy('exam_date', 'asc')
            ->take(5)
            ->get();

        // Recent attendance
        $recentAttendance = Attendance::whereIn('course_id', $courses->pluck('id'))
            ->with(['course', 'student.user'])
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        return view('teacher.dashboard', compact(
            'teacher',
            'totalCourses',
            'activeCourses',
            'totalStudents',
            'courseStats',
            'upcomingExams',
            'recentAttendance'
        ));
    }
}
```

---

## **STEP 52: Create Teacher Routes** (20 mins)

**File:** `routes/web.php`

Add after student routes:

```php
// Teacher Routes
Route::middleware(['auth', 'role:teacher', 'prevent-back'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\Teacher\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\Teacher\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('profile.update');
    
    // Course routes
    Route::get('/courses', [App\Http\Controllers\Teacher\CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [App\Http\Controllers\Teacher\CourseController::class, 'show'])->name('courses.show');
    
    // Attendance routes
    Route::get('/attendance', [App\Http\Controllers\Teacher\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/{course}/mark', [App\Http\Controllers\Teacher\AttendanceController::class, 'mark'])->name('attendance.mark');
    Route::post('/attendance', [App\Http\Controllers\Teacher\AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/{course}/report', [App\Http\Controllers\Teacher\AttendanceController::class, 'report'])->name('attendance.report');
    
    // Exam routes
    Route::resource('exams', App\Http\Controllers\Teacher\ExamController::class);
    
    // Result routes
    Route::get('/results/{exam}/enter-marks', [App\Http\Controllers\Teacher\ResultController::class, 'enterMarks'])->name('results.enter-marks');
    Route::post('/results/save-marks', [App\Http\Controllers\Teacher\ResultController::class, 'saveMarks'])->name('results.save-marks');
    Route::post('/results/{exam}/publish', [App\Http\Controllers\Teacher\ResultController::class, 'publish'])->name('results.publish');
    Route::post('/results/{exam}/unpublish', [App\Http\Controllers\Teacher\ResultController::class, 'unpublish'])->name('results.unpublish');
});
```

---

**Continue to DAYS_13_15_STAFF_DEPTHEAD.md for more...**

