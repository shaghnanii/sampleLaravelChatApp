<?php

namespace App\Models\Chats;

use App\Models\User;
use App\Models\Chats\Message;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    // public $primaryKey = "id";

    public function u_one(){
        return $this->belongsTo(User::class, 'user_one');
    }
    public function u_two(){
        return $this->belongsTo(User::class, 'user_two');
    }

    public function messages(){
        return $this->hasMany(Message::class, 'conversation_id');
    }
}
