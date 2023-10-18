<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Point;
use App\Models\Material;

class MaterialController extends Controller
{
    public function create(Point $point)
    {
        return view('materials.create', compact('point'));
    }

    public function store(Request $request, Point $point)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'file' => 'required|file|max:10240', // 10 MB limit
        ]);

        $file = $validatedData['file'];
        $file_name = time() . '_' . $file->getClientOriginalName();
        $file_path = $file->storeAs('materials', $file_name);

        $material = new Material([
            'title' => $validatedData['title'],
            'filename' => $file_path,
        ]);

        $point->materials()->save($material);

        return redirect()->route('points.show', $point);
    }

    public function edit(Point $point, Material $material)
    {
        return view('materials.edit', compact('point', 'material'));
    }

    public function update(Request $request, Point $point, Material $material)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
        ]);

        $material->title = $validatedData['title'];
        $material->save();

        return redirect()->route('points.show', $point);
    }

    public function destroy(Point $point, Material $material)
    {
        $material->delete();

        return redirect()->route('points.show', $point);
    }


    public function deleteMaterial($materialId)
    {
        // Retrieve the Material model based on the material ID
        $material = Material::find($materialId);
    
        if (!$material) {
            return response()->json(['error' => 'Material not found'], 404);
        }
    
        try {
            // Delete the material from the database
            $material->delete();
    
            // You can also delete the physical file if it's stored on the server
            // Make sure to implement the logic to delete the file
    
            return response()->json(['message' => 'Material deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete material'], 500);
        }
    }









    public function uploadForPoint(Request $request, $point_id)
    {
        $this->validate($request, [
            'file' => 'required|file', // Allow all types of files
        ]);

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();

        // Generate a unique filename or customize it as needed
        $customFilename = 'custom_' . time() . '_' . $originalFilename;

        // Use the custom disk for storage
        $path = $file->storeAs('custom-path', $customFilename, 'materials');

        // Save the material to the materials table
        $material = Material::create([
            'filename' => $originalFilename,
            'file_path' => $path,
            'point_id' => $point_id,
        ]);

        return response()->json($material, 201);
    }

    // public function uploadForSubpoint(Request $request, $subpoint)
    // {
    //     $this->validate($request, [
    //         'file' => 'required|file', // Allow all types of files
    //     ]);

    //     $file = $request->file('file');
    //     $originalFilename = $file->getClientOriginalName();

    //     // Generate a unique filename or customize it as needed
    //     $customFilename = 'custom_' . time() . '_' . $originalFilename;

    //     // Use the custom disk for storage
    //     $path = $file->storeAs('custom-path', $customFilename, 'materials');

    //     // Save the material to the materials table
    //     $material = Material::create([
    //         'filename' => $originalFilename,
    //         'file_path' => $path,
    //         'point_id' => $subpoint->id,
    //     ]);

    //     return response()->json($material, 201);
    // }
}
