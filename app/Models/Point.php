<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'details',
        'parent_id',
        'meeting_id',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function parent()
    {
        return $this->belongsTo(Point::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Point::class, 'parent_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }
    public function subpoints()
    {
        return $this->hasMany(Point::class, 'parent_id')->where('parent_id', '<>', null);
    }

        // Define the updateMaterials method
        public function updateMaterials(array $materials)
        {
            // You can implement the logic to update materials here
            // For example, you can delete existing materials and insert the new ones
    
            // Assuming materials have a 'filename' and 'point_id' field
            $existingMaterials = $this->materials;
            
            // Delete existing materials
            foreach ($existingMaterials as $material) {
                $material->delete();
            }
    
            // Insert new materials
            foreach ($materials as $materialData) {
                $this->materials()->create([
                    'filename' => $materialData['filename'],
                    // Other fields...
                ]);
            }
        }


}