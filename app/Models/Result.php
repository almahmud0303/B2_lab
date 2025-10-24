<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Result extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'exam_id',
        'obtained_marks',
        'total_marks',
        'percentage',
        'grade',
        'grade_points',
        'remarks',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'obtained_marks' => 'decimal:2',
            'total_marks' => 'decimal:2',
            'percentage' => 'decimal:2',
            'grade_points' => 'decimal:2',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    public function scopePassed($query)
    {
        return $query->whereNotIn('grade', ['F', 'U']);
    }

    // Accessors
    public function getPercentageAttribute()
    {
        if ($this->total_marks > 0) {
            return round(($this->obtained_marks / $this->total_marks) * 100, 2);
        }
        return 0;
    }

    public function getGradeBadgeAttribute()
    {
        $badges = [
            'A+' => 'bg-green-100 text-green-800',
            'A' => 'bg-green-100 text-green-800',
            'A-' => 'bg-green-100 text-green-800',
            'B+' => 'bg-blue-100 text-blue-800',
            'B' => 'bg-blue-100 text-blue-800',
            'B-' => 'bg-blue-100 text-blue-800',
            'C+' => 'bg-yellow-100 text-yellow-800',
            'C' => 'bg-yellow-100 text-yellow-800',
            'C-' => 'bg-yellow-100 text-yellow-800',
            'D+' => 'bg-orange-100 text-orange-800',
            'D' => 'bg-orange-100 text-orange-800',
            'F' => 'bg-red-100 text-red-800',
            'U' => 'bg-gray-100 text-gray-800',
        ];

        return $badges[$this->grade] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusAttribute()
    {
        if (in_array($this->grade, ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D'])) {
            return 'Passed';
        }
        return 'Failed';
    }

    // Mutators
    public function setGradeAttribute($value)
    {
        $this->attributes['grade'] = strtoupper($value);
        $this->attributes['grade_points'] = $this->calculateGradePoints($value);
    }

    private function calculateGradePoints($grade)
    {
        $gradePoints = [
            'A+' => 4.0,
            'A' => 4.0,
            'A-' => 3.7,
            'B+' => 3.3,
            'B' => 3.0,
            'B-' => 2.7,
            'C+' => 2.3,
            'C' => 2.0,
            'C-' => 1.7,
            'D+' => 1.3,
            'D' => 1.0,
            'F' => 0.0,
            'U' => 0.0,
        ];

        return $gradePoints[$grade] ?? 0.0;
    }
}