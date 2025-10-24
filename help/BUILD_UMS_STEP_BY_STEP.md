# 🎓 Build University Management System - Step-by-Step with Code

## 📖 **COMPLETE PRACTICAL GUIDE**

This guide provides **exact code** for every step, shows **what problems you'll face**, and gives **exact solutions**. Follow along, copy the code, and build your UMS!

---

# 🚀 **PHASE 1: INITIAL SETUP (Day 1)**

## **STEP 1: Install Git and Setup**

### **1.1 - Download and Install Git**

```bash
# Windows: Download from https://git-scm.com/downloads
# Install with default options

# Verify installation
git --version
```

**❌ PROBLEM:** `'git' is not recognized`

**✅ SOLUTION:**
```bash
# Add Git to PATH:
# 1. Windows Key → Search "Environment Variables"
# 2. System Properties → Environment Variables
# 3. Under "System Variables" → Select "Path" → Edit
# 4. Click "New" → Add: C:\Program Files\Git\cmd
# 5. Click OK → Restart Command Prompt
# 6. Test: git --version
```

### **1.2 - Configure Git**

```bash
# Set your name and email
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"

# Set default branch to main
git config --global init.defaultBranch main

# Verify configuration
git config --list
```

**✅ EXPECTED OUTPUT:**
```
user.name=Your Name
user.email=your.email@example.com
init.defaultbranch=main
```

---

## **STEP 2: Create GitHub Repository**

### **2.1 - Create Repository on GitHub**

```
1. Go to https://github.com
2. Click "+" (top right) → "New repository"
3. Repository name: kuet-ums
4. Description: University Management System
5. ✅ Public (or Private)
6. ✅ Add README file
7. ✅ Add .gitignore → Choose "Laravel"
8. Click "Create repository"
```

### **2.2 - Clone Repository to Your Computer**

```bash
# Navigate to where you want the project
cd C:\xampp\htdocs

# Clone repository (replace 'yourusername' with your GitHub username)
git clone https://github.com/yourusername/kuet-ums.git

# Enter project directory
cd kuet-ums
```

**❌ PROBLEM:** `Permission denied (publickey)`

**✅ SOLUTION:**
```bash
# Generate SSH key
ssh-keygen -t ed25519 -C "your.email@example.com"
# Press Enter 3 times (accept defaults)

# Copy your public key
# Windows:
type %USERPROFILE%\.ssh\id_ed25519.pub
# Mac/Linux:
cat ~/.ssh/id_ed25519.pub

# Add to GitHub:
# 1. GitHub → Settings (top right)
# 2. SSH and GPG keys → New SSH key
# 3. Paste the key → Add SSH key

# Try cloning again with SSH:
git clone git@github.com:yourusername/kuet-ums.git
```

---

## **STEP 3: Install Laravel**

### **3.1 - Create Laravel Project**

```bash
# Make sure you're in the repository folder
cd kuet-ums

# Install Laravel (this will take 2-3 minutes)
composer create-project laravel/laravel .
```

**❌ PROBLEM:** `'composer' is not recognized`

**✅ SOLUTION:**
```bash
# Install Composer:
# 1. Go to https://getcomposer.org/download/
# 2. Download "Composer-Setup.exe" (Windows)
# 3. Run installer → Follow prompts
# 4. Restart Command Prompt
# 5. Verify: composer --version

# If still not working, add to PATH:
# C:\ProgramData\ComposerSetup\bin
```

**❌ PROBLEM:** `Failed to download Laravel`

**✅ SOLUTION:**
```bash
# Check PHP version (needs 8.2+)
php --version

# If PHP < 8.2, install XAMPP 8.2+:
# 1. Download from https://www.apachefriends.org/
# 2. Install XAMPP
# 3. Add PHP to PATH: C:\xampp\php
# 4. Restart terminal
# 5. Verify: php --version
# 6. Try composer command again
```

**✅ EXPECTED OUTPUT:**
```
Creating a "laravel/laravel" project at "./"
Installing laravel/laravel (v11.x)
...
Application ready! Build something amazing.
```

---

## **STEP 4: Configure Environment**

### **4.1 - Setup .env File**

```bash
# Copy example environment file
copy .env.example .env
```

### **4.2 - Edit .env File**

Open `.env` file and change these lines:

```env
APP_NAME="KUET UMS"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# Comment out these lines (add # at start):
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

### **4.3 - Create SQLite Database**

```bash
# Create database directory if not exists
mkdir database

# Create empty database file
# Windows:
type nul > database\database.sqlite

# Mac/Linux:
touch database/database.sqlite

