# Phase 1: Project Setup & Authentication (Days 1-4)

## 🎯 **Phase 1 Objectives**
- Set up Laravel project
- Configure database
- Implement authentication system
- Create user roles
- Set up basic routing
- Initialize Git repository

---

## 📅 **Day 1: Project Initialization**

### **Step 1: Create Laravel Project**

```bash
# Install Laravel globally (if not already installed)
composer global require laravel/installer

# Create new Laravel project
laravel new university-management-system
cd university-management-system

# Or using Composer
composer create-project laravel/laravel university-management-system
cd university-management-system
```

### **Step 2: Install Additional Packages**

```bash
# Install Laravel Breeze for authentication
composer require laravel/breeze --dev

# Install Breeze with Blade stack
php artisan breeze:install blade

# Install additional packages
composer require laravel/sanctum
composer require intervention/image
composer require maatwebsite/excel
```

### **Step 3: Environment Configuration**

Create `.env` file:
```env
APP_NAME="University Management System"
APP_ENV=local
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=university_management
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
```

### **Step 4: Generate Application Key**

```bash
php artisan key:generate
```

### **Step 5: Initialize Git Repository**

```bash
# Initialize Git
git init

# Create .gitignore (should already exist)
# Add all files to Git
git add .

# Initial commit
git commit -m "Initial Laravel project setup with Breeze authentication"
```

---

## 📅 **Day 2: Database Setup**

### **Step 1: Create Database**

```sql
-- Create database
CREATE DATABASE university_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user (optional)
CREATE USER 'ums_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON university_management.* TO 'ums_user'@'localhost';
FLUSH PRIVILEGES;
```

### **Step 2: Run Initial Migrations**

```bash
# Run Breeze migrations
php artisan migrate

# This creates:
# - users table
# - password_reset_tokens table
# - failed_jobs table
# - personal_access_tokens table
```

### **Step 3: Create Custom Migrations**

```bash
# Create migrations for user roles
php artisan make:migration add_role_to_users_table
php artisan make:migration create_departments_table
php artisan make:migration create_teachers_table
php artisan make:migration create_students_table
php artisan make:migration create_staff_table
```

### **Step 4: Implement User Roles Migration**

**File: `database/migrations/xxxx_xx_xx_add_role_to_users_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'teacher', 'student', 'staff', 'department_head'])
                  ->default('student')
                  ->after('email');
            $table->string('profile_image')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('profile_image');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'profile_image', 'is_active']);
        });
    }
};
```

### **Step 5: Create Departments Migration**

**File: `database/migrations/xxxx_xx_xx_create_departments_table.php`**

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
            $table->string('head_of_department')->nullable();
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

---

## 📅 **Day 3: Models & Relationships**

### **Step 1: Update User Model**

**File: `app/Models/User.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    public function department()
    {
        return $this->belongsTo(Department::class, 'head_user_id');
    }

    // Helper methods
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
        return $this->role === 'department_head';
    }
}
```

### **Step 2: Create Department Model**

**File: `app/Models/Department.php`**

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
        'head_of_department',
        'head_user_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

### **Step 3: Create Teacher Model**

**File: `app/Models/Teacher.php`**

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
        'qualification',
        'specialization',
        'salary',
        'joining_date',
        'employment_type',
        'bio',
        'is_active',
        'is_department_head',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'salary' => 'decimal:2',
            'is_active' => 'boolean',
            'is_department_head' => 'boolean',
        ];
    }

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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function isDepartmentHead()
    {
        return $this->is_department_head;
    }
}
```

---

## 📅 **Day 4: Authentication & Middleware**

### **Step 1: Create Role Middleware**

```bash
php artisan make:middleware CheckRole
```

**File: `app/Http/Middleware/CheckRole.php`**

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

### **Step 2: Register Middleware**

**File: `app/Http/Kernel.php`**

```php
protected $routeMiddleware = [
    // ... existing middleware
    'role' => \App\Http\Middleware\CheckRole::class,
    'prevent.back' => \App\Http\Middleware\PreventBackButton::class,
];
```

### **Step 3: Create Prevent Back Button Middleware**

```bash
php artisan make:middleware PreventBackButton
```

**File: `app/Http/Middleware/PreventBackButton.php`**

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
                        ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }
}
```

### **Step 4: Update Authentication Routes**

**File: `routes/web.php`**

```php
<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    switch ($user->role) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'teacher':
            return redirect()->route('teacher.dashboard');
        case 'student':
            return redirect()->route('student.dashboard');
        case 'staff':
            return redirect()->route('staff.dashboard');
        case 'department_head':
            return redirect()->route('department-head.dashboard');
        default:
            return redirect()->route('login');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
```

### **Step 5: Create Basic Route Groups**

**File: `routes/web.php` (continued)**

```php
// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin', 'prevent.back'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});

// Teacher Routes
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher', 'prevent.back'])->group(function () {
    Route::get('/dashboard', function () {
        return view('teacher.dashboard');
    })->name('dashboard');
});

// Student Routes
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student', 'prevent.back'])->group(function () {
    Route::get('/dashboard', function () {
        return view('student.dashboard');
    })->name('dashboard');
});

// Staff Routes
Route::prefix('staff')->name('staff.')->middleware(['auth', 'role:staff', 'prevent.back'])->group(function () {
    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('dashboard');
});

// Department Head Routes
Route::prefix('department-head')->name('department-head.')->middleware(['auth', 'role:department_head', 'prevent.back'])->group(function () {
    Route::get('/dashboard', function () {
        return view('department-head.dashboard');
    })->name('dashboard');
});
```

### **Step 6: Create Basic Dashboard Views**

**File: `resources/views/admin/dashboard.blade.php`**

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Welcome to Admin Dashboard</h1>
                    <p>You are logged in as: <strong>{{ auth()->user()->name }}</strong></p>
                    <p>Role: <strong>{{ ucfirst(auth()->user()->role) }}</strong></p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

### **Step 7: Run Migrations**

```bash
# Run all migrations
php artisan migrate

# Check migration status
php artisan migrate:status
```

### **Step 8: Git Commit**

```bash
git add .
git commit -m "Phase 1 complete: Project setup, authentication, and basic routing"
```

---

## ✅ **Phase 1 Checklist**

- [x] Laravel project created
- [x] Laravel Breeze installed
- [x] Database configured
- [x] User roles implemented
- [x] Basic models created
- [x] Middleware implemented
- [x] Route groups created
- [x] Basic views created
- [x] Git repository initialized

---

## 🚀 **Next Steps**

Phase 1 is complete! You now have:
- A working Laravel application
- Authentication system with roles
- Basic routing structure
- Database setup
- Git version control

**Ready for Phase 2?** We'll create the core models and database relationships! 🎯
