<?php

namespace App\Http\Controllers\Chats;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Chats\Conversation;
use App\Models\Chats\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ChatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $id = Auth::user()->id;
        $user = User::find($id);
        // DB::enableQueryLog();
        $conversations = Conversation::where('user_one',auth()->user()->id)
                            ->orWhere('user_two',auth()->user()->id)
                            ->with(['messages', 'u_two','u_one'])
                            ->get();
        // dd(DB::getQueryLog());
        // return $conversations;
        return view('home')->with('conversations', $conversations);

    }

    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        // return $request->mID . ' ' .  $request->fID;
        
        $conversations = Conversation::where('user_one', $user->id)
                            ->orWhere('user_two', $user->id)
                            ->first();
        if($conversations == null){
            // no conversation found , create one
            return "create converstion";
        }
        else {
            // send message
            $message = new Message(
                [
                    'message' => $request->message,
                    'message_from' => $request->mID,
                    'message_to' => $request->fID,
                ]
            );
            // return $message;
            $conversations = Conversation::find($request->cID);

            $conversations->messages()->save($message);
            // We are using the toOthers() which allows us to exclude the current user from the broadcast's recipients.
            broadcast(new MessageSent($user, $message))->toOthers();

            return response()->json(['success' => "sent"]);
        }
    }

    public function fetchMessages() {
        return Message::with(['conversation', 'conversation.u_one', 'conversation.u_two'])->get();
    }
}
