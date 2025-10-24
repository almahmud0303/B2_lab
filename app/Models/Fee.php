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

    public function scopeByType($query, $type)
    {
        return $query->where('fee_type', $type);
    }

    // Accessors
    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date < now() && $this->status !== 'paid';
    }

    // Mutators
    public function setStatusAttribute($value)
    {
        if ($this->paid_amount >= $this->amount) {
            $this->attributes['status'] = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->attributes['status'] = 'partial';
        } else {
            $this->attributes['status'] = $this->is_overdue ? 'overdue' : 'pending';
        }
    }
}