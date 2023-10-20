<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Material extends Model
{
    protected $fillable = ['title', 'filename', 'point_id'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($material) {
            // Delete the associated file from storage
            if (Storage::disk('public')->exists($material->filename)) {
                Storage::disk('public')->delete($material->filename);
            }
        });
    }

    public function point()
    {
        return $this->belongsTo(Point::class);
    }
}

