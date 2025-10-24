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
        'qualifications',
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

    // Accessors
    public function isDepartmentHead()
    {
        return $this->is_department_head;
    }

    public function scopeDepartmentHeads($query)
    {
        return $query->where('is_department_head', true);
    }



    public function getManagedDepartment()
    {
        return Department::where('head_user_id', $this->user_id)->first();
    }
}