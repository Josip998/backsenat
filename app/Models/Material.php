<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Material extends Model
{
    protected $fillable = ['title', 'filename', 'point_id'];

    public function point()
    {
        return $this->belongsTo(Point::class);
    }
}

