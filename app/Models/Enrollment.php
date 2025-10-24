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
        'grade',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
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
    public function scopeActive($query)
    {
        return $query->where('status', 'enrolled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->whereHas('course', function($q) use ($semester) {
            $q->where('semester', $semester);
        });
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'enrolled' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'dropped' => 'bg-red-100 text-red-800',
            'withdrawn' => 'bg-yellow-100 text-yellow-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }
}