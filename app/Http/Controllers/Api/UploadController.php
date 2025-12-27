<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // Max 10MB
            'folder' => 'nullable|string',
            'filename' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $folder = $request->input('folder', 'uploads');
            $filename = $request->input('filename');

            // Store file
            if ($filename) {
                $path = $file->storeAs($folder, $filename, 'public');
            } else {
                $path = $file->store($folder, 'public');
            }

            return response()->json([
                'path' => $path,
                'url' => Storage::url($path),
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
