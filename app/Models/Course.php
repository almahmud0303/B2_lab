<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department_id',
        'teacher_id',
        'course_code',
        'title',
        'description',
        'credits',
        'semester',
        'academic_year',
        'course_type',
        'prerequisites',
        'max_students',
        'max_enrollments',
        'fee_amount',
        'currency',
        'fee_required',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'fee_required' => 'boolean',
            'fee_amount' => 'decimal:2',
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

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments')
                    ->withPivot('status', 'enrollment_date')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    // Accessors
    public function getCurrentEnrollmentCountAttribute()
    {
        return $this->enrollments()->where('status', 'enrolled')->count();
    }

    public function getAvailableSlotsAttribute()
    {
        return $this->max_students - $this->current_enrollment_count;
    }
}