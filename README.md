<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Laravel Chat App Using Pusher 

## Laravel Chat App, Using Pusher

### First All all we need to uncomment the BroadcastServiceProvider from config/app.php from the providers list.

```
  // App\Providers\BroadcastServiceProvider      
```
### Next thing we required is to enable pusher  from the .env
```
BROADCAST_DRIVER=pusher
```
			
### Then we need to install the pusher package using composer command.
```
composer require pusher/pusher-php-server
```

## Setting Up Pusher
### Open config/broadcasting.php and modify the options section in the pusher accrodingly

```
  'pusher' => [
      'driver' => 'pusher',
      'key' => env('PUSHER_APP_KEY'),
      'secret' => env('PUSHER_APP_SECRET'),
      'app_id' => env('PUSHER_APP_ID'),
      'options' => [
          'cluster' => env('PUSHER_CLUSTER'),
          'encrypted' => true,
      ],
  ],
```

### Now update the env file with the pusher ID an Keys.
```
PUSHER_APP_ID=xxxxxx
PUSHER_APP_KEY=xxxxxxxxxxxxxxxxxxxx
PUSHER_APP_SECRET=xxxxxxxxxxxxxxxxxxxx
PUSHER_CLUSTER=xx
```
### To subscribe and listen to events we need to install laravel-echo and pusher-js. 
```
npm install --save laravel-echo pusher-js
```

### IN resources/assets/js/bootstrap.js we need to uncomment the laravel-echo and pusher-js import section with sample code.
```
import Echo from "laravel-echo"

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'xxxxxxxxxxxxxxxxxxxx',
    cluster: 'eu',
    encrypted: true
});
```
### Note: Replace the x with your pusher app key, and use the same cluster which you had used before in your config/broadcasting.php.

## Authenticating Users
### Create the scrufholding and migrate your db as we do normally..

## Message Model and Migration
### Create a Message model along with the migration file by running the command:
```
php artisan make:model Message -m
```
### Open message Model and add the below line to it.
```
protected $fillable = ['message'];
```
### Within the databases/migrations directory, open the messages table migration that was created when we ran the command above and update the up method with:

```
Schema::create('messages', function (Blueprint $table) {
  $table->increments('id');
  $table->integer('user_id')->unsigned();
  $table->text('message');
  $table->timestamps();
});
```

### Run migration
```
php artisan migrate
```

## User To Message Relationship

### User can send as many message as he wants to there is a one to many relationship btw user and message. Add the following line in the user model to establish the relationship.
```
public function messages()
{
  return $this->hasMany(Message::class);
}
```
### Next, we need to define the inverse relationship by adding the code below to Message model:
```
public function user()
{
  return $this->belongsTo(User::class);
}
```

## Defining App Routes
### Let's create the routes our chat app will need. Open routes/web.php
```
Route::get('/', 'ChatsController@index');
Route::get('messages', 'ChatsController@fetchMessages');
Route::post('messages', 'ChatsController@sendMessage');
```

### update the redirectTo property of both app/Http/Controllers/Auth/LoginController.php and app/Http/Controllers/Auth/RegisterController.php to:
```
protected $redirectTo = '/';
```

## Chat Controller 
### Create a controller for chats using artisan command 
```
php artisan make:controller ChatsController
```

### Fill the controller with this code.
```
<?php

namespace App\Http\Controllers;

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
        return view("home");

    }

    public function sendMessage(Request $request){
        $user = Auth::user();

        $message = $user->messages()->create([
            'message' => $request->input('message')
        ]);

        return ['status' => 'Message Sent!'];
    }

    public function fetchMessages() {
        return Message::with('user')->get();
    }

}

```
## Creating The Chat App View

### In the resources/views/home.blade.php paste this code.
```
@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Chats</div>

                <div class="panel-body">
                    <chat-messages :messages="messages"></chat-messages>
                </div>
                <div class="panel-footer">
                    <chat-form
                        v-on:messagesent="addMessage"
                        :user="{{ Auth::user() }}"
                    ></chat-form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### Do some front end work here for chat view....

## Broadcasting Message Sent Event
### Create an event named as MessageSent.
```
php artisan make:event MessageSent
```

### This class must implement the ShouldBroadcast interface. The class should look like:
```
use App\User;
use App\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * User that sent the message
     *
     * @var User
     */
    public $user;

    /**
     * Message details
     *
     * @var Message
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Message $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chat');
    }
}
```
### Next, we need to update the sendMessage() of ChatsController to broadcast the MessageSent event:

```
public function sendMessage(Request $request)
{
  $user = Auth::user();

  $message = $user->messages()->create([
    'message' => $request->input('message')
  ]);

  broadcast(new MessageSent($user, $message))->toOthers();

  return ['status' => 'Message Sent!'];
}
```
### Since we created a private channel, only authenticated users will be able to listen on the chat channel. So, we need a way to authorize that the currently authenticated user can actually listen on the channel. This can be done by in the routes/channels.php file:

```
Broadcast::channel('chat', function ($user) {
  return Auth::check();
});
```

## Listening For Message Sent Event
### Add the following in the home.blade.php. using the following script you can append the upcomming requests to the UI view.

```
<script>
    Echo.private('chat')
  .listen('MessageSent', (e) => {
        var array = [];
        // console.log(e.message.message);
        array.push(e.message.message + " by " + e.user.name);
        var content = document.getElementById("mynotifications");

        for (var i = 0; i < array.length; i++) {
            content.innerHTML += '<div class="alert alert-success">' + array[i] + '</div>';
        }
  });
</script>
```
