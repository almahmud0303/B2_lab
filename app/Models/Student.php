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
        'admission_number',
        'admission_date',
        'academic_year',
        'semester',
        'guardian_name',
        'guardian_phone',
        'guardian_address',
        'cgpa',
        'status',
        'is_active',
        'hall_id',
    ];

    protected function casts(): array
    {
        return [
            'admission_date' => 'date',
            'cgpa' => 'decimal:2',
            'is_active' => 'boolean',
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

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
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

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id', 'user_id');
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->user->name;
    }
}