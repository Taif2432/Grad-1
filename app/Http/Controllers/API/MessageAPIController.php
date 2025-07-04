<?php

namespace App\Http\Controllers\API; 

use App\Events\NewMessage;
use App\Models\Message;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageAPIController extends APIController
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'session_id' => 'required|exists:sessions,id',
        'message' => 'required|string',
    ]);


  \Log::info(' creating///', $validated);
    $message = Message::create([
        'session_id' => $validated['session_id'],
        'sender_id' => Auth::id(),
        'message' => $validated['message'],
    ]);
    \Log::info(' Message saved', ['id' => $message->id]);

    $message->load('sender');

     //Broadcast the message
    \Log::info('Auth ID:' . Auth::id());
    broadcast(new NewMessage($message))->toOthers();
    \Log::info('Storing .....2');

    return response()->json([
    'status' => 'success',
    'message' => 'Message sent successfully.',
    'data' => $message-> ToArray(),
]);
}

    public function history($session_id)
{
    // Load  session (to check is_anonymous and client_id)
    $session = Session::findOrFail($session_id);

    // Eager-load sender, then get messages in chronological order
    $messages = Message::with('sender:id,name')
        ->where('session_id', $session_id)
        ->orderBy('created_at')
        ->get()
        // Map into a clean array
        ->map(function ($msg) use ($session) {
            // Determine masked or real sender name
            $senderName = ($session->is_anonymous && $msg->sender_id === $session->client_id)
                ? 'Anonymous Client'
                : $msg->sender->name;

            return [
                'id'          => $msg->id,
                'message'     => $msg->message,
                'sender_name' => $senderName,
                'created_at'  => $msg->created_at->toDateTimeString(),
            ];
        });

    // Return the clean collection
    return response()->json($messages);
}
}
