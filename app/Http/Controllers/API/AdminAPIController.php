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
        // if (auth()->user()->role !== 'admin') {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }
        
        $pending = User::where('is_approved', false)->get();
        // return response()->json($pending);
        return UserResource::collection($pending);

    }

    // Approve a specific user
    public function approveUser($id)
    {
        // if (auth()->user()->role !== 'admin') {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }
        
        $user = User::findOrFail($id);
        $user->is_approved = true;
        $user->save();
        return new UserResource($user);


        // return response()->json(['message' => 'User approved successfully']);
    }

    // Reject (delete) a specific user
    public function rejectUser($id)
    {
       // if (auth()->user()->role !== 'admin') {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }
 
        
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
