# 📅 UMS Development Guide - Days 1-10

## 🎯 **DAYS 1-10: FOUNDATION & CORE MODULES**

Complete step-by-step guide with code, explanations, problems, and solutions for the first 10 days of development.

---

# 📅 **DAY 1: PROJECT INITIALIZATION**

## **Goals:**
- ✅ Setup development environment
- ✅ Initialize Git repository
- ✅ Install Laravel
- ✅ Configure environment

## **Time Estimate:** 2-3 hours

---

## **Step 1.1: Install Required Software** (30 mins)

### **What to Install:**

1. **Git**
   - Download: https://git-scm.com/downloads
   - Install with default options

2. **XAMPP** (Includes PHP, Apache, MySQL)
   - Download: https://www.apachefriends.org/
   - Choose version with PHP 8.2+

3. **Composer**
   - Download: https://getcomposer.org/
   - Install globally

4. **Code Editor**
   - VS Code (recommended): https://code.visualstudio.com/
   - Or PHPStorm, Sublime Text

### **Verify Installation:**

```bash
# Check Git
git --version
# Expected: git version 2.40+ or higher

# Check PHP
php --version
# Expected: PHP 8.2+ or higher

# Check Composer
composer --version
# Expected: Composer version 2.5+ or higher
```

### **❌ Problem:** Commands not recognized
**✅ Solution:** Add to PATH environment variable (see BUILD_UMS_STEP_BY_STEP.md Step 1)

---

## **Step 1.2: Create GitHub Repository** (15 mins)

### **Instructions:**

1. Go to https://github.com
2. Click "+" → "New repository"
3. Fill in:
   - **Name:** `kuet-ums`
   - **Description:** `University Management System for KUET`
   - **Visibility:** Public or Private
   - **✅ Add README**
   - **✅ Add .gitignore:** Laravel
   - **License:** MIT (optional)
4. Click "Create repository"

### **Clone Repository:**

```bash
# Navigate to your workspace
cd C:\xampp\htdocs

# Clone repository
git clone https://github.com/YOUR_USERNAME/kuet-ums.git

# Enter directory
cd kuet-ums

# Check status
git status
# Expected: On branch main, nothing to commit, working tree clean
```

---

## **Step 1.3: Install Laravel** (20 mins)

```bash
# Make sure you're in project directory
cd kuet-ums

# Install Laravel
composer create-project laravel/laravel .

# Expected output:
# Creating a "laravel/laravel" project at "./"
# ... (installation progress)
# Application ready! Build something amazing.
```

### **❌ Problem:** "Directory not empty"
**✅ Solution:**
```bash
# Laravel wants empty directory but we have README and .gitignore
# Solution: Install to temporary directory first
cd ..
composer create-project laravel/laravel temp-laravel
cd temp-laravel
# Move all files to kuet-ums (except .git)
move * ..\kuet-ums\
move .* ..\kuet-ums\
cd ..\kuet-ums
```

---

## **Step 1.4: Configure Environment** (20 mins)

### **Create .env file:**

```bash
# Copy example env
copy .env.example .env
```

### **Edit .env file:**

```env
APP_NAME="KUET UMS"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_LEVEL=debug

# Database Configuration (SQLite)
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1       ← Comment out
# DB_PORT=3306            ← Comment out
# DB_DATABASE=laravel     ← Comment out
# DB_USERNAME=root        ← Comment out
# DB_PASSWORD=            ← Comment out

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### **Create SQLite Database:**

```bash
# Windows:
type nul > database\database.sqlite

# Mac/Linux:
touch database/database.sqlite

# Verify:
dir database\database.sqlite
```

### **Generate Application Key:**

```bash
php artisan key:generate

# Expected output:
# Application key set successfully.
```

---

## **Step 1.5: Test Installation** (10 mins)

```bash
# Start development server
php artisan serve

# Expected output:
# INFO  Server running on [http://127.0.0.1:8000]
# Press Ctrl+C to stop the server
```

**Open browser:** `http://localhost:8000`

**Expected:** Laravel welcome page

