<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function getUsers(){
        $users = User::all();

        return response()->json($users);
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
        'receiver_id' => 'required|exists:users,id',
        'message' => 'required',
        'type' => 'required|in:text,video,photo'
        ]);

        Message::create([
        'receiver_id' => $validated['receiver_id'],
        'message' => $validated['message'],
        'type' => $validated['type'],
        'sender_id' => auth()->id()
        ]);

        return response()->json([
        'message' => 'Message sent successfully!'
        ], 201);
    }

    public function getMessages($id){
        $user = auth()->user();

        $messages = Message::where(function($query) use($user, $id) {
            $query->where('sender_id', $user->id)->where('receiver_id', $id);
        })->orWhere(function($query) use ($user, $id){
             $query->where('sender_id', $id)->where('receiver_id',$user->id );
        })->get();

        return response()->json($messages);
    }

}


