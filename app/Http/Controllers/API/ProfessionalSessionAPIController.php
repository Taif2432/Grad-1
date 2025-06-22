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
// use App\Services\GenerateAgoraTokenService;


class ProfessionalSessionAPIController extends APIController
{ 
      // View upcoming sessions
    public function upcomingSessions()
     {
        $professional_id = Auth::id();

        $upcomingSessions = Session::with('client')
                ->where('professional_id', $professional_id)
                ->where('scheduled_at', '>=', now()) // sessions in the future
                ->where('status', '!=', 'cancelled') // exclude cancelled sessions
                ->orderBy('scheduled_at', 'desc')
                ->get();
    // $allSessions = Session::with('client')->get();
    return SessionResource::collection($upcomingSessions);

     }
 
      // View past sessions
    public function pastSessions()
     {
        $sessions = Session::where('professional_id', Auth::id())
        ->where('scheduled_at', '<', now())
        ->whereIn('status', ['completed', 'cancelled']) // assuming cancelled is also "in the past"
        ->with('client')
        ->orderBy('scheduled_at', 'desc')
        ->get();

        return response()->json(['past_sessions' => $sessions]);
     }
    
        // Join a session
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
    $data = $request->validated(); 

    $data['professional_id'] = auth()->id();

    $availability = Availability::create($data);

    return response()->json([
        'message' => 'Availability added successfully.',
        'availability' => $availability,
    ], 201);
}

// Cancel a session
public function cancelSession($id)
{
    $session = Session::where('id',$id)
                      ->where('professional_id',auth()->id())
                    //   ->firstOrFail();
                    ->first();
        if (!$session) {
        return response()->json(['message' => 'Session not found or you do not have permission to cancel it.'], 404);
    }

    // $session->update(['status'=>'cancelled']);
    $session->status = 'cancelled';
    $session->save();


    // Log cancellation
    SessionLog::create([
        'session_id' => $session->id,
        'ended_at' => Carbon::now(),
        'notes' => 'Professional cancelled session',
    ]);
    
    $session->client->notify(new SessionCancelled($session,'professional'));
    // return response()->json(['message'=>'Cancelled successfully']);
    return response()->json([
        'success' => true,
        'message' => 'Session has been successfully cancelled.',
        'session' => $session
    ]);
}
}