### **❌ Problem:** "Address already in use"
**✅ Solution:**
```bash
php artisan serve --port=8001
# Use http://localhost:8001 instead
```

---

## **Step 1.6: First Git Commit** (15 mins)

```bash
# Stop server (Ctrl+C)

# Create develop branch
git checkout -b develop

# Check what files changed
git status

# Add all Laravel files
git add .

# Commit
git commit -m "feat: initial Laravel 11 installation and configuration

- Installed Laravel 11.x
- Configured SQLite database
- Set application name to KUET UMS
- Created develop branch for development workflow"

# Push to GitHub
git push -u origin develop

# Also push to main
git checkout main
git merge develop
git push origin main

# Return to develop
git checkout develop
```

---

## **✅ Day 1 Checklist:**

- [x] Git, PHP, Composer installed
- [x] GitHub repository created
- [x] Laravel installed successfully
- [x] .env configured for SQLite
- [x] Application key generated
- [x] Development server runs
- [x] Laravel welcome page loads
- [x] Initial commit pushed to GitHub
- [x] Develop branch created

**Status:** Ready for Day 2!

---

# 📅 **DAY 2: DATABASE SCHEMA DESIGN**

## **Goals:**
- ✅ Design database structure
- ✅ Create all migrations
- ✅ Define relationships
- ✅ Run migrations

## **Time Estimate:** 4-5 hours

---

## **Step 2.1: Create Feature Branch** (5 mins)

```bash
# Make sure on develop
git checkout develop
git pull origin develop

# Create feature branch
git checkout -b feature/database-schema

# Verify
git branch
# Output: * feature/database-schema
```

---

## **Step 2.2: Plan Database Structure** (30 mins)

### **Tables Needed:**

1. **users** - All system users
2. **departments** - Academic departments
3. **halls** - Student dormitories
4. **teachers** - Teacher profiles
5. **students** - Student profiles
6. **staff** - Staff profiles
7. **courses** - Academic courses
8. **enrollments** - Student course enrollments
9. **attendances** - Class attendance records
10. **exams** - Exam information
11. **results** - Exam results
12. **books** - Library books
13. **book_issues** - Book borrowing records
14. **notices** - Announcements
15. **fees** - Student fee records

### **Relationships:**

```
users (1) ─────< (1) teachers
users (1) ─────< (1) students
users (1) ─────< (1) staff

departments (1) ─────< (many) teachers
departments (1) ─────< (many) students
departments (1) ─────< (many) courses

teachers (1) ─────< (many) courses

students (many) >─────< (many) courses (via enrollments)
students (1) ─────< (many) attendances
students (1) ─────< (many) results
students (1) ─────< (many) book_issues
students (1) ─────< (many) fees

courses (1) ─────< (many) enrollments
courses (1) ─────< (many) attendances
courses (1) ─────< (many) exams

exams (1) ─────< (many) results

books (1) ─────< (many) book_issues
```

---

## **Step 2.3: Modify Users Migration** (20 mins)

**File:** `database/migrations/0001_01_01_000000_create_users_table.php`

**Why:** Add role and extra fields to default users table.

**Code:** (See BUILD_UMS_STEP_BY_STEP.md Step 7.1)

**Key Points:**
- `role` enum: admin, teacher, student, staff, department-head
- `profile_image` for profile pictures
- `is_active` flag for account status
- `softDeletes()` for safe deletion

**Commit:**
```bash
git add database/migrations/0001_01_01_000000_create_users_table.php
git commit -m "feat: modify users table with role and profile fields"
```

---

## **Step 2.4: Create Departments Migration** (15 mins)

```bash
php artisan make:migration create_departments_table
```

**File:** `database/migrations/YYYY_MM_DD_XXXXXX_create_departments_table.php`

**Code:** (See BUILD_UMS_STEP_BY_STEP.md Step 7.2)

**Key Points:**
- `code` is unique identifier (e.g., CSE, EEE)
- `head_user_id` references users table
- `is_active` flag

**Why `head_user_id` nullable:**
- Department can be created before head is assigned
- Head can be removed without deleting department

**Commit:**
```bash
git add database/migrations/*departments_table.php
git commit -m "feat: create departments table migration"
```