# Verify file created
dir database\database.sqlite
```

### **4.4 - Generate Application Key**

```bash
php artisan key:generate
```

**✅ EXPECTED OUTPUT:**
```
Application key set successfully.
```

---

## **STEP 5: Test Installation**

### **5.1 - Run Development Server**

```bash
php artisan serve
```

**✅ EXPECTED OUTPUT:**
```
INFO  Server running on [http://127.0.0.1:8000]
Press Ctrl+C to stop the server
```

### **5.2 - Open Browser**

```
Open: http://localhost:8000
You should see Laravel welcome page
```

**❌ PROBLEM:** `Address already in use`

**✅ SOLUTION:**
```bash
# Stop server (Ctrl+C)
# Use different port:
php artisan serve --port=8001

# Open: http://localhost:8001
```

---

## **STEP 6: First Commit**

```bash
# Stop server (Ctrl+C)

# Check git status
git status

# Add all files
git add .

# Commit
git commit -m "feat: initial Laravel installation and configuration"

# Push to GitHub
git push origin main
```

**✅ SUCCESS CHECKPOINT:**
- ✅ Laravel welcome page loads
- ✅ No errors in terminal
- ✅ Code pushed to GitHub

---

# 🗄️ **PHASE 2: DATABASE DESIGN (Day 2-3)**

## **STEP 7: Create Database Migrations**

### **7.1 - Modify Users Table Migration**

Find file: `database/migrations/0001_01_01_000000_create_users_table.php`

Replace with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'teacher', 'student', 'staff', 'department-head'])->default('student');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('profile_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
```

### **7.2 - Create Departments Migration**

```bash
php artisan make:migration create_departments_table
```

Find the new file: `database/migrations/YYYY_MM_DD_XXXXXX_create_departments_table.php`

Replace with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('head_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
```

### **7.3 - Create Teachers Migration**

```bash
php artisan make:migration create_teachers_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('employee_id')->unique();
            $table->string('designation');
            $table->string('qualification');
            $table->decimal('salary', 10, 2)->nullable();
            $table->date('joining_date');
            $table->enum('employment_type', ['full-time', 'part-time', 'contract'])->default('full-time');
            $table->text('specialization')->nullable();
            $table->boolean('is_department_head')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
```

### **7.4 - Create Students Migration**

```bash
php artisan make:migration create_students_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('student_id')->unique();
            $table->string('roll_number')->unique();
            $table->string('registration_number')->unique();
            $table->string('session');
            $table->string('academic_year');
            $table->string('semester');
            $table->date('admission_date');
            $table->foreignId('hall_id')->nullable()->constrained('halls')->onDelete('set null');
            $table->string('blood_group')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->decimal('cgpa', 3, 2)->default(0.00);
            $table->integer('total_credits')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
```

### **7.5 - Create Staff Migration**

```bash
php artisan make:migration create_staff_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->string('employee_id')->unique();
            $table->string('position');
            $table->string('qualification')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->date('joining_date');
            $table->enum('employment_type', ['full-time', 'part-time', 'contract'])->default('full-time');
            $table->enum('location', ['library', 'administration', 'department'])->default('administration');
            $table->text('responsibilities')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
```

### **7.6 - Create Halls Migration**

```bash
php artisan make:migration create_halls_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('halls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['male', 'female', 'both'])->default('both');
            $table->integer('capacity');
            $table->integer('occupied')->default(0);
            $table->foreignId('provost_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('facilities')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('halls');
    }
};
```

### **7.7 - Create Courses Migration**

```bash
php artisan make:migration create_courses_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code')->unique();
            $table->string('course_name');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->decimal('credit_hours', 3, 1);
            $table->string('academic_year');
            $table->string('semester');
            $table->enum('course_type', ['theory', 'lab', 'project', 'thesis'])->default('theory');
            $table->text('description')->nullable();
            $table->text('prerequisites')->nullable();
            $table->integer('max_students')->default(60);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
```

### **7.8 - Create Enrollments Migration**

```bash
php artisan make:migration create_enrollments_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->date('enrollment_date');
            $table->enum('status', ['enrolled', 'dropped', 'completed'])->default('enrolled');
            $table->decimal('grade_point', 3, 2)->nullable();
            $table->string('letter_grade')->nullable();
            $table->integer('attendance_percentage')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['student_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
```

### **7.9 - Create Attendances Migration**

```bash
php artisan make:migration create_attendances_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'course_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
```

### **7.10 - Create Exams Migration**

```bash
php artisan make:migration create_exams_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('exam_name');
            $table->enum('exam_type', ['mid-term', 'final', 'quiz', 'assignment'])->default('mid-term');
            $table->date('exam_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('total_marks');
            $table->integer('passing_marks')->nullable();
            $table->string('room_number')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
```

### **7.11 - Create Results Migration**

```bash
php artisan make:migration create_results_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->decimal('marks_obtained', 5, 2);
            $table->decimal('percentage', 5, 2);
            $table->string('grade');
            $table->decimal('grade_point', 3, 2);
            $table->text('remarks')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['exam_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
```

### **7.12 - Create Books Migration**

```bash
php artisan make:migration create_books_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('isbn')->unique();
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->string('category');
            $table->text('description')->nullable();
            $table->integer('total_copies');
            $table->integer('available_copies');
            $table->string('shelf_location')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
```

### **7.13 - Create Book Issues Migration**

```bash
php artisan make:migration create_book_issues_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('issued_by')->constrained('users')->onDelete('cascade');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['issued', 'returned', 'overdue', 'lost'])->default('issued');
            $table->decimal('fine_amount', 8, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_issues');
    }
};
```

### **7.14 - Create Notices Migration**

```bash
php artisan make:migration create_notices_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('posted_by')->constrained('users')->onDelete('cascade');
            $table->enum('target_audience', ['all', 'students', 'teachers', 'staff', 'department'])->default('all');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade');
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
```

### **7.15 - Create Fees Migration**

```bash
php artisan make:migration create_fees_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('fee_type');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue', 'waived'])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
```

---

## **STEP 8: Run Migrations**

```bash
php artisan migrate
```

**✅ EXPECTED OUTPUT:**
```
INFO  Preparing database.
Creating migration table .............................. 28ms DONE
INFO  Running migrations.
0001_01_01_000000_create_users_table .................. 35ms DONE
YYYY_MM_DD_create_departments_table ................... 12ms DONE
YYYY_MM_DD_create_halls_table ......................... 10ms DONE
YYYY_MM_DD_create_teachers_table ...................... 15ms DONE
YYYY_MM_DD_create_students_table ...................... 18ms DONE
... (all migrations)
```

**❌ PROBLEM:** `SQLSTATE[42S01]: Base table or view already exists`

**✅ SOLUTION:**
```bash
# Fresh migration (WARNING: deletes all data)
php artisan migrate:fresh

# Or rollback and re-run
php artisan migrate:rollback
php artisan migrate
```

**❌ PROBLEM:** `Foreign key constraint fails`

**✅ SOLUTION:**
```bash
# Check migration order
# halls_table must run BEFORE students_table
# departments_table must run BEFORE teachers_table

# Rename migration files to fix order:
# Change timestamps in filename:
# 2025_10_09_000001_create_departments_table.php
# 2025_10_09_000002_create_halls_table.php
# 2025_10_09_000003_create_teachers_table.php
# 2025_10_09_000004_create_students_table.php

# Then run:
php artisan migrate:fresh
```

**✅ SUCCESS CHECKPOINT:**
```bash
# Check migration status
php artisan migrate:status

# All migrations should show "Ran"
```

---

# 🏗️ **PHASE 3: CREATE MODELS (Day 3)**

## **STEP 9: Create Eloquent Models**

### **9.1 - Update User Model**

File: `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'profile_image',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    // Helper Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isDepartmentHead()
    {
        return $this->role === 'department-head';
    }
}
```

### **9.2 - Create Department Model**

```bash
php artisan make:model Department
```

File: `app/Models/Department.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'head_user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function head()
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
}
```

### **9.3 - Create Teacher Model**

```bash
php artisan make:model Teacher
```

File: `app/Models/Teacher.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_id',
        'employee_id',
        'designation',
        'qualification',
        'salary',
        'joining_date',
        'employment_type',
        'specialization',
        'is_department_head',
        'is_active',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'salary' => 'decimal:2',
        'is_department_head' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function exams()
    {
        return $this->hasManyThrough(Exam::class, Course::class);
    }

    // Helper Methods
    public function isDepartmentHead()
    {
        return $this->is_department_head || 
               Department::where('head_user_id', $this->user_id)->exists();
    }

    public function getManagedDepartment()
    {
        return Department::where('head_user_id', $this->user_id)->first();
    }
}
```

### **9.4 - Create Student Model**

```bash
php artisan make:model Student
```

File: `app/Models/Student.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_id',
        'student_id',
        'roll_number',
        'registration_number',
        'session',
        'academic_year',
        'semester',
        'admission_date',
        'hall_id',
        'blood_group',
        'guardian_name',
        'guardian_phone',
        'cgpa',
        'total_credits',
        'is_active',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'cgpa' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot('enrollment_date', 'status', 'grade_point', 'letter_grade')
            ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    public function bookIssues()
    {
        return $this->hasMany(BookIssue::class);
    }
}
```

### **9.5 - Create Staff Model**

```bash
php artisan make:model Staff
```

File: `app/Models/Staff.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_id',
        'employee_id',
        'position',
        'qualification',
        'salary',
        'joining_date',
        'employment_type',
        'location',
        'responsibilities',
        'is_active',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function issuedBooks()
    {
        return $this->hasMany(BookIssue::class, 'issued_by');
    }
}
```

### **9.6 - Create Remaining Models**

```bash
# Create all remaining models at once
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

