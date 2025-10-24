<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'isbn',
        'title',
        'author',
        'publisher',
        'publication_year',
        'category',
        'total_copies',
        'available_copies',
        'description',
        'cover_image',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function bookIssues()
    {
        return $this->hasMany(BookIssue::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_copies', '>', 0);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getIssuedCopiesAttribute()
    {
        return $this->total_copies - $this->available_copies;
    }

    public function getAvailabilityStatusAttribute()
    {
        return $this->available_copies > 0 ? 'Available' : 'Not Available';
    }
}