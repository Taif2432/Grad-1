<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest; 
use App\Http\Requests\LoginRequest;    
use App\Http\Requests\DeleteAccountRequest; 
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\UserResource;



class AuthAPIController extends APIController
{
    public function register(RegisterRequest $request)
     { 
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
        'is_approved' => ($request->role === 'client' || $request->role === 'admin')  ? true : false, // auto approve admin & client
    ]);

    $message = '';
        if ($user->is_approved) {
            // Message for clients/admins who are auto-approved
            $message = 'Account created successfully. You can now log in.';
        } else {
            // Message for professionals awaiting approval
            $message = 'Account created. Awaiting administrator approval.';
        }

        return response()->json([
            'message' => $message,
            'user' => new UserResource($user),
        ], 201); // 201 Created status code is appropriate
    }
    
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if (!$user->is_approved) {
            return response()->json(['message' => 'Account not approved'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ]);
        
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }

public function deleteAccount(DeleteAccountRequest $request)
{
    $user = $request->user();

    if (!Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Incorrect password.'], 403);
    }

    $user->tokens()->delete(); // Revoke tokens
    $user->delete(); // Delete account

    return response()->json(['message' => 'Your account has been deleted successfully.']);
 }
 public function viewAccount(Request $request)
{
    return response()->json([
    'user' => new UserResource($request->user()),
     ]);
}

public function updateAccount(UpdateAccountRequest $request)
{
    $user = $request->user();

    $user->update($request->validated());

    return response()->json(['message' => 'Account updated successfully.', 
    'user' => new UserResource($user),
]); 
}

public function updatePassword(UpdatePasswordRequest $request)
{
    $user = $request->user();

    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json(['message' => 'Current password is incorrect.'], 403);
    }

    $user->update([
        'password' => Hash::make($request->new_password),
    ]);

    return response()->json(['message' => 'Password updated successfully.']);
}


}