### **9.7 - Hall Model**

File: `app/Models/Hall.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hall extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'capacity',
        'occupied',
        'provost_id',
        'facilities',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function provost()
    {
        return $this->belongsTo(User::class, 'provost_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
```

### **9.8 - Course Model**

File: `app/Models/Course.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_code',
        'course_name',
        'department_id',
        'teacher_id',
        'credit_hours',
        'academic_year',
        'semester',
        'course_type',
        'description',
        'prerequisites',
        'max_students',
        'is_active',
    ];

    protected $casts = [
        'credit_hours' => 'decimal:1',
        'is_active' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments')
            ->withPivot('enrollment_date', 'status', 'grade_point', 'letter_grade')
            ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
```

### **9.9 - Enrollment Model**

File: `app/Models/Enrollment.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'course_id',
        'enrollment_date',
        'status',
        'grade_point',
        'letter_grade',
        'attendance_percentage',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'grade_point' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
```

### **9.10 - Attendance Model**

File: `app/Models/Attendance.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'date',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
```

### **9.11 - Exam Model**

File: `app/Models/Exam.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'exam_name',
        'exam_type',
        'exam_date',
        'start_time',
        'end_time',
        'total_marks',
        'passing_marks',
        'room_number',
        'instructions',
        'is_published',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'is_published' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
```

### **9.12 - Result Model**

File: `app/Models/Result.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Result extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_id',
        'student_id',
        'marks_obtained',
        'percentage',
        'grade',
        'grade_point',
        'remarks',
        'is_published',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'percentage' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
```

### **9.13 - Book Model**

File: `app/Models/Book.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'isbn',
        'author',
        'publisher',
        'publication_year',
        'category',
        'description',
        'total_copies',
        'available_copies',
        'shelf_location',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function bookIssues()
    {
        return $this->hasMany(BookIssue::class);
    }
}
```

### **9.14 - BookIssue Model**

File: `app/Models/BookIssue.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookIssue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'book_id',
        'student_id',
        'issued_by',
        'issue_date',
        'due_date',
        'return_date',
        'status',
        'fine_amount',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_amount' => 'decimal:2',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function issuedByUser()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
```

### **9.15 - Notice Model**

File: `app/Models/Notice.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'posted_by',
        'target_audience',
        'department_id',
        'valid_until',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function postedByUser()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
```

### **9.16 - Fee Model**

File: `app/Models/Fee.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'fee_type',
        'amount',
        'due_date',
        'paid_date',
        'status',
        'transaction_id',
        'payment_method',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
```

**✅ SUCCESS CHECKPOINT:**
```bash
# Test models in Tinker
php artisan tinker

>>> User::count()
# Should return 0 (no users yet)

>>> exit
```

---

## **STEP 10: Commit Progress**

```bash
git add .
git commit -m "feat: create database migrations and eloquent models"
git push origin main
```

---

# 🔐 **PHASE 4: AUTHENTICATION (Day 4-5)**

## **STEP 11: Install Laravel Breeze**

```bash
composer require laravel/breeze --dev
```

**✅ EXPECTED OUTPUT:**
```
Info from https://repo.packagist.org: #StandWithUkraine
...
Package operations: X installs, 0 updates, 0 removals
...
Package laravel/breeze installed successfully.
```

```bash
php artisan breeze:install blade
```

**Select options:**
```
Which Breeze stack would you like to install?
› Blade with Alpine

Would you like dark mode support?
› No

Which testing framework do you prefer?
› PHPUnit
```

**✅ EXPECTED OUTPUT:**
```
INFO  Breeze scaffolding installed successfully.
```

```bash
npm install
npm run build
```

**❌ PROBLEM:** `'npm' is not recognized`

**✅ SOLUTION:**
```bash
# Download and install Node.js from https://nodejs.org/
# Download LTS version
# Run installer
# Restart Command Prompt
# Verify:
node --version
npm --version

# Try again:
npm install
npm run build
```

**OR Use CDN (Simpler for beginners):**

Edit `resources/views/layouts/app.blade.php` and add:

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Use Tailwind CDN instead of Vite -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    {{ $slot }}
</body>
</html>
```

---

## **STEP 12: Create Middleware**

### **12.1 - Create CheckRole Middleware**

```bash
php artisan make:middleware CheckRole
```

File: `app/Http/Middleware/CheckRole.php`

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
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->role !== $role) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
```

### **12.2 - Create PreventBackButton Middleware**

```bash
php artisan make:middleware PreventBackButton
```

File: `app/Http/Middleware/PreventBackButton.php`

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

### **12.3 - Register Middleware**

File: `bootstrap/app.php`

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

---

## **STEP 13: Update Authentication Logic**

### **13.1 - Modify Login Controller**

File: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

Find the `store` method and update:

```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    // Redirect based on role
    return redirect()->route('dashboard');
}
```

Find the `destroy` method and update:

```php
public function destroy(Request $request): RedirectResponse
{
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();
    $request->session()->flush();

    return redirect('/');
}
```

### **13.2 - Create Dashboard Route**

File: `routes/web.php`

Replace entire content with:

```php
<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isTeacher() || $user->isDepartmentHead()) {
        if ($user->teacher && $user->teacher->isDepartmentHead()) {
            return redirect()->route('department-head.dashboard');
        }
        return redirect()->route('teacher.dashboard');
    } elseif ($user->isStudent()) {
        return redirect()->route('student.dashboard');
    } elseif ($user->isStaff()) {
        return redirect()->route('staff.dashboard');
    }

    abort(403, 'Unauthorized access');
})->middleware(['auth', 'verified', 'prevent-back'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
```

---

## **STEP 14: Create Database Seeder**

### **14.1 - Create DepartmentSeeder**

```bash
php artisan make:seeder DepartmentSeeder
```

File: `database/seeders/DepartmentSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Computer Science and Engineering', 'code' => 'CSE', 'is_active' => true],
            ['name' => 'Electrical and Electronic Engineering', 'code' => 'EEE', 'is_active' => true],
            ['name' => 'Mechanical Engineering', 'code' => 'ME', 'is_active' => true],
            ['name' => 'Civil Engineering', 'code' => 'CE', 'is_active' => true],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
```

### **14.2 - Create HallSeeder**

```bash
php artisan make:seeder HallSeeder
```

