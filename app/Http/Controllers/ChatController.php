<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $messages = Message::where('user_id', Auth::user()->id)->get();
        return view("home")->with('messages', $messages);

    }

    public function sendMessage(Request $request)
    {
        $user = Auth::user();   

        $message = $user->messages()->create([
            'message' => $request->input('message')
        ]);

        // We are using the toOthers() which allows us to exclude the current user from the broadcast's recipients.
        broadcast(new MessageSent($user, $message))->toOthers();

    }

    public function fetchMessages() {
        return Message::with('user')->get();
    }

}