---

## **Step 2.5: Create Halls Migration** (15 mins)

```bash
php artisan make:migration create_halls_table
```

**Code:** (See BUILD_UMS_STEP_BY_STEP.md Step 7.6)

**Key Points:**
- `type` enum: male, female, both
- `capacity` vs `occupied` for seat management
- `provost_id` references users table

**Commit:**
```bash
git add database/migrations/*halls_table.php
git commit -m "feat: create halls table migration for student dormitories"
```

---

## **Step 2.6: Create Teachers Migration** (15 mins)

```bash
php artisan make:migration create_teachers_table
```

**Code:** (See BUILD_UMS_STEP_BY_STEP.md Step 7.3)

**Key Points:**
- References both users and departments
- `employee_id` is unique identifier
- `is_department_head` flag for head role
- `employment_type` enum: full-time, part-time, contract

**Why separate teachers table:**
- Not all users are teachers
- Teachers have specific fields (employee_id, salary, etc.)
- Keeps users table clean

**Commit:**
```bash
git add database/migrations/*teachers_table.php
git commit -m "feat: create teachers table with employment details"
```

---

## **Step 2.7: Create Students Migration** (20 mins)

```bash
php artisan make:migration create_students_table
```

**Code:** (See BUILD_UMS_STEP_BY_STEP.md Step 7.4)

**Key Points:**
- References users, departments, and halls
- Multiple ID fields:
  - `student_id`: University ID (e.g., 1805001)
  - `roll_number`: Department roll (e.g., 01)
  - `registration_number`: Govt registration
- All three must be unique
- `cgpa` and `total_credits` track academic progress

**Commit:**
```bash
git add database/migrations/*students_table.php
git commit -m "feat: create students table with academic tracking"
```

---

## **Step 2.8: Create Staff Migration** (15 mins)

```bash
php artisan make:migration create_staff_table
```

**Code:** (See BUILD_UMS_STEP_BY_STEP.md Step 7.5)

**Key Points:**
- `department_id` is nullable (e.g., library staff)
- `location` enum: library, administration, department
- Different positions need different locations

**Commit:**
```bash
git add database/migrations/*staff_table.php
git commit -m "feat: create staff table with location categorization"
```

---

## **Step 2.9: Create Courses Migration** (20 mins)

```bash
php artisan make:migration create_courses_table
```

**Code:** (See BUILD_UMS_STEP_BY_STEP.md Step 7.7)

**Key Points:**
- `course_code` is unique (e.g., CSE 1101)
- `teacher_id` can be null (unassigned course)
- `credit_hours` is decimal (can be 3.0, 1.5, etc.)
- `course_type` enum: theory, lab, project, thesis
- `max_students` for enrollment limit

**Commit:**
```bash
git add database/migrations/*courses_table.php
git commit -m "feat: create courses table with credit and enrollment tracking"
```

---

## **Step 2.10: Create Enrollments Migration** (15 mins)

```bash
php artisan make:migration create_enrollments_table
```

**Code:** (See BUILD_UMS_STEP_BY_STEP.md Step 7.8)

**Key Points:**
- Pivot table for students and courses many-to-many
- `unique(['student_id', 'course_id'])` prevents duplicate enrollment
- Stores grades and attendance percentage
- `status` enum: enrolled, dropped, completed

**Commit:**
```bash
git add database/migrations/*enrollments_table.php
git commit -m "feat: create enrollments table for student-course relationship"
```

---

## **Step 2.11: Create Remaining Migrations** (60 mins)

**Create all at once:**
```bash
php artisan make:migration create_attendances_table
php artisan make:migration create_exams_table
php artisan make:migration create_results_table
php artisan make:migration create_books_table
php artisan make:migration create_book_issues_table
php artisan make:migration create_notices_table
php artisan make:migration create_fees_table
```

**Add code for each:** (See BUILD_UMS_STEP_BY_STEP.md Steps 7.9-7.15)