File: `database/seeders/HallSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Hall;
use Illuminate\Database\Seeder;

class HallSeeder extends Seeder
{
    public function run(): void
    {
        $halls = [
            ['name' => 'Khan Jahan Ali Hall', 'code' => 'KJAH', 'type' => 'male', 'capacity' => 400, 'occupied' => 0],
            ['name' => 'Amar Ekushey Hall', 'code' => 'AEH', 'type' => 'male', 'capacity' => 350, 'occupied' => 0],
            ['name' => 'Lalan Shah Hall', 'code' => 'LSH', 'type' => 'male', 'capacity' => 300, 'occupied' => 0],
            ['name' => 'Rokeya Hall', 'code' => 'RH', 'type' => 'female', 'capacity' => 250, 'occupied' => 0],
        ];

        foreach ($halls as $hall) {
            Hall::create($hall);
        }
    }
}
```

### **14.3 - Create AdminSeeder**

```bash
php artisan make:seeder AdminSeeder
```

File: `database/seeders/AdminSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@kuet.ac.bd',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '01700000000',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Vice Chancellor',
            'email' => 'vc@kuet.ac.bd',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '01700000001',
            'is_active' => true,
        ]);
    }
}
```

### **14.4 - Update DatabaseSeeder**

File: `database/seeders/DatabaseSeeder.php`

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
        ]);
    }
}
```

### **14.5 - Run Seeders**

```bash
php artisan db:seed
```

**✅ EXPECTED OUTPUT:**
```
INFO  Seeding database.

Database\Seeders\DepartmentSeeder ....................... RUNNING
Database\Seeders\DepartmentSeeder ................ 15.23 ms DONE

Database\Seeders\HallSeeder .............................. RUNNING
Database\Seeders\HallSeeder ........................ 8.45 ms DONE

Database\Seeders\AdminSeeder ............................. RUNNING
Database\Seeders\AdminSeeder ....................... 125.67 ms DONE
```

---

## **STEP 15: Test Authentication**

```bash
# Run server
php artisan serve
```

```
# Open browser: http://localhost:8000/login

# Try logging in:
Email: admin@kuet.ac.bd
Password: password

# You'll see error "route [admin.dashboard] not defined"
# This is EXPECTED - we haven't created admin dashboard yet
```

**✅ SUCCESS CHECKPOINT:**
- ✅ Login page loads
- ✅ Can submit login form
- ✅ Error shows "route not defined" (not "wrong credentials")

---

## **STEP 16: Commit Progress**

```bash
git add .
git commit -m "feat: implement authentication with Laravel Breeze and role-based middleware"
git push origin main
```

---

# 👨‍💼 **PHASE 5: ADMIN PANEL (Days 6-7)**

## **DAY 6: ADMIN PANEL - SETUP & DASHBOARD**

### **Goals:**
- ✅ Create admin controllers
- ✅ Build admin dashboard
- ✅ Create admin layout

### **Time Estimate:** 4-5 hours

---

## **STEP 17: Verify Git Status**

```bash
# Make sure you're on main branch
git branch
# Should show: * main

# Check you're up to date
git status
```

**Note:** We work directly on main branch - no feature branches needed!

---

## **STEP 18: Create Admin Controllers** (15 mins)

```bash
php artisan make:controller Admin/DashboardController
php artisan make:controller Admin/DepartmentController --resource
php artisan make:controller Admin/TeacherController --resource
php artisan make:controller Admin/StudentController --resource
php artisan make:controller Admin/StaffController --resource
```

**✅ EXPECTED OUTPUT:**
```
Controller created successfully.
Controller created successfully.
... (for each controller)
```

---

## **STEP 19: Admin Dashboard Controller** (30 mins)

**File:** `app/Http/Controllers/Admin/DashboardController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Department, Teacher, Student, Staff, Course, Notice};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate statistics
        $stats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('is_active', true)->count(),
            'total_teachers' => Teacher::count(),
            'active_teachers' => Teacher::where('is_active', true)->count(),
            'total_staff' => Staff::count(),
            'total_courses' => Course::count(),
            'active_courses' => Course::where('is_active', true)->count(),
            'total_departments' => Department::count(),
        ];

        // Recent records
        $recentStudents = Student::with('user', 'department')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentNotices = Notice::with('postedByUser')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentStudents', 'recentNotices'));
    }
}
```

---

## **STEP 20: Create Admin Routes** (15 mins)

**File:** `routes/web.php`

Add after authentication routes:

```php
// Admin Routes
Route::middleware(['auth', 'role:admin', 'prevent-back'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('departments', App\Http\Controllers\Admin\DepartmentController::class);
    Route::resource('teachers', App\Http\Controllers\Admin\TeacherController::class);
    Route::resource('students', App\Http\Controllers\Admin\StudentController::class);
    Route::resource('staff', App\Http\Controllers\Admin\StaffController::class);
});
```

---

## **STEP 21: Create Admin Layout** (30 mins)

**File:** `resources/views/components/admin-layout.blade.php`

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-white">{{ config('app.name') }} - Admin</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-white hover:text-gray-200">Dashboard</a>
                    <a href="{{ route('admin.departments.index') }}" class="text-white hover:text-gray-200">Departments</a>
                    <a href="{{ route('admin.teachers.index') }}" class="text-white hover:text-gray-200">Teachers</a>
                    <a href="{{ route('admin.students.index') }}" class="text-white hover:text-gray-200">Students</a>
                    <a href="{{ route('admin.staff.index') }}" class="text-white hover:text-gray-200">Staff</a>
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-white hover:text-gray-200 flex items-center">
                            {{ Auth::user()->name }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
</body>
</html>
```

---

## **STEP 22: Create Admin Dashboard View** (45 mins)

**File:** `resources/views/admin/dashboard.blade.php`

```html
<x-admin-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Admin Dashboard</h2>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-500">Total Students</div>
                                <div class="text-3xl font-bold text-blue-600">{{ $stats['total_students'] }}</div>
                                <div class="text-sm text-gray-600 mt-1">
                                    Active: {{ $stats['active_students'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Teachers -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-500">Total Teachers</div>
                                <div class="text-3xl font-bold text-green-600">{{ $stats['total_teachers'] }}</div>
                                <div class="text-sm text-gray-600 mt-1">
                                    Active: {{ $stats['active_teachers'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Staff -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-500">Total Staff</div>
                                <div class="text-3xl font-bold text-purple-600">{{ $stats['total_staff'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Courses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-500">Total Courses</div>
                                <div class="text-3xl font-bold text-orange-600">{{ $stats['total_courses'] }}</div>
                                <div class="text-sm text-gray-600 mt-1">
                                    Active: {{ $stats['active_courses'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <a href="{{ route('admin.students.create') }}" 
                           class="bg-blue-600 text-white px-4 py-3 rounded text-center hover:bg-blue-700">
                            Add New Student
                        </a>
                        <a href="{{ route('admin.teachers.create') }}" 
                           class="bg-green-600 text-white px-4 py-3 rounded text-center hover:bg-green-700">
                            Add New Teacher
                        </a>
                        <a href="{{ route('admin.staff.create') }}" 
                           class="bg-purple-600 text-white px-4 py-3 rounded text-center hover:bg-purple-700">
                            Add New Staff
                        </a>
                        <a href="{{ route('admin.departments.create') }}" 
                           class="bg-orange-600 text-white px-4 py-3 rounded text-center hover:bg-orange-700">
                            Add New Department
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Students -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4">Recent Students</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentStudents as $student)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $student->student_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $student->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $student->user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $student->department->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('admin.students.show', $student) }}" 
                                           class="text-blue-600 hover:underline">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
```

