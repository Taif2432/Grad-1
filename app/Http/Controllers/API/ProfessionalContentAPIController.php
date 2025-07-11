<?php

namespace App\Http\Controllers\API;
 
use App\Models\Content;
use App\Models\Category;
use App\Models\ContentType;
use App\Models\Availability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\ContentRequest;
use App\Http\Requests\AvailabilityRequest;
use storage\python\ai_categorization\categorize_content;

class ProfessionalContentAPIController extends APIController
{
    public function index()
{
    $contents = Content::where('professional_id', Auth::id())
        ->with('categories')
        ->latest()
        ->paginate(10);

    // Add full URL for each content's file path if exists
    $contents->getCollection()->transform(function ($content) {
        if ($content->file_path) {
            $content->file_url = asset('storage/' . $content->file_path);
        } else {
            $content->file_url = null;
        }
        return $content;
    });

    return response()->json(['contents' => $contents]);
}

//    public function store(ContentRequest  $request)
// {
//     $file_Path = null;
//     if ($request->hasFile('file')) {
//     $file_Path = $request->file('file')->store('contents', 'public');
// }

//     $content = Content::create([
//         'title' => $request->title,
//         'description' => $request->description,
//         'content_type_id' => $request->content_type_id,
//         'type' => $request->type,
//         'file_Path' => $file_Path,
//         'professional_id' => Auth::id(),
//     ]);

//     // Optionally run category detection
//     $text = $request->title . ' ' . $request->description;
//     $category = $this->categorizeContentWithPython($text);

//     // Attach category by name (assumes names are unique)
//     $categoryModel = \App\Models\Category::firstOrCreate(['name' => $category]);
//     $content->categories()->sync([$categoryModel->id]);

//     return response()->json([
//         'message' => 'Content created successfully',
//         'data' => $content->load('categories')
//     ]);
//  }
public function store(ContentRequest $request)
{
    $filePath = null;

    // Handle file upload first
    if ($request->hasFile('file')) {
        $filePath = $request->file('file')->store('contents', 'public');
    }

    // Create content with file path
    $content = Content::create([
        'title' => $request->title,
        'description' => $request->description,
        'content_type_id' => $request->content_type_id,
        'type' => $request->type,
        'file_path' => $filePath, // this is now correct
        'professional_id' => Auth::id(),
    ]);

    // AI-based auto-categorization
    $text = $request->title . ' ' . $request->description;
    $category = $this->categorizeContentWithPython($text);
    $categoryModel = \App\Models\Category::firstOrCreate(['name' => $category]);
    $content->categories()->sync([$categoryModel->id]);

    return response()->json([
        'message' => 'Content created successfully',
        'data' => $content->load('categories')
    ]);
}

   public function update(ContentRequest $request, string $id)
{
    $content = Content::findOrFail($id);

    if ($content->professional_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $data = $request->only(['title', 'description', 'type']);

    if ($request->hasFile('file')) {
        $data['file_path'] = $request->file('file')->store('contents');
    }

    // Re-categorize if title or description changes
    if ($request->filled('title') || $request->filled('description')) {
        $text = ($data['title'] ?? $content->title) . ' ' . ($data['description'] ?? $content->description);
        $category = $this->categorizeContentWithPython($text);

        // Update category relationship
        $categoryModel = \App\Models\Category::firstOrCreate(['name' => $category]);
        $content->categories()->sync([$categoryModel->id]);
    }

    $content->update($data);

    return response()->json(['message' => 'Content updated', 'data' => $content->load('categories')]);
}
    public function destroy(string $id)
    {
        $contents = Content::findOrFail($id);
        if ($contents->file_path) {
       Storage::disk('public')->delete($contents->file_path);
       }
        $contents->delete();

        return response()->json(['message' => 'Content deleted by pro']);
    }

public function categorizeContentWithPython($text)
{
     $escapedText = json_encode(['text' => $text]);
     
    $scriptPath = base_path('storage/python/ai_categorization/categorize_content.py');
$process = proc_open(
    "python \"$scriptPath\"",
    [
        0 => ['pipe', 'r'], // stdin
        1 => ['pipe', 'w'], // stdout
        2 => ['pipe', 'w']  // stderr
    ],
    $pipes
);

    if (is_resource($process)) {
        fwrite($pipes[0], $escapedText);
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        proc_close($process);

        \Log::info('Sending to Python:', ['input' => $escapedText]);
\Log::info('Python STDOUT:', ['output' => $output]);
\Log::error('Python STDERR:', ['error' => $error]);

        return trim($output) ?: 'Uncategorized';
    }

    return 'Uncategorized';
}


}
