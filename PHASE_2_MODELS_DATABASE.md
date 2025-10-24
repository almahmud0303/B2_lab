# Phase 2: Core Models & Database (Days 5-8)

## 🎯 **Phase 2 Objectives**
- Create all core models
- Implement database relationships
- Create comprehensive migrations
- Set up model factories
- Create database seeders
- Implement soft deletes

---

## 📅 **Day 5: Core Models Creation**

### **Step 1: Create Student Model**

```bash
php artisan make:model Student -m
```

**File: `database/migrations/xxxx_xx_xx_create_students_table.php`**

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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('student_id')->unique();
            $table->string('batch')->nullable();
            $table->string('session')->nullable();
            $table->date('admission_date');
            $table->enum('status', ['active', 'inactive', 'graduated', 'suspended'])->default('active');
            $table->decimal('cgpa', 3, 2)->nullable();
            $table->integer('total_credits')->default(0);
            $table->integer('completed_credits')->default(0);
            $table->foreignId('hall_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['student_id', 'status']);
            $table->index(['department_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
```

**File: `app/Models/Student.php`**

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
        'batch',
        'session',
        'admission_date',
        'status',
        'cgpa',
        'total_credits',
        'completed_credits',
        'hall_id',
    ];

    protected function casts(): array
    {
        return [
            'admission_date' => 'date',
            'cgpa' => 'decimal:2',
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
        return $this->belongsToMany(Course::class, 'enrollments');
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeGraduated($query)
    {
        return $query->where('status', 'graduated');
    }

    // Helper methods
    public function getFullNameAttribute()
    {
        return $this->user->name;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }
}
```

### **Step 2: Create Staff Model**

```bash
php artisan make:model Staff -m
```

**File: `database/migrations/xxxx_xx_xx_create_staff_table.php`**

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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_id')->unique();
            $table->string('designation');
            $table->string('department')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->date('joining_date');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract'])->default('full_time');
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['employee_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
```

**File: `app/Models/Staff.php`**

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
        'employee_id',
        'designation',
        'department',
        'salary',
        'joining_date',
        'employment_type',
        'bio',
        'location',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'salary' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookIssues()
    {
        return $this->hasMany(BookIssue::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function getFullNameAttribute()
    {
        return $this->user->name;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }
}
```

### **Step 3: Create Course Model**

```bash
php artisan make:model Course -m
```

**File: `database/migrations/xxxx_xx_xx_create_courses_table.php`**

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
            $table->string('title');
            $table->string('course_code')->unique();
            $table->text('description')->nullable();
            $table->integer('credits');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->integer('academic_year');
            $table->integer('semester');
            $table->integer('max_students')->default(50);
            $table->enum('type', ['theory', 'lab', 'project', 'thesis'])->default('theory');
            $table->decimal('currency', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['course_code', 'is_active']);
            $table->index(['department_id', 'academic_year', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
```

**File: `app/Models/Course.php`**

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
        'title',
        'course_code',
        'description',
        'credits',
        'department_id',
        'teacher_id',
        'academic_year',
        'semester',
        'max_students',
        'type',
        'currency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'currency' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
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
        return $this->belongsToMany(Student::class, 'enrollments');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    // Helper methods
    public function getEnrolledCountAttribute()
    {
        return $this->enrollments()->where('status', 'enrolled')->count();
    }

    public function getAvailableSlotsAttribute()
    {
        return $this->max_students - $this->enrolled_count;
    }
}
```

---

## 📅 **Day 6: Enrollment & Exam Models**

### **Step 1: Create Enrollment Model**

```bash
php artisan make:model Enrollment -m
```

**File: `database/migrations/xxxx_xx_xx_create_enrollments_table.php`**

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
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['enrolled', 'dropped', 'completed', 'failed'])->default('enrolled');
            $table->date('enrollment_date');
            $table->date('completion_date')->nullable();
            $table->decimal('grade_point', 3, 2)->nullable();
            $table->string('letter_grade')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['student_id', 'course_id']);
            $table->index(['student_id', 'status']);
            $table->index(['course_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
```

**File: `app/Models/Enrollment.php`**

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
        'status',
        'enrollment_date',
        'completion_date',
        'grade_point',
        'letter_grade',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'completion_date' => 'date',
            'grade_point' => 'decimal:2',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Scopes
    public function scopeEnrolled($query)
    {
        return $query->where('status', 'enrolled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Helper methods
    public function getStudentNameAttribute()
    {
        return $this->student->user->name;
    }

    public function getCourseTitleAttribute()
    {
        return $this->course->title;
    }
}
```

### **Step 2: Create Exam Model**

```bash
php artisan make:model Exam -m
```

**File: `database/migrations/xxxx_xx_xx_create_exams_table.php`**

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
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['quiz', 'midterm', 'final', 'assignment'])->default('quiz');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('total_marks');
            $table->string('venue')->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['course_id', 'exam_date']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
```

**File: `app/Models/Exam.php`**

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
        'title',
        'description',
        'course_id',
        'type',
        'exam_date',
        'start_time',
        'end_time',
        'total_marks',
        'venue',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
        ];
    }

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function getDurationAttribute()
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        return $start->diffInMinutes($end);
    }

    public function getFormattedDateAttribute()
    {
        return $this->exam_date->format('M d, Y');
    }

    public function getFormattedTimeAttribute()
    {
        return \Carbon\Carbon::parse($this->start_time)->format('h:i A') . ' - ' . 
               \Carbon\Carbon::parse($this->end_time)->format('h:i A');
    }
}
```

### **Step 3: Create Result Model**

```bash
php artisan make:model Result -m
```

**File: `database/migrations/xxxx_xx_xx_create_results_table.php`**

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
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->integer('marks_obtained');
            $table->string('grade');
            $table->decimal('grade_point', 3, 2);
            $table->text('remarks')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['exam_id', 'student_id']);
            $table->index(['student_id', 'is_published']);
            $table->index(['exam_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
```

**File: `app/Models/Result.php`**

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
        'grade',
        'grade_point',
        'remarks',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'grade_point' => 'decimal:2',
            'is_published' => 'boolean',
        ];
    }

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUnpublished($query)
    {
        return $query->where('is_published', false);
    }

    // Helper methods
    public function getStudentNameAttribute()
    {
        return $this->student->user->name;
    }

    public function getExamTitleAttribute()
    {
        return $this->exam->title;
    }

    public function getPercentageAttribute()
    {
        return ($this->marks_obtained / $this->exam->total_marks) * 100;
    }
}
```

---

## 📅 **Day 7: Additional Models (Hall, Fee, Payment, Notice)**

### **Step 1: Create Hall Model**

```bash
php artisan make:model Hall -m
```

**File: `database/migrations/xxxx_xx_xx_create_halls_table.php`**

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
            $table->text('description')->nullable();
            $table->integer('capacity');
            $table->json('facilities')->nullable();
            $table->string('location')->nullable();
            $table->enum('type', ['male', 'female', 'mixed'])->default('mixed');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['code', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('halls');
    }
};
```

**File: `app/Models/Hall.php`**

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
        'description',
        'capacity',
        'facilities',
        'location',
        'type',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
            'is_available' => 'boolean',
        ];
    }

    // Relationships
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function getOccupiedCountAttribute()
    {
        return $this->students()->count();
    }

    public function getAvailableCountAttribute()
    {
        return $this->capacity - $this->occupied_count;
    }

    public function getOccupancyPercentageAttribute()
    {
        return ($this->occupied_count / $this->capacity) * 100;
    }

    // Ensure facilities is always an array
    public function getFacilitiesAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?: [];
        }
        return is_array($value) ? $value : [];
    }
}
```

### **Step 2: Create Fee Model**

```bash
php artisan make:model Fee -m
```

**File: `database/migrations/xxxx_xx_xx_create_fees_table.php`**

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
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('fee_type');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['student_id', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
```

**File: `app/Models/Fee.php`**

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
        'paid_amount',
        'due_date',
        'paid_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_date' => 'date',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    // Helper methods
    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    public function getStudentNameAttribute()
    {
        return $this->student->user->name;
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date < now() && $this->status !== 'paid';
    }
}
```

### **Step 3: Create Payment Model**

```bash
php artisan make:model Payment -m
```

**File: `database/migrations/xxxx_xx_xx_create_payments_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('fee_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'mobile_banking', 'bkash', 'nagad', 'rocket'])->default('cash');
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['student_id', 'status']);
            $table->index(['payment_method', 'status']);
            $table->index(['transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
```

**File: `app/Models/Payment.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'course_id',
        'fee_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'notes',
        'payment_details',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_details' => 'array',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // Helper methods
    public function getStudentNameAttribute()
    {
        return $this->student->user->name;
    }

    public function getFormattedAmountAttribute()
    {
        return 'TK ' . number_format($this->amount, 2);
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y');
    }
}
```

### **Step 4: Create Notice Model**

```bash
php artisan make:model Notice -m
```

**File: `database/migrations/xxxx_xx_xx_create_notices_table.php`**

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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['general', 'academic', 'exam', 'fee', 'library', 'event'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->json('target_roles')->nullable();
            $table->date('publish_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_published', 'publish_date']);
            $table->index(['type', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
```

**File: `app/Models/Notice.php`**

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
        'user_id',
        'title',
        'content',
        'type',
        'priority',
        'target_roles',
        'publish_date',
        'expiry_date',
        'is_published',
        'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'target_roles' => 'array',
            'publish_date' => 'date',
            'expiry_date' => 'date',
            'is_published' => 'boolean',
            'is_pinned' => 'boolean',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeActive($query)
    {
        return $query->where('is_published', true)
                    ->where(function($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', now());
                    });
    }

    // Helper methods
    public function getTargetRolesListAttribute()
    {
        return is_array($this->target_roles) ? implode(', ', $this->target_roles) : 'All';
    }

    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }
}
```

---

## 📅 **Day 8: Model Factories & Seeders**

### **Step 1: Create Model Factories**

```bash
php artisan make:factory DepartmentFactory
php artisan make:factory TeacherFactory
php artisan make:factory StudentFactory
php artisan make:factory StaffFactory
php artisan make:factory CourseFactory
php artisan make:factory HallFactory
php artisan make:factory NoticeFactory
```

### **Step 2: Create Database Seeders**

```bash
php artisan make:seeder DepartmentSeeder
php artisan make:seeder UserSeeder
php artisan make:seeder TeacherSeeder
php artisan make:seeder StudentSeeder
php artisan make:seeder StaffSeeder
php artisan make:seeder CourseSeeder
php artisan make:seeder HallSeeder
php artisan make:seeder NoticeSeeder
```

### **Step 3: Run Migrations and Seeders**

```bash
# Run all migrations
php artisan migrate

# Run seeders
php artisan db:seed --class=DepartmentSeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=TeacherSeeder
php artisan db:seed --class=StudentSeeder
php artisan db:seed --class=StaffSeeder
php artisan db:seed --class=CourseSeeder
php artisan db:seed --class=HallSeeder
php artisan db:seed --class=NoticeSeeder
```

### **Step 4: Git Commit**

```bash
git add .
git commit -m "Phase 2 complete: Core models, relationships, and database structure"
```

---

## ✅ **Phase 2 Checklist**

- [x] Student model created
- [x] Staff model created
- [x] Course model created
- [x] Enrollment model created
- [x] Exam model created
- [x] Result model created
- [x] Hall model created
- [x] Fee model created
- [x] Payment model created
- [x] Notice model created
- [x] All relationships defined
- [x] Model factories created
- [x] Database seeders created
- [x] Migrations run successfully

---

## 🚀 **Next Steps**

Phase 2 is complete! You now have:
- Complete database structure
- All core models with relationships
- Proper migrations and seeders
- Model factories for testing

**Ready for Phase 3?** We'll build the Admin module with full CRUD operations! 🎯
