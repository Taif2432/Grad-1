<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\SessionLog;
use Illuminate\Support\Facades\Storage;


class AdminContentAPIController extends APIController
{
    public function index()
    {
        $contents = Content::with('categories', 'professional')->latest()->paginate(10);
        return response()->json(['contents' => $contents]);

    }

    public function destroy(string $id)
    {
        $contents = Content::findOrFail($id);
        if ($contents->file_path) {
       Storage::disk('public')->delete($contents->file_path);
       }
        $contents->delete();

        return response()->json(['message' => 'Content deleted by admin']);
    }
}    
