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
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $contents = Content::where('professional_id', Auth::id())
        ->with('categories')
        ->latest()
        ->paginate(10);

    return response()->json(['contents' => $contents]);
}


    /**
     * Show the form for creating a new resource.
     */
   public function store(ContentRequest  $request)
{
    $filePath = null;
    if ($request->hasFile('file')) {
        $filePath = $request->file('file')->store('contents');
    }

    $content = Content::create([
        'title' => $request->title,
        'description' => $request->description,
        'content_type_id' => $request->content_type_id,
        'type' => $request->type,
        'file_path' => $filePath,
        'professional_id' => Auth::id(),
    ]);

    // Optionally run category detection
    $text = $request->title . ' ' . $request->description;
    $category = $this->categorizeContentWithPython($text);

    // Attach category by name (assumes names are unique)
    $categoryModel = \App\Models\Category::firstOrCreate(['name' => $category]);
    $content->categories()->sync([$categoryModel->id]);

    return response()->json([
        'message' => 'Content created successfully',
        'data' => $content->load('categories')
    ]);
 }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
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


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $content = Content::findOrFail($id);
        if ($content->professional_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $content->delete();
        return response()->json(['message' => 'Content deleted']);
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

public function storeAvailability(AvailabilityRequest $request)
{
    $data = $request->validated(); 

    $data['professional_id'] = auth()->id();

    $availability = Availability::create($data);
    // ([
    //     'professional_id' => auth()->id(),
    //     'available_date' => $request->date,
    //     'start_time' => $request->start_time,
    //     'end_time' => $request->end_time,
    // ]);

    return response()->json([
        'message' => 'Availability added successfully.',
        'availability' => $availability,
    ], 201);
}


//     protected function categorizeContentWithPython($text)
// {
//     $process = proc_open(
//         'python storage/python/ai_categorization/categorize_content.py',
//         [
//             0 => ['pipe', 'r'], // stdin
//             1 => ['pipe', 'w'], // stdout
//             2 => ['pipe', 'w'], // stderr
//         ],
//         $pipes
//     );

//     if (is_resource($process)) {
//         // Properly encode and write raw JSON to stdin
//         $payload = json_encode(['text' => $text]);
//         fwrite($pipes[0], $payload);
//         fclose($pipes[0]);

//         // Capture output and error
//         $result = stream_get_contents($pipes[1]);
//         fclose($pipes[1]);

//         $error = stream_get_contents($pipes[2]);
//         fclose($pipes[2]);

//         proc_close($process);

//         // Log if error
//         if (trim($error)) {
//             logger()->error("Python Error: $error");
//         }

//         return trim($result);
//     }

//     return 'Uncategorized';
// }

}
