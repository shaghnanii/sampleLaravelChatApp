@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Chats</div>

                <div class="panel-body">
                   
                    <div id="mynotifications">
                        <div class="alert alert-success"></div>
                    </div>
                    
                </div>
                <div class="panel-footer">
                    <form action="/messages" method="POST">
                        @csrf
                        <input type="text" name="message" placeholder="Enter Message">

                        <input type="submit" value="send">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
@endsection