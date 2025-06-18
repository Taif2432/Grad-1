<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\SessionLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SessionCancelled;
use App\Http\Requests\AvailabilityRequest; 
use App\Models\Availability;
use App\Http\Resources\SessionResource;
// /use App\Services\GenerateAgoraTokenService;


class ProfessionalSessionAPIController extends APIController
{
      // View upcoming sessions
    public function upcomingSessions()
     {
    $allSessions = Session::with('client')->get();
    return SessionResource::collection($allSessions);

     }
 
      // View past sessions
    public function pastSessions()
     {
        Session::markPastSessionsCompleted();

        $sessions = Session::where('professional_id', Auth::id())
        ->where('scheduled_at', '<', now())
        ->whereIn('status', ['completed', 'cancelled']) // assuming cancelled is also "in the past"
        ->with('client')
        ->orderBy('scheduled_at', 'desc')
        ->get();

        return response()->json(['past_sessions' => $sessions]);
     }
    
        // Join a session (placeholder logic)
    public function joinSession($id)
     {
        $session = Session::where('id', $id)
        ->where('professional_id', Auth::id())
        ->first();

    if (!$session) {
        return response()->json(['message' => 'Session not found'], 404);
    }

    // block joining before scheduled time
    if ($session->status !== 'scheduled') {
        return response()->json(['message' => 'Session is not in a joinable state.'], 403);
    }

    $session->update(['status' => 'ongoing']);

    // Log join
    SessionLog::create([
        'session_id' => $session->id,
        'started_at' => Carbon::now(),
        'notes' => 'Professional joined session',
    ]);


    return response()->json(['message' => 'Session started', 'session' => $session]);
}

// private function generateAgoraToken($channel)
// {
//     return 'dummy_audio_token_'. $channel; 
// }
  // Mark session as completed
public function completeSession($id)
{
    $session = Session::where('id', $id)
        ->where('professional_id', Auth::id())
        ->first();

    if (!$session) {
        return response()->json(['message' => 'Session not found'], 404);
    }

    $session->status = 'completed';
    $session->save();

    return response()->json(['message' => 'Session marked as completed.']);
}

public function storeAvailability(AvailabilityRequest $request)
{
    $availability = Availability::create([
        'professional_id' => auth()->id(),
        'available_date' => $request->available_date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
    ]);

    return response()->json([
        'message' => 'Availability successfully added.',
        'data' => $availability
    ], 201);

}

// Cancel a session
public function cancelSession($id)
{
    $session = Session::where('id',$id)
                      ->where('professional_id',auth()->id())
                      ->firstOrFail();
    $session->update(['status'=>'cancelled']);

    // Log cancellation
    SessionLog::create([
        'session_id' => $session->id,
        'ended_at' => Carbon::now(),
        'notes' => 'Professional cancelled session',
    ]);
    
    $session->client->notify(new SessionCancelled($session,'professional'));
    return response()->json(['message'=>'Cancelled']);
}
}