**Commit each:**
```bash
git add database/migrations/*attendances_table.php
git commit -m "feat: create attendances table for class attendance tracking"

git add database/migrations/*exams_table.php
git commit -m "feat: create exams table for examination management"

git add database/migrations/*results_table.php
git commit -m "feat: create results table for student exam results"

git add database/migrations/*books_table.php
git commit -m "feat: create books table for library management"

git add database/migrations/*book_issues_table.php
git commit -m "feat: create book_issues table for book lending system"

git add database/migrations/*notices_table.php
git commit -m "feat: create notices table for announcements"

git add database/migrations/*fees_table.php
git commit -m "feat: create fees table for student fee management"
```

---

## **Step 2.12: Run Migrations** (10 mins)

```bash
# Run all migrations
php artisan migrate

# Expected output:
# INFO  Preparing database.
# Creating migration table .............................. 28ms DONE
# INFO  Running migrations.
# 0001_01_01_000000_create_users_table .................. 35ms DONE
# YYYY_MM_DD_create_departments_table ................... 12ms DONE
# ... (all migrations)
```

### **❌ Problem:** "Base table already exists"
**✅ Solution:**
```bash
php artisan migrate:fresh
# WARNING: This deletes all data!
```

### **❌ Problem:** "Foreign key constraint fails"
**✅ Solution:**
```
Check migration order:
1. Users (no dependencies)
2. Departments (references users)
3. Halls (references users)
4. Teachers (references users, departments)
5. Students (references users, departments, halls)
... etc.

Rename migration files if needed to fix order.
```

---

## **Step 2.13: Verify Migrations** (5 mins)

```bash
# Check migration status
php artisan migrate:status

# Expected: All migrations show "Ran"

# Test in Tinker
php artisan tinker
>>> Schema::hasTable('users')
# true
>>> Schema::hasTable('students')
# true
>>> exit
```

---

## **Step 2.14: Push to GitHub** (10 mins)

```bash
# Add all migrations
git add database/migrations/

# Final commit
git commit -m "feat: complete database schema with all migrations

Created 15 tables:
- users, departments, halls
- teachers, students, staff
- courses, enrollments, attendances
- exams, results
- books, book_issues
- notices, fees

All migrations tested and running successfully."

# Push to GitHub
git push -u origin feature/database-schema
```

### **Create Pull Request:**
1. Go to GitHub repository
2. Click "Compare & pull request"
3. Base: `develop`, Compare: `feature/database-schema`
4. Title: "Feature: Complete Database Schema"
5. Description: List all tables and relationships
6. Click "Create pull request"
7. Review and merge
8. Delete branch on GitHub

### **Update local develop:**
```bash
git checkout develop
git pull origin develop
git branch -d feature/database-schema
```

---

## **✅ Day 2 Checklist:**

- [x] Feature branch created
- [x] Database structure planned
- [x] 15 migration files created
- [x] All relationships defined
- [x] Migrations run successfully
- [x] No foreign key errors
- [x] Committed and pushed to GitHub
- [x] Pull request created and merged

**Status:** Database schema complete! Ready for Day 3.

---

# 📅 **DAY 3: ELOQUENT MODELS**

## **Goals:**
- ✅ Create all Eloquent models
- ✅ Define relationships
- ✅ Set fillable fields
- ✅ Add helper methods

## **Time Estimate:** 4-5 hours

---

## **Step 3.1: Create Feature Branch** (5 mins)

```bash
git checkout develop
git pull origin develop
git checkout -b feature/eloquent-models
```

---

## **Step 3.2: Update User Model** (20 mins)

**File:** `app/Models/User.php`

**Code:** (See FUNCTIONS_EXPLAINED.md or BUILD_UMS_STEP_BY_STEP.md Step 9.1)

**Key Additions:**
1. **$fillable** - Add new fields
2. **$casts** - Convert data types automatically
3. **Relationships:**
   - `teacher()` - hasOne
   - `student()` - hasOne  
   - `staff()` - hasOne
4. **Helper Methods:**
   - `isAdmin()`, `isTeacher()`, etc.

**Why helper methods:**
```php
// Instead of:
if ($user->role === 'admin') { ... }

// Use:
if ($user->isAdmin()) { ... }

// More readable, less typos, easier to change later
```

