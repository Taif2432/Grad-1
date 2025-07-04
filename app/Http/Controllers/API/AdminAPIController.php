<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SessionLog;
use App\Http\Resources\SessionLogResource;
use App\Http\Resources\UserResource;


class AdminAPIController extends APIController
{
    // Get all pending users
    public function pendingUsers()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $pending = User::where('is_approved', false)->get();
        return UserResource::collection($pending) ->additional([ 'message'=> 'Pending users:']); 

    }
 
    // Approve a specific user
    public function approveUser($id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $user = User::findOrFail($id);
        $user->is_approved = true;
        $user->save();
        return (new UserResource($user)) ->additional([ 'message'=> 'User approved successfully']);
    }

    // Reject a specific user
    public function rejectUser($id)
    {
       if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User rejected and deleted'], 200);
    }


public function viewLogs()
{
    $logs = SessionLog::with('session.client', 'session.professional')
              ->orderByDesc('created_at')
              ->get();

    return SessionLogResource::collection($logs);
}


}
