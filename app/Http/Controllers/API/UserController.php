<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function register(Request $request){
       $request->validate([
        'name' => 'required',
        'email' => 'required|unique:users,email',
        'phone' => 'required|unique:users,phone',
        'date_of_birth' => 'required',
        'password' => 'required', 
        'profile_picture' => 'required|image'
       ]);

       $filePath = $request->profile_picture->store('/profile_picture', 'public');
       $profilePicture = Storage::url($filePath);

       $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'date_of_birth' => $request->date_of_birth,
        'password' => bcrypt($request->password),
        'profile_picture' => $profilePicture
       ]);

       $token = $user->createToken('auth')->plainTextToken;
       //this is how created token by auth sanctum


       return response()->json([
        'message' => 'User registered successfully',
        'data' => $user,
        'token' => $token
       ]);
    }

    public function login(Request $request){
    $request->validate([
        'email' => 'required|exists:users,email',
        'password' => 'required'
    ]);

    if(Auth::attempt($request->only('email', 'password'))){

        $user = Auth::user();

       $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'message' => 'User logged in',
            'data' => $user,
            'token' => $token
        ]);
    }

    return response()->json([
        'message' => 'Invalid Details'
    ], 401);
}
}
