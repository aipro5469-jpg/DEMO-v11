<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FieldReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class FieldReportController extends Controller
{
    /**
     * Display a listing of field reports.
     */
    public function index(Request $request)
    {
        $query = FieldReport::query();

        // Filter by task
        if ($request->has('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        // Filter by reporter
        if ($request->has('reporter_id')) {
            $query->where('reporter_id', $request->reporter_id);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($reports);
    }

    /**
     * Store a newly created field report.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'nullable|exists:tasks,id',
            'notes' => 'nullable|string',
            'location' => 'nullable|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $photosPaths = [];

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('field_reports', 'public');
                $photosPaths[] = $path;
            }
        }

        $report = FieldReport::create([
            'task_id' => $request->task_id,
            'reporter_id' => auth()->id(),
            'notes' => $request->notes,
            'location' => $request->location,
            'photos' => $photosPaths,
        ]);

        return response()->json([
            'message' => 'Field report created successfully',
            'data' => $report
        ], 201);
    }

    /**
     * Display the specified field report.
     */
    public function show($id)
    {
        $report = FieldReport::find($id);

        if (!$report) {
            return response()->json([
                'message' => 'Field report not found'
            ], 404);
        }

        // Generate full URLs for photos
        if ($report->photos) {
            $report->photos_urls = array_map(function ($path) {
                return Storage::url($path);
            }, $report->photos);
        }

        return response()->json($report);
    }

    /**
     * Update the specified field report.
     */
    public function update(Request $request, $id)
    {
        $report = FieldReport::find($id);

        if (!$report) {
            return response()->json([
                'message' => 'Field report not found'
            ], 404);
        }

        // Only allow reporter to update their own reports
        if ($report->reporter_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
            'location' => 'nullable|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $photosPaths = $report->photos ?? [];

        // Handle new photo uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('field_reports', 'public');
                $photosPaths[] = $path;
            }
        }

        $report->update([
            'notes' => $request->notes ?? $report->notes,
            'location' => $request->location ?? $report->location,
            'photos' => $photosPaths,
        ]);

        return response()->json([
            'message' => 'Field report updated successfully',
            'data' => $report
        ]);
    }

    /**
     * Remove the specified field report.
     */
    public function destroy($id)
    {
        $report = FieldReport::find($id);

        if (!$report) {
            return response()->json([
                'message' => 'Field report not found'
            ], 404);
        }

        // Only allow reporter to delete their own reports
        if ($report->reporter_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Delete associated photos
        if ($report->photos) {
            foreach ($report->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $report->delete();

        return response()->json([
            'message' => 'Field report deleted successfully'
        ]);
    }

    /**
     * Delete a specific photo from a field report.
     */
    public function deletePhoto($id, Request $request)
    {
        $report = FieldReport::find($id);

        if (!$report) {
            return response()->json([
                'message' => 'Field report not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'photo_path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $photos = $report->photos ?? [];
        $photoPath = $request->photo_path;

        if (in_array($photoPath, $photos)) {
            // Remove from array
            $photos = array_values(array_diff($photos, [$photoPath]));

            // Delete file
            Storage::disk('public')->delete($photoPath);

            // Update report
            $report->update(['photos' => $photos]);

            return response()->json([
                'message' => 'Photo deleted successfully'
            ]);
        }

        return response()->json([
            'message' => 'Photo not found in this report'
        ], 404);
    }
}
