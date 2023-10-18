<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Point;
use App\Models\Meeting;
use App\Models\Material;
use Illuminate\Support\Facades\Log;


class PointController extends Controller
{
    public function index()
    {
        $points = Point::all();
        return view('points.index', compact('points'));
    }

    public function create()
    {
        return view('points.create');
    }

    public function getSubpoints($meetingId, $pointId)
    {
        $point = Point::where('meeting_id', $meetingId)
            ->where('id', $pointId)
            ->first();

        $subpoints = $point->subpoints;

        return response()->json(['subpoints' => $subpoints]);
    }

    public function show($id)
    {
        $point = Point::with(['subpoints', 'subpoints.materials', 'materials'])->find($id);

        if (!$point) {
            return response()->json(['error' => 'Point not found'], 404);
        }

        return response()->json(['point' => $point]);
    }


    // public function update(Request $request, Point $point)
    // {
    //     // Validate the incoming request data as needed
    //     $validatedData = $request->validate([
    //         'title' => 'required|string',
    //         'details' => 'required|string',
    //     ]);

    //     // Update the point using the validated data
    //     $point->update($validatedData);

    //     // Optionally, you can handle updates to subpoints or materials here

    //     // Return a response, e.g., a success message or the updated point
    //     return response()->json(['message' => 'Point updated successfully', 'point' => $point]);
    // }


    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'details' => 'nullable',
            'parent_id' => 'exists:points,id',
            // Ensure the parent_id exists in points
            'meeting_id' => 'required|exists:meetings,id',
            // Ensure the meeting_id exists in meetings
        ]);

        Point::create($data);

        return redirect()->route('points.index');
    }

    public function addPoint(Meeting $meeting, Request $request)
    {
        try {
            // Validate and store the point data
            $data = $request->validate([
                'title' => 'required',
                'details' => 'required',
            ]);
    
            $data['parent_id'] = null; // Set parent_id to null for regular points
    
            Log::info('Point data received: ' . json_encode($data)); // Log data
    
            $point = new Point($data);
            $meeting->points()->save($point);
    
            return response()->json(['message' => 'Point added successfully', 'point_id' => $point->id]);
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Log the error
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

    public function addSubpoint(Meeting $meeting, Point $point, Request $request)
    {
        try {
            // Validate and store the subpoint data
            $data = $request->validate([
                'title' => 'required',
                'details' => 'required',
            ]);
    
            $data['parent_id'] = $point->id; // Set the parent_id to the ID of the parent point
    
            Log::info('Subpoint data received: ' . json_encode($data)); // Log data
    
            $subpoint = new Point($data);
            $meeting->points()->save($subpoint);
    
            return response()->json(['message' => 'Subpoint added successfully', 'point_id' => $subpoint->id]);
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Log the error
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }
    
    

    public function uploadMaterial(Meeting $meeting, Point $point, Request $request)
    {
        // Handle material file uploads for the given point
        $files = $request->file('pdfMaterial');
        $materialMessages = [];
    
        foreach ($files as $file) {
            // Generate a unique filename or customize it as needed
            $customFilename = 'custom_' . time() . '_' . $file->getClientOriginalName();
    
            // Use the custom disk for storage
            $path = $file->storeAs('custom-path', $customFilename, 'materials');
    
            // Extract the original filename (name of the uploaded file)
            $originalFilename = $file->getClientOriginalName();
    
            $material = new Material([
                'filename' => $originalFilename, // Set the original filename as the material's filename
                'file_path' => $path
            ]);
    
            $point->materials()->save($material);
            $materialMessages[] = 'Material uploaded successfully';
        }
    
        return response()->json(['messages' => $materialMessages]);
    }





    public function update(Request $request, $id)
    {
        // Validate the request data
        $this->validate($request, [
            'title' => 'required|string',
            'details' => 'nullable|string',
            'pdfMaterial' => 'array',
        ]);
    
        // Find the top-level point by its ID
        $point = Point::findOrFail($id);
    
        // Update point details
        $point->update([
            'title' => $request->input('title'),
            'details' => $request->input('details'),
        ]);
    
        // Update point materials (pdfMaterial)
        if ($request->has('pdfMaterial')) {
            $point->updateMaterials($request->input('pdfMaterial'));
        }
    
        // Handle subpoints and their materials
        if ($request->has('subpoints')) {
            $subpoints = $request->input('subpoints');
    
            // Get the IDs of subpoints that should be deleted
            $currentSubpointIds = collect($subpoints)->pluck('id');
            $existingSubpointIds = $point->subpoints->pluck('id');
            $deletedSubpointIds = $existingSubpointIds->diff($currentSubpointIds);
    
            // Delete subpoints with matching IDs
            Point::whereIn('id', $deletedSubpointIds)->delete();
    
            // Iterate over subpoints and update/create them
            foreach ($subpoints as $subpointData) {
                $subpoint = Point::updateOrCreate(
                    ['id' => $subpointData['id']], // Update if ID exists, else create a new point (subpoint)
                    [
                        'title' => $subpointData['title'],
                        'details' => $subpointData['details'],
                        'parent_id' => $point->id, // Set the parent_id to link subpoints to the main point
                    ]
                );
    
                // Update subpoint materials
                if (isset($subpointData['materials'])) {
                    $subpoint->updateMaterials($subpointData['materials']);
                }
            }
        }
    
        // Return a response, e.g., a success message or updated data
        return response()->json(['message' => 'Point and subpoints updated successfully']);
    }




    public function destroy($id)
    {
        // Find the point by its ID
        $point = Point::findOrFail($id);

        // Delete the point
        $point->delete();

        // Optionally, you can also delete associated materials or subpoints if needed.

        return response()->json(['message' => 'Point deleted successfully']);
    }


    public function createSubpoint(Request $request, $parentId)
    {
        // Validate the request data for the subpoint
        $this->validate($request, [
            'title' => 'required|string',
            'details' => 'nullable|string',
            'meeting_id' => 'required|integer', // Validate the meeting_id
        ]);
    
        // Create the subpoint with the provided data, set the parent_id and meeting_id
        $subpoint = Point::create([
            'title' => $request->input('title'),
            'details' => $request->input('details'),
            'parent_id' => $parentId, // Set the parent_id to the provided $parentId
            'meeting_id' => $request->input('meeting_id'), // Set the meeting_id
        ]);
    
        // Return a response, e.g., the created subpoint
        return response()->json($subpoint, 201);
    }
    
    

    
    

    // Implement update, delete, and show methods as needed.
}