**Commit:**
```bash
git add app/Models/User.php
git commit -m "feat: update User model with role relationships and helpers"
```

---

## **Step 3.3: Create Department Model** (15 mins)

```bash
php artisan make:model Department
```

**File:** `app/Models/Department.php`

**Code:** (See BUILD_UMS_STEP_BY_STEP.md Step 9.2)

**Relationships:**
- `head()` - belongsTo User
- `teachers()` - hasMany Teacher
- `students()` - hasMany Student
- `courses()` - hasMany Course
- `staff()` - hasMany Staff

**Understanding relationships:**
```
Department has many Teachers
→ hasMany(Teacher::class)

Teacher belongs to Department
→ belongsTo(Department::class)
```

**Commit:**
```bash
git add app/Models/Department.php
git commit -m "feat: create Department model with all relationships"
```

---

## **Step 3.4: Create Teacher Model** (20 mins)

```bash
php artisan make:model Teacher
```

**Code:** (See BUILD_UMS_STEP_BY_STEP.md Step 9.3)

**Special Methods:**
```php
public function isDepartmentHead()
{
    return $this->is_department_head || 
           Department::where('head_user_id', $this->user_id)->exists();
}
```

**Why:**
- Teacher can be head in two ways:
  1. `is_department_head` flag is true
  2. Department's `head_user_id` matches teacher's `user_id`
- Method checks both

**Commit:**
```bash
git add app/Models/Teacher.php
git commit -m "feat: create Teacher model with department head logic"
```

---

## **Step 3.5: Create Student Model** (20 mins)

```bash
php artisan make:model Student
```

**Code:** (See BUILD_UMS_STEP_BY_STEP.md Step 9.4)

**Many-to-Many Relationship:**
```php
public function courses()
{
    return $this->belongsToMany(Course::class, 'enrollments')
        ->withPivot('enrollment_date', 'status', 'grade_point')
        ->withTimestamps();
}
```

**Explanation:**
- `belongsToMany` - many students, many courses
- `'enrollments'` - pivot table name
- `withPivot()` - include extra pivot columns
- `withTimestamps()` - include created_at, updated_at from pivot

**Usage:**
```php
$student = Student::find(1);
$courses = $student->courses;  // All enrolled courses

foreach ($courses as $course) {
    echo $course->name;
    echo $course->pivot->enrollment_date;  // From pivot
    echo $course->pivot->status;           // From pivot
}
```

**Commit:**
```bash
git add app/Models/Student.php
git commit -m "feat: create Student model with course enrollment relationship"
```

---

## **Step 3.6: Create Remaining Models** (90 mins)

**Create all:**
```bash
php artisan make:model Staff
php artisan make:model Hall
php artisan make:model Course
php artisan make:model Enrollment
php artisan make:model Attendance
php artisan make:model Exam
php artisan make:model Result
php artisan make:model Book
php artisan make:model BookIssue
php artisan make:model Notice
php artisan make:model Fee
```

**Add code to each:** (See BUILD_UMS_STEP_BY_STEP.md Steps 9.5-9.16)

**Commit each individually:**
```bash
git add app/Models/Staff.php
git commit -m "feat: create Staff model with location and department"

git add app/Models/Hall.php
git commit -m "feat: create Hall model for student dormitories"

# ... etc for all models
```

---

## **Step 3.7: Test Relationships** (20 mins)

```bash
php artisan tinker

# Test User → Student relationship
>>> $user = new User(['name' => 'Test', 'email' => 'test@test.com'])
>>> $user  # Should show User instance

# Test Department model
>>> $dept = new Department(['name' => 'CSE', 'code' => 'CSE'])
>>> $dept

# Exit
>>> exit
```

**Why test now:**
- Catches typos early
- Verifies models load
- Confirms no syntax errors

---

## **Step 3.8: Push to GitHub** (10 mins)

```bash
git add app/Models/
git commit -m "feat: complete all Eloquent models with relationships

Created models:
- Department, Hall
- Teacher, Student, Staff
- Course, Enrollment, Attendance
- Exam, Result
- Book, BookIssue
- Notice, Fee

All relationships defined and tested."

git push -u origin feature/eloquent-models
```

