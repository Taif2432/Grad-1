<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Content; 

class UserContentAPIController extends APIController
{
    
    public function index(Request $request)
{
    $query = Content::with('categories')->latest();

    // Filter by category ID
    if ($request->has('category_id')) {
        $query->whereHas('categories', function ($q) use ($request) {
            $q->where('categories.id', $request->category_id);
        });
    }

    // Filter by category name
    if ($request->has('name')) {
        $query->whereHas('categories', function ($q) use ($request) {
            $q->where('name', $request->name);
        });
    }

    // Filter by type (e.g., article, video, pdf)
    if ($request->has('type')) {
        $query->where('type', $request->type);
    }

    // Filter by search in title
    if ($request->has('search')) {
        $search = $request->search;
        $query->where('title', 'like', "%$search%");
    }

    $contents = $query->paginate(10);
    return response()->json(['contents' => $contents]);
}

    public function show(string $id)
    {
        $content = Content::with('categories', 'professional')->findOrFail($id);
        return response()->json(['content' => $content]);
    }

    public function getAvailableTypes()
{
    $types = ContentType::all();
        return response()->json(['content_types' => $types]);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
