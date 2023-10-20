<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\Point;

class MeetingController extends Controller
{
    public function index()
    {
        $meetings = Meeting::orderBy('id', 'desc')->get();

        return response()->json($meetings);
    }

    public function show(Meeting $meeting)
    {
        return response()->json($meeting);
    }

    public function getMeetingPoints(Meeting $meeting)
    {
        // Use eager loading to retrieve points associated with the meeting
        $meetingPoints = $meeting->points;

        return response()->json($meetingPoints);
    }

    public function getTopLevelPoints($meetingId)
    {
        // Fetch top-level points for the specified meeting
        $topLevelPoints = Point::where('meeting_id', $meetingId)
            ->whereNull('parent_id')
            ->with(['subpoints', 'subpoints.materials', 'materials']) // Eager load subpoints and materials for both top-level points and subpoints
            ->get();
    
        // Transform the materials data to include document URLs for top-level points and subpoints
        $topLevelPoints->each(function ($point) {
            $point->materials->transform(function ($material) {
                $material->document_url = asset('storage/' . $material->filename);
                return $material;
            });
    
            // Iterate through subpoints for each top-level point
            $point->subpoints->each(function ($subpoint) {
                $subpoint->materials->transform(function ($material) {
                    $material->document_url = asset('storage/' . $material->filename);
                    return $material;
                });
            });
        });
    
        return response()->json($topLevelPoints);
    }
    
    




    public function store(Request $request)
    {
        // Validate and create a new meeting
        $data = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'start_time' => 'required|date',
            'location' => 'nullable',
            'virtual' => 'boolean',
            'google_meet_link' => 'required',
        ]);

        $meeting = Meeting::create($data);

        return response()->json($meeting, 201); // 201 Created status code
    }

    public function getMeetingsWithoutPoints()
    {
        // Use Eloquent to fetch meetings without associated points
        $meetings = Meeting::doesntHave('points')->get();

        return response()->json($meetings);
    }

    public function update(Request $request, $meetingId)
    {
        // Validate and update the meeting
        $data = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'start_time' => 'required|date',
            'location' => 'nullable',
            'virtual' => 'boolean',
            'google_meet_link' => 'required',
        ]);
    
        $meeting = Meeting::findOrFail($meetingId); // Find the meeting by ID
    
        $meeting->update($data);
    
        return response()->json($meeting);
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return response()->json(null, 204); // 204 No Content status code
    }

    public function getMeetingById($id)
    {
        $meeting = Meeting::find($id);

        if (!$meeting) {
            return response()->json(['error' => 'Meeting not found'], 404);
        }

        return response()->json($meeting);
    }
}
