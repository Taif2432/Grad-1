<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\SessionFeedback;
use App\Models\Availability;
use App\Models\SessionLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SessionRequest;
use App\Notifications\SessionCancelled;
use App\Notifications\SessionConfirmed;


class ClientSessionAPIController extends APIController
{
        // Book a session
    public function bookSession(SessionRequest $request)
    {
    $validated = $request->validated();

    $session = Session::create([
        'client_id' => auth()->id(),
        'professional_id' => $validated['professional_id'],
        'scheduled_at' => $validated['scheduled_at'],
        'session_type' => $validated['session_type'],
    ]);

    // notify both
    $session->client->notify(new SessionConfirmed($session));
    $session->professional->notify(new SessionConfirmed($session));

    return response()->json(['message' => 'Session booked successfully', 'session' => $session], 201);
    }
       // View client's booked sessions
    public function mySessions()
    {

    $sessions = Session::where('client_id', Auth::id())
    ->with('professional')->orderBy('scheduled_at', 'desc')->get();

    return response()->json(['my_sessions' => $sessions]);
    }

    public function cancelMySession($id)
{
    $session = Session::where('id',$id)
                      ->where('client_id',auth()->id())
                      ->firstOrFail();
    $session->update(['status'=>'cancelled']);

     // Log cancellation
    SessionLog::create([
        'session_id' => $session->id,
        'ended_at' => Carbon::now(),
        'notes' => 'Client cancelled session',
    ]);
    
    $session->professional->notify(new SessionCancelled($session,'client'));
    return response()->json(['message'=>'Session Cancelled']);
}

public function joinSession($id)
{
    $session = Session::where('id', $id)
        ->where('client_id', Auth::id())
        ->whereIn('status', ['scheduled', 'ongoing'])
        ->first();

    if (!$session) {
        return response()->json(['message' => 'Session not found or not accessible'], 404);
    }

    // Log the join
    SessionLog::create([
        'session_id' => $session->id,
        'started_at' => Carbon::now(),
        'notes' => 'Client joined session',
    ]);

    return response()->json([
        'message' => 'Session ready to join',
        'session' => $session
    ]);
}

// public function endSession($sessionId)
// {
//     $log = SessionLog::where('session_id', $sessionId)->latest()->first();

//     if ($log && is_null($log->ended_at)) {
//         $log->update([
//             'ended_at' => Carbon::now(),
//             'notes' => 'Session ended normally.'
//         ]);
//     }

//     return response()->json(['message' => 'Session ended and log updated.']);
// }


public function deleteSession($id)
{
    $session = Session::find($id);

    if (!$session) {
        return response()->json([
            'success' => false,
            'message' => 'Session not found.'
        ], 404);
    }

    // Check if the user is authorized to delete this session
    if (auth()->user()->id !== $session->client_id) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized.'
        ], 403);
    }

    $session->delete();

    return response()->json([
        'success' => true,
        'message' => 'Session deleted successfully.'
    ]);
}

public function viewAvailableSessions(Request $request)
{
    $availabilities = Availability::with('professional')
        ->where('available_date', '>=', now()->toDateString())
        ->orderBy('available_date')
        ->get();

    return response()->json($availabilities);
}

public function feedback(Request $request)
{
    $request->validate([
        'session_id' => 'required|exists:sessions,id',
        'rating' => 'required|integer|min:1|max:5',
        'comments' => 'nullable|string',
    ]);

    $feedback = SessionFeedback::updateOrCreate(
        [
            'session_id' => $request->session_id,
        ],
        [
            'rating' => $request->rating,
            'comments' => $request->comments,
        ]
    );

    return response()->json([
        'message' => 'Feedback saved successfully.',
        'data' => $feedback,
    ]);
}
}
