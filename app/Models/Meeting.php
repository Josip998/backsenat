<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'location',
        'virtual',
        'google_meet_link',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'virtual' => 'boolean',
    ];

    public function points()
    {
        return $this->hasMany(Point::class);
    }
    
    // Define scopes for common queries, if needed
    // For example:
    

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>=', now());
    }
}