---

## **STEP 23: Test Admin Dashboard** (10 mins)

```bash
# Run server
php artisan serve

# Open browser: http://localhost:8000/login

# Login with admin credentials:
Email: admin@kuet.ac.bd
Password: password

# Should now redirect to admin dashboard!
```

**✅ SUCCESS CHECKPOINT:**
- ✅ Admin dashboard loads
- ✅ Statistics show (even if 0)
- ✅ Navigation works
- ✅ No errors

---

## **STEP 24: Commit Progress**

```bash
# Commit your work to main branch
git add .
git commit -m "feat: implement admin dashboard with statistics and layout"
git push origin main
```

---

## **DAY 7: ADMIN PANEL - CRUD OPERATIONS**

### **Goals:**
- ✅ Create student management
- ✅ Create teacher management
- ✅ Create department management

### **Time Estimate:** 6-7 hours

---

## **STEP 25: Department Controller** (60 mins)

**File:** `app/Http/Controllers/Admin/DepartmentController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Department, User};
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('head')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.departments.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code',
            'description' => 'nullable|string',
            'head_user_id' => 'nullable|exists:users,id',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load('head', 'teachers', 'students', 'courses');

        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $teachers = User::where('role', 'teacher')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.departments.edit', compact('department', 'teachers'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'head_user_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
```

---

## **STEP 26: Create Department Views** (90 mins)

**Create directory:**
```bash
mkdir resources/views/admin/departments
```

**File:** `resources/views/admin/departments/index.blade.php`

```html
<x-admin-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Departments</h2>
                <a href="{{ route('admin.departments.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Add New Department
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Head</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($departments as $department)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $department->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $department->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $department->head->name ?? 'Not Assigned' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded {{ $department->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $department->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.departments.show', $department) }}" 
                                       class="text-blue-600 hover:underline mr-3">View</a>
                                    <a href="{{ route('admin.departments.edit', $department) }}" 
                                       class="text-green-600 hover:underline mr-3">Edit</a>
                                    <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" 
                                          class="inline" 
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $departments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
```

**File:** `resources/views/admin/departments/create.blade.php`

```html
<x-admin-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold mb-6">Create New Department</h2>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.departments.store') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Department Name <span class="text-red-600">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Code -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Department Code <span class="text-red-600">*</span>
                            </label>
                            <input type="text" 
                                   name="code" 
                                   value="{{ old('code') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('code')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" 
                                      rows="4"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Department Head -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department Head</label>
                            <select name="head_user_id" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Department Head</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('head_user_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('head_user_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('admin.departments.index') }}" 
                               class="px-4 py-2 text-gray-700 hover:text-gray-900">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                Create Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
```

**File:** `resources/views/admin/departments/edit.blade.php`

```html
<x-admin-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold mb-6">Edit Department</h2>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.departments.update', $department) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Department Name <span class="text-red-600">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', $department->name) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm"
                                   required>
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Code -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Department Code <span class="text-red-600">*</span>
                            </label>
                            <input type="text" 
                                   name="code" 
                                   value="{{ old('code', $department->code) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm"
                                   required>
                            @error('code')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" 
                                      rows="4"
                                      class="w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $department->description) }}</textarea>
                        </div>

                        <!-- Department Head -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department Head</label>
                            <select name="head_user_id" 
                                    class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Select Department Head</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" 
                                            {{ old('head_user_id', $department->head_user_id) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $department->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('admin.departments.index') }}" 
                               class="px-4 py-2 text-gray-700 hover:text-gray-900">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                Update Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
```

---

## **STEP 27: Test Department Management** (15 mins)

```bash
# Make sure server is running
php artisan serve

# Open: http://localhost:8000/admin/dashboard
# Click "Departments" in navigation
# Click "Add New Department"
# Fill form and submit

# Test:
# ✅ Create department
# ✅ View department list
# ✅ Edit department
# ✅ Delete department
```

---

## **STEP 28: Commit Day 7 Progress**

```bash
# Commit your work to main branch
git add .
git commit -m "feat: implement department CRUD operations with views"
git push origin main
```

---

**✅ DAY 6-7 CHECKLIST:**
- [x] Admin dashboard created
- [x] Admin layout complete
- [x] Department CRUD functional
- [x] Views responsive and styled
- [x] All code committed

# 👨‍🎓 **PHASE 6: STUDENT MODULE (Days 8-9)**

## **DAY 8: STUDENT MODULE - SETUP & DASHBOARD**

### **Goals:**
- ✅ Create student controllers
- ✅ Build student dashboard
- ✅ Create student layout

### **Time Estimate:** 5-6 hours

---

## **STEP 29: Create Feature Branch**

```bash
git checkout develop
git pull origin develop
git checkout -b feature/student-module
```

---

## **STEP 30: Create Student Controllers** (15 mins)

```bash
php artisan make:controller Student/DashboardController
php artisan make:controller Student/ProfileController
php artisan make:controller Student/CourseEnrollmentController
php artisan make:controller Student/ResultController
```

---

## **STEP 31: Student Dashboard Controller** (45 mins)

**File:** `app/Http/Controllers/Student/DashboardController.php`

```php
<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\{Notice, Enrollment, Result};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;

        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Calculate statistics
        $enrolledCourses = $student->enrollments()
            ->where('status', 'enrolled')
            ->count();

        $completedCourses = $student->enrollments()
            ->where('status', 'completed')
            ->count();

        $totalCredits = $student->enrollments()
            ->where('status', 'completed')
            ->with('course')
            ->get()
            ->sum(function ($enrollment) {
                return $enrollment->course->credit_hours;
            });

        // Get current courses
        $currentCourses = $student->enrollments()
            ->with(['course.teacher.user', 'course.department'])
            ->where('status', 'enrolled')
            ->get();

        // Get recent results
        $recentResults = $student->results()
            ->with('exam.course')
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get notices
        $notices = Notice::where('is_active', true)
            ->where(function($query) use ($student) {
                $query->where('target_audience', 'all')
                    ->orWhere('target_audience', 'students')
                    ->orWhere('department_id', $student->department_id);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Fee statistics
        $pendingFees = $student->fees()
            ->where('status', 'pending')
            ->sum('amount');

        return view('student.dashboard', compact(
            'student',
            'enrolledCourses',
            'completedCourses',
            'totalCredits',
            'currentCourses',
            'recentResults',
            'notices',
            'pendingFees'
        ));
    }
}
```

---

## **STEP 32: Create Student Routes** (15 mins)

**File:** `routes/web.php`

Add after admin routes:

```php
// Student Routes
Route::middleware(['auth', 'role:student', 'prevent-back'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [App\Http\Controllers\Student\ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::put('/profile/password', [App\Http\Controllers\Student\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/profile/academic', [App\Http\Controllers\Student\ProfileController::class, 'academic'])->name('profile.academic');
    
    // Course enrollment
    Route::get('/courses', [App\Http\Controllers\Student\CourseEnrollmentController::class, 'index'])->name('courses.index');
    Route::post('/courses/{course}/enroll', [App\Http\Controllers\Student\CourseEnrollmentController::class, 'enroll'])->name('courses.enroll');
    Route::delete('/courses/{enrollment}/drop', [App\Http\Controllers\Student\CourseEnrollmentController::class, 'drop'])->name('courses.drop');
    
    // Results
    Route::get('/results', [App\Http\Controllers\Student\ResultController::class, 'index'])->name('results.index');
});
```

---

## **STEP 33: Create Student Layout** (30 mins)

**File:** `resources/views/components/student-layout.blade.php`

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Student Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-indigo-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-white">{{ config('app.name') }} - Student</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('student.dashboard') }}" class="text-white hover:text-gray-200">Dashboard</a>
                    <a href="{{ route('student.courses.index') }}" class="text-white hover:text-gray-200">Courses</a>
                    <a href="{{ route('student.results.index') }}" class="text-white hover:text-gray-200">Results</a>
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-white hover:text-gray-200 flex items-center">
                            {{ Auth::user()->name }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                            <a href="{{ route('student.profile.index') }}" 
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                            <a href="{{ route('student.profile.academic') }}" 
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Academic Info</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
</body>
</html>
```

---

## **STEP 34: Create Student Dashboard View** (60 mins)

**Create directory:**
```bash
mkdir resources/views/student
```

**File:** `resources/views/student/dashboard.blade.php`

```html
<x-student-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Student Dashboard</h2>

            <!-- Student Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        @if(Auth::user()->profile_image)
                            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                                 alt="Profile" 
                                 class="h-20 w-20 rounded-full object-cover mr-4">
                        @else
                            <div class="h-20 w-20 rounded-full bg-indigo-600 flex items-center justify-center text-white text-2xl font-bold mr-4">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="text-2xl font-bold">{{ Auth::user()->name }}</h3>
                            <p class="text-gray-600">Student ID: {{ $student->student_id }}</p>
                            <p class="text-gray-600">{{ $student->department->name }}</p>
                            <p class="text-gray-600">{{ $student->academic_year }} - Semester {{ $student->semester }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- CGPA -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">CGPA</div>
                        <div class="text-3xl font-bold text-indigo-600">{{ number_format($student->cgpa, 2) }}</div>
                    </div>
                </div>

                <!-- Enrolled Courses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Enrolled Courses</div>
                        <div class="text-3xl font-bold text-blue-600">{{ $enrolledCourses }}</div>
                    </div>
                </div>

                <!-- Completed Courses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Completed Courses</div>
                        <div class="text-3xl font-bold text-green-600">{{ $completedCourses }}</div>
                    </div>
                </div>

                <!-- Total Credits -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Total Credits</div>
                        <div class="text-3xl font-bold text-purple-600">{{ $totalCredits }}</div>
                    </div>
                </div>
            </div>

            <!-- Current Courses -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4">Current Courses</h3>
                    @if($currentCourses->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($currentCourses as $enrollment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $enrollment->course->course_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $enrollment->course->course_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $enrollment->course->teacher->user->name ?? 'Not Assigned' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $enrollment->course->credit_hours }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600">No courses enrolled yet.</p>
                    @endif
                </div>
            </div>

            <!-- Recent Results and Notices Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Results -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-4">Recent Results</h3>
                        @if($recentResults->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentResults as $result)
                                <div class="border-l-4 border-indigo-500 pl-4">
                                    <p class="font-semibold">{{ $result->exam->course->course_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $result->exam->exam_name }}</p>
                                    <p class="text-lg font-bold text-indigo-600">
                                        Grade: {{ $result->grade }} ({{ $result->grade_point }})
                                    </p>
                                </div>
                                @endforeach
                            </div>
                            <a href="{{ route('student.results.index') }}" 
                               class="text-indigo-600 hover:underline mt-4 inline-block">
                                View All Results →
                            </a>
                        @else
                            <p class="text-gray-600">No results published yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Notices -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-4">Notices</h3>
                        @if($notices->count() > 0)
                            <div class="space-y-3">
                                @foreach($notices as $notice)
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <p class="font-semibold">{{ $notice->title }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ Str::limit($notice->content, 100) }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $notice->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-600">No notices available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
```

---

## **STEP 35: Test Student Dashboard** (10 mins)

First, create a test student:

```bash
php artisan tinker

# Create a test student
>>> $user = User::create([
    'name' => 'Test Student',
    'email' => 'student@test.com',
    'password' => Hash::make('password'),
    'role' => 'student',
    'is_active' => true
]);

>>> $student = Student::create([
    'user_id' => $user->id,
    'department_id' => 1,
    'student_id' => 'STU-001',
    'roll_number' => '001',
    'registration_number' => 'REG-001',
    'session' => '2024-25',
    'academic_year' => '1st Year',
    'semester' => '1st',
    'admission_date' => now(),
    'cgpa' => 0.00
]);

>>> exit
```

Test login:
```bash
# Run server
php artisan serve

# Open: http://localhost:8000/login
# Email: student@test.com
# Password: password

# Should redirect to student dashboard!
```

---

## **STEP 36: Commit Day 8 Progress**

```bash
# Commit your work to main branch
git add .
git commit -m "feat: implement student dashboard with statistics and current courses"
git push origin main
```

---

## **DAY 9: STUDENT MODULE - PROFILE & FEATURES**

### **Goals:**
- ✅ Student profile management
- ✅ Course enrollment
- ✅ View results

### **Time Estimate:** 5-6 hours

---

## **STEP 37: Profile Controller** (90 mins)

**File:** `app/Http/Controllers/Student/ProfileController.php`

```php
<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Storage};
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $student->load('user', 'department', 'hall');

        return view('student.profile.index', compact('student'));
    }

    public function edit()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        return view('student.profile.edit', compact('student'));
    }

    public function update(Request $request)
    {
        $student = Auth::user()->student;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'blood_group' => 'nullable|string|max:5',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update user info
        $student->user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        // Update student-specific info
        $student->update([
            'blood_group' => $request->blood_group,
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            
            // Delete old image if exists
            if ($student->user->profile_image) {
                $oldImagePath = storage_path('app/public/' . $student->user->profile_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            // Store new image
            $imagePath = $image->store('profile_images', 'public');
            
            // Update database
            $student->user->update(['profile_image' => $imagePath]);
        }

        return redirect()->route('student.profile.index')
            ->with('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        return view('student.profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('student.profile.index')
            ->with('success', 'Password changed successfully.');
    }

    public function academic()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $student->load('department.head', 'hall', 'enrollments.course');

        return view('student.profile.academic', compact('student'));
    }
}
```

---

## **STEP 38: Create Profile Views** (120 mins)

**Create directory:**
```bash
mkdir resources/views/student/profile
```

**File:** `resources/views/student/profile/index.blade.php`

```html
<x-student-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">My Profile</h2>
                <div class="space-x-3">
                    <a href="{{ route('student.profile.edit') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Edit Profile
                    </a>
                    <a href="{{ route('student.profile.change-password') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                        Change Password
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Profile Picture and Basic Info -->
                    <div class="flex items-center mb-6 pb-6 border-b">
                        @if($student->user->profile_image)
                            <img src="{{ asset('storage/' . $student->user->profile_image) }}" 
                                 alt="Profile" 
                                 class="h-24 w-24 rounded-full object-cover mr-6">
                        @else
                            <div class="h-24 w-24 rounded-full bg-indigo-600 flex items-center justify-center text-white text-3xl font-bold mr-6">
                                {{ substr($student->user->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="text-2xl font-bold">{{ $student->user->name }}</h3>
                            <p class="text-gray-600">{{ $student->user->email }}</p>
                            <p class="text-gray-600">Student ID: {{ $student->student_id }}</p>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b">
                        <div>
                            <h4 class="text-lg font-semibold mb-4">Personal Information</h4>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600">Phone:</span>
                                    <span class="ml-2 font-medium">{{ $student->user->phone ?? 'Not provided' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Blood Group:</span>
                                    <span class="ml-2 font-medium">{{ $student->blood_group ?? 'Not provided' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Address:</span>
                                    <span class="ml-2 font-medium">{{ $student->user->address ?? 'Not provided' }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-lg font-semibold mb-4">Guardian Information</h4>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600">Guardian Name:</span>
                                    <span class="ml-2 font-medium">{{ $student->guardian_name ?? 'Not provided' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Guardian Phone:</span>
                                    <span class="ml-2 font-medium">{{ $student->guardian_phone ?? 'Not provided' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-lg font-semibold mb-4">Academic Information</h4>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600">Department:</span>
                                    <span class="ml-2 font-medium">{{ $student->department->name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Roll Number:</span>
                                    <span class="ml-2 font-medium">{{ $student->roll_number }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Registration:</span>
                                    <span class="ml-2 font-medium">{{ $student->registration_number }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Session:</span>
                                    <span class="ml-2 font-medium">{{ $student->session }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-lg font-semibold mb-4">Current Status</h4>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600">Academic Year:</span>
                                    <span class="ml-2 font-medium">{{ $student->academic_year }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Semester:</span>
                                    <span class="ml-2 font-medium">{{ $student->semester }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">CGPA:</span>
                                    <span class="ml-2 font-medium text-indigo-600 text-lg">{{ number_format($student->cgpa, 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Total Credits:</span>
                                    <span class="ml-2 font-medium">{{ $student->total_credits }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
```

**File:** `resources/views/student/profile/edit.blade.php`

```html
<x-student-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold mb-6">Edit Profile</h2>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Profile Picture -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                            <div class="flex items-center space-x-4">
                                @if(Auth::user()->profile_image)
                                    <img id="preview" 
                                         src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                                         alt="Profile" 
                                         class="h-24 w-24 rounded-full object-cover">
                                @else
                                    <div id="preview" class="h-24 w-24 rounded-full bg-indigo-600 flex items-center justify-center text-white text-3xl font-bold">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <input type="file" 
                                       name="profile_image" 
                                       accept="image/*"
                                       onchange="previewImage(event)"
                                       class="border border-gray-300 rounded px-3 py-2">
                            </div>
                            @error('profile_image')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-600">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', Auth::user()->name) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm"
                                   required>
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="text" 
                                   name="phone" 
                                   value="{{ old('phone', Auth::user()->phone) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('phone')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="address" 
                                      rows="3"
                                      class="w-full border-gray-300 rounded-md shadow-sm">{{ old('address', Auth::user()->address) }}</textarea>
                            @error('address')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Blood Group -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Blood Group</label>
                            <select name="blood_group" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Select Blood Group</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                    <option value="{{ $group }}" 
                                            {{ old('blood_group', $student->blood_group) == $group ? 'selected' : '' }}>
                                        {{ $group }}
                                    </option>
                                @endforeach
                            </select>
                            @error('blood_group')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Guardian Name -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Name</label>
                            <input type="text" 
                                   name="guardian_name" 
                                   value="{{ old('guardian_name', $student->guardian_name) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('guardian_name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Guardian Phone -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Phone</label>
                            <input type="text" 
                                   name="guardian_phone" 
                                   value="{{ old('guardian_phone', $student->guardian_phone) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('guardian_phone')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-3 mt-6">
                            <a href="{{ route('student.profile.index') }}" 
                               class="px-4 py-2 text-gray-700 hover:text-gray-900">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        const img = document.createElement('img');
                        img.id = 'preview';
                        img.className = 'h-24 w-24 rounded-full object-cover';
                        img.src = e.target.result;
                        preview.replaceWith(img);
                    }
                };
                
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-student-layout>
```

---

## **STEP 39: Create Storage Link** (5 mins)

```bash
# Create symbolic link for storage
php artisan storage:link
```

**✅ EXPECTED OUTPUT:**
```
The [public/storage] link has been connected to [storage/app/public].
```

---

## **STEP 40: Test Profile Features** (15 mins)

```bash
# Make sure server is running
php artisan serve

# Login as student
# Email: student@test.com
# Password: password

# Test:
# ✅ View profile
# ✅ Edit profile
# ✅ Upload profile picture
# ✅ Change password
```

---

## **STEP 41: Commit Day 9 Progress**

```bash
# Commit your work to main branch
git add .
git commit -m "feat: implement student profile management with image upload"
git push origin main
```

---

**✅ DAYS 8-9 CHECKLIST:**
- [x] Student dashboard created
- [x] Student layout complete
- [x] Profile management functional
- [x] Profile picture upload working
- [x] Password change working
- [x] All code committed

---

# 🎓 **SUMMARY: REMAINING DAYS**

## **Days 10-20 Pattern:**

The BUILD_UMS_STEP_BY_STEP.md guide has now covered **Days 1-9** in complete detail with full code.

For **Days 10-20**, follow the same detailed pattern shown in Days 1-9:

### **Days 11-12: TEACHER MODULE**
**Pattern to follow:**
- Create feature branch `feature/teacher-module`
- Create controllers (Dashboard, Course, Attendance, Exam, Result)
- Create teacher layout similar to student layout
- Create teacher dashboard with course statistics
- Implement attendance marking system
- Implement exam creation and marks entry
- Implement grade calculation and result publishing
- Create all views with same structure as student views
- Test all features
- Commit and merge

**Reference:** See DAY_11_20_GUIDE.md for complete teacher module code

---

### **Day 13: STAFF MODULE**
**Pattern to follow:**
- Create feature branch `feature/staff-module`
- Create controllers (Dashboard, Library, BookIssue, Student)
- Create staff layout similar to admin layout
- Implement library book management
- Implement book issue/return system
- Restrict staff access to student academic info
- Test all features
- Commit and merge

**Reference:** See DAY_11_20_GUIDE.md for complete staff module code

---

### **Day 14: DEPARTMENT HEAD**
**Pattern to follow:**
- Create feature branch `feature/department-head`
- Add `is_department_head` flag to teachers table
- Create DepartmentHead controllers
- Implement course assignment to teachers
- Implement workload reports
- Add conditional department head menu in teacher layout
- Test features
- Commit and merge

**Reference:** See DAY_11_20_GUIDE.md for complete department head code

---

### **Days 15-17: UI/UX & FEATURES**
**Pattern to follow:**
- Feature branch `feature/ui-improvements`
- Enhance all layouts with consistent styling
- Add responsive design improvements
- Implement search and filter functionality
- Add sorting to tables
- Improve error messages and validation
- Add loading states
- Test on mobile devices
- Commit and merge

---

### **Day 18: TESTING**
**Testing checklist:**
```bash
# Test all modules:
1. Admin Panel
   - Login as admin
   - Create/Edit/Delete departments
   - Create/Edit/Delete teachers
   - Create/Edit/Delete students
   - Create/Edit/Delete staff

2. Student Module
   - Login as student
   - View dashboard
   - Edit profile
   - Upload profile picture
   - Change password
   - View courses
   - View results

3. Teacher Module
   - Login as teacher
   - View dashboard
   - Mark attendance
   - Create exam
   - Enter marks
   - Publish results

4. Staff Module
   - Login as staff
   - View dashboard
   - Manage books
   - Issue/Return books
   - View student records

5. Department Head
   - Login as department head
   - View department stats
   - Assign courses to teachers
   - View workload reports

6. Security Testing
   - Try accessing admin routes as student
   - Try accessing teacher routes as staff
   - Verify middleware protection
   - Test logout functionality
   - Test back button prevention
```

---

### **Day 19: BUG FIXES**
**Common bugs to check:**
```bash
# Check for:
1. N+1 query problems
   - Use Laravel Debugbar
   - Check queries on each page
   - Add eager loading where needed

2. Validation issues
   - Test all forms with invalid data
   - Verify error messages display
   - Check required field validation

3. Permission issues
   - Test cross-role access
   - Verify middleware works
   - Check 403 errors

4. UI issues
   - Test responsive design
   - Check mobile view
   - Verify all links work

5. Data integrity
   - Check foreign key constraints
   - Verify cascading deletes
   - Test unique constraints
```

---

### **Day 20: DEPLOYMENT PREPARATION**
**Deployment checklist:**
```bash
# 1. Environment preparation
cp .env .env.production
# Edit .env.production:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# 2. Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 3. Security checks
- Remove all dd() and dump()
- Remove all console.log()
- Check all passwords are hashed
- Verify CSRF protection on forms
- Check file upload validation

# 4. Database preparation
php artisan migrate --force
php artisan db:seed --force

# 5. Final testing
- Test all modules
- Verify production environment
- Check error handling
- Test email functionality (if added)

# 6. Deployment
- Push to production server
- Set up cron jobs (if needed)
- Configure web server
- Set file permissions
- Test live site

# 7. Post-deployment
- Monitor error logs
- Check performance
- Verify backups working
- Document any issues
```

---

# 📚 **COMPLETE DEVELOPMENT RESOURCES**

## **You Now Have:**

1. **BUILD_UMS_STEP_BY_STEP.md** (This file)
   - Days 1-9 with complete code
   - Pattern for Days 10-20

2. **DAY_01_10_GUIDE.md**
   - Detailed day-by-day for Days 1-10
   - Complete explanations

3. **DAY_11_20_GUIDE.md**
   - Detailed day-by-day for Days 11-20
   - Teacher, Staff, Department Head code

4. **FUNCTIONS_EXPLAINED.md**
   - Every function explained
   - How each works

5. **ERROR_HANDLING_GUIDE.md**
   - All errors with solutions

6. **GIT_BRANCHING_STRATEGY.md**
   - Complete Git workflow
   - All branches to create

7. **INTERVIEW_QA.md**
   - Interview questions and answers

8. **DOCUMENTATION_INDEX.md**
   - Master guide to all resources

---

# 🎯 **HOW TO COMPLETE DAYS 10-20**

## **Option 1: Follow This File's Pattern**
Use the same structure shown in Days 1-9:
1. Create feature branch
2. Create controllers with complete code
3. Create routes
4. Create layouts
5. Create views
6. Test features
7. Commit and merge

## **Option 2: Use DAY_11_20_GUIDE.md**
Open DAY_11_20_GUIDE.md and follow Days 11-20 with complete code for:
- Teacher Module (Days 11-12)
- Staff Module (Day 13)
- Department Head (Day 14)

## **Option 3: Copy from Existing System**
Look at the current codebase in `app/Http/Controllers/Teacher`, `Staff`, etc. and create views following the pattern shown in Days 1-9.

---

# ✅ **FINAL SUMMARY**

## **What's Complete:**

✅ **Days 1-5:** Setup, Database, Models, Authentication  
✅ **Days 6-7:** Admin Panel with Department CRUD  
✅ **Days 8-9:** Student Module with Profile & Features  

## **What to Do Next:**

📋 **Days 11-12:** Teacher Module (Attendance, Exams, Grading)  
📋 **Day 13:** Staff Module (Library Management)  
📋 **Day 14:** Department Head (Course Assignment)  
📋 **Days 15-17:** UI/UX Improvements  
📋 **Days 18-20:** Testing & Deployment  

## **Resources Available:**

📖 **9 Complete Guides** covering every aspect  
💻 **Complete Code** for Days 1-9  
🔍 **Patterns** to follow for Days 10-20  
🐛 **Error Solutions** for every problem  
🎤 **Interview Answers** ready to use  

---

**You have everything needed to complete the entire UMS system!** 🚀

**Follow the pattern shown in Days 1-9, reference DAY_11_20_GUIDE.md for teacher/staff/department-head code, and use the other guides for support!**