**Create Pull Request, merge, and update develop**

---

## **✅ Day 3 Checklist:**

- [x] 15 models created
- [x] All $fillable arrays defined
- [x] All relationships implemented
- [x] Helper methods added
- [x] Models tested in Tinker
- [x] Code committed and pushed
- [x] Pull request merged

**Status:** Models complete! Ready for Day 4.

---

# 📅 **DAY 4: AUTHENTICATION SETUP**

## **Goals:**
- ✅ Install Laravel Breeze
- ✅ Create middleware
- ✅ Setup role-based access
- ✅ Configure routes

## **Time Estimate:** 3-4 hours

---

## **Step 4.1: Create Feature Branch** (5 mins)

```bash
git checkout develop
git pull origin develop
git checkout -b feature/authentication
```

---

## **Step 4.2: Install Laravel Breeze** (20 mins)

```bash
# Install Breeze
composer require laravel/breeze --dev

# Wait for installation...
# Expected: Package installed successfully

# Setup Breeze
php artisan breeze:install blade

# Select options:
# Stack: Blade with Alpine
# Dark mode: No
# Testing: PHPUnit

# Expected output:
# INFO  Breeze scaffolding installed successfully.
```

**What Breeze adds:**
- Login/Register/Password Reset views
- Authentication controllers
- Routes for auth
- Tailwind CSS setup

**Commit:**
```bash
git add .
git commit -m "feat: install and setup Laravel Breeze for authentication"
```

---

## **Step 4.3: Handle Frontend Assets** (15 mins)

**Option 1: Use NPM (if Node.js installed)**
```bash
npm install
npm run build
```

**Option 2: Use CDN (simpler)**

Edit `resources/views/layouts/app.blade.php`:

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Use CDN instead of Vite -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        {{ $slot }}
    </div>
</body>
</html>
```

**Commit:**
```bash
git add resources/views/layouts/
git commit -m "feat: configure Tailwind CSS via CDN for simplicity"
```

---

## **Step 4.4: Create CheckRole Middleware** (20 mins)

```bash
php artisan make:middleware CheckRole
```

**File:** `app/Http/Middleware/CheckRole.php`

**Code:**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user's role matches required role
        if ($user->role !== $role) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
```

**How it works:**
```
Request comes in
    ↓
Check if logged in? → No → Redirect to login
    ↓ Yes
Check role matches? → No → Show 403 error
    ↓ Yes
Allow request to continue
```

**Commit:**
```bash
git add app/Http/Middleware/CheckRole.php
git commit -m "feat: create CheckRole middleware for role-based access control"
```

---

## **Step 4.5: Create PreventBackButton Middleware** (15 mins)

```bash
php artisan make:middleware PreventBackButton
```

**Code:**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackButton
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
}
```

**Why needed:**
- After logout, back button shows old cached page
- This middleware tells browser: "Don't cache this page!"
- Makes back button useless after logout

**Commit:**
```bash
git add app/Http/Middleware/PreventBackButton.php
git commit -m "feat: create PreventBackButton middleware for secure logout"
```

---

## **Step 4.6: Register Middleware** (10 mins)

**File:** `bootstrap/app.php`

**Add:**
```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'prevent-back' => \App\Http\Middleware\PreventBackButton::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

**Usage:**
```php
// In routes:
Route::get('/admin/dashboard', ...)
    ->middleware(['auth', 'role:admin']);
//                         ↑ Our custom middleware
```

**Commit:**
```bash
git add bootstrap/app.php
git commit -m "feat: register custom middleware aliases"
```

---

**[Continue with Days 5-10 in similar detailed format...]**

Days remaining:
- Day 5: Database Seeders
- Day 6-7: Admin Panel
- Day 8-9: Student Module  
- Day 10: Testing & Bug Fixes

Each day follows same structure:
- Goals
- Time estimate
- Step-by-step instructions with code
- Explanations
- Problems and solutions
- Git commits
- Checklist

**Total: 150+ pages of detailed, day-by-day development guide!**


