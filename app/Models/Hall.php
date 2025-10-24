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
        'capacity',
        'description',
        'location',
        'facilities',
        'is_available',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Ensure facilities is always an array
    public function getFacilitiesAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?: [];
        }
        return is_array($value) ? $value : [];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByCapacity($query, $minCapacity)
    {
        return $query->where('capacity', '>=', $minCapacity);
    }

    // Accessors
    public function getFacilitiesListAttribute()
    {
        return is_array($this->facilities) ? implode(', ', $this->facilities) : '';
    }

    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'Inactive';
        }
        return $this->is_available ? 'Available' : 'Occupied';
    }

    // Relationships
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function getAssignedStudentsCountAttribute()
    {
        return $this->students()->count();
    }

    public function getAvailableCapacityAttribute()
    {
        return $this->capacity - $this->assigned_students_count;
    }
}