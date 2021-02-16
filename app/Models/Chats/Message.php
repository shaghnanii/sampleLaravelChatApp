<?php

namespace App\Models\Chats;
use App\Models\Chats\Conversation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['message', 'message_from', 'message_to'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }
    
}
