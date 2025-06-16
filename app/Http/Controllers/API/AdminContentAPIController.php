<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\SessionLog;
use Illuminate\Support\Facades\Storage;


class AdminContentAPIController extends APIController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contents = Content::with('categories', 'professional')->latest()->paginate(10);
        return response()->json(['contents' => $contents]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    // $contents = Content::with('categories', 'professional')->findOrFail($id);
    // return response()->json(['contents' => $contents]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contents = Content::findOrFail($id);
        Storage::delete($contents->file_path);
        $contents->delete();

        return response()->json(['message' => 'Content deleted by admin']);
    }
}
