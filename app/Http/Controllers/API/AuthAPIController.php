<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest; // Import RegisterRequest
use App\Http\Requests\LoginRequest;    // Import LoginRequest
use App\Http\Requests\DeleteAccountRequest; // Import DeleteAccountRequest
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\UserResource;



class AuthAPIController extends APIController
{
    public function register(RegisterRequest $request)
     { 
        // if ($request->role === 'admin' && $request->admin_code !== env('ADMIN_SECRET_CODE')) {
        //     return response()->json(['message' => 'Invalid admin code.'], 403);
        // }
   
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
        // 'role' => $validated['role'],
        'is_approved' => $request->role === 'admin' ? true : false, // auto approve admin
    ]);

    return response()->json([
        'message' => 'Accounted created. Awaiting for admin approval.',
        // 'user' => $user,
        // $user = User::findOrFail($id),
         new UserResource($user),
    ], 201);
    
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
    return response()->json($request->user());
}

public function updateAccount(UpdateAccountRequest $request)
{
    $user = $request->user();

    $user->update($request->validated());

    return response()->json(['message' => 'Account updated successfully.',     new UserResource($user),
]); 
    // 'user' => $user
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
