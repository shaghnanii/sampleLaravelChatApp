@extends('layouts.app')

@section('content')

    <div class="container">
        <h3 class=" text-center">Real Time Messages</h3>
        <div class="messaging">
            <div class="inbox_msg">
                <div class="inbox_people">
                    <div class="headind_srch">
                        <div class="recent_heading">
                            <h4>Recent Chats</h4>
                        </div>
                    </div>

                    {{-- conversation list starts here --}}
                    <div class="inbox_chat">

                        @if (count($conversations) > 0)

                            @foreach ($conversations as $item)
                                <div class="chat_list active_chat chat_style" id="chat_head"
                                    onclick="showThisChat({{ $item->messages }}, {{ $item->user_two }}, {{ Auth::user()->id }}, {{ $item->id }})">
                                    <div class="chat_people">
                                        <div class="chat_img"> <img src="https://ptetutorials.com/images/user-profile.png"
                                                alt="sunil"> </div>
                                        <div class="chat_ib">
                                            <h5>{{ $item->u_two->name }}
                                                <span class="chat_date">
                                                    <span class="dot">0</span>
                                                </span>
                                            </h5>
                                            <p>{{ $item->u_two->email }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="chat_list active_chat">
                                <div class="alert alert-warning">No Conversations Found</div>
                            </div>
                        @endif
                    </div>
                    {{-- conversation lists end here --}}
                </div>
                <div class="mesgs" id="chat_messages">
                    {{-- message chats sections here --}}
                </div>

            </div>
        </div>
    </div>

    <script>
        Echo.private('chat')
            .listen('MessageSent', (e) => {
                var array = [];
                console.log("Fetching messages");
                console.log(e.message.message);
                array.push(e.message.message);
                var content = document.getElementById("sent_message");
                var checkUser = e.message.message_to;

                var mID = "{{ Auth::user()->id }}";

                for (var i = 0; i < array.length; i++) {
                    if (checkUser == mID) {
                        content.innerHTML +=
                            '<div class="incoming_msg">' +
                            ' <div class="incoming_msg_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>' +
                            ' <div class="received_msg">' +
                            ' <div class="received_withd_msg">' +
                            ' <p> ' + array[i] + ' </p>' +
                            ' <span class="time_date"> 11:01 AM    |    June 9</span>' +
                            ' </div>' +
                            ' </div>' +
                            ' </div>';
                    }

                }
            });



        function showThisChat(list, fromID, myID, cID) {
            var array = [];

            var content = document.getElementById("chat_messages");

            content.innerHTML = ""
            var mContent = "";

            mContent = '<div class="msg_history" id="sent_message">';

            for (var i = 0; i < list.length; i++) {
                // console.log(list[i].message);
                array.push(list[i].message);
                var check = list[i].message_from;

                if (check != myID) {
                    mContent +=
                        '<div class="incoming_msg">' +
                        ' <div class="incoming_msg_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>' +
                        ' <div class="received_msg">' +
                        ' <div class="received_withd_msg">' +
                        ' <p> ' + array[i] + ' </p>' +
                        ' <span class="time_date"> 11:01 AM    |    June 9</span>' +
                        ' </div>' +
                        ' </div>' +
                        ' </div>';
                } else {
                    mContent +=
                        '<div class="outgoing_msg">' +
                        '<div class="sent_msg">' +
                        '<p> ' + array[i] + '</p>' +
                        '<span class="time_date"> 11:01 AM    |    June 9</span> ' +
                        '</div>' +
                        '</div>'
                }
            }


            mContent +=
                '</div>' +
                '<div class="type_msg"> ' +
                '    <div class="input_msg_write"> ' +
                '        <form action="send" method="POST" id="test"> ' +
                '            @csrf ' +
                '            <input type="hidden" id="mID" name="mID" value="' + myID + '"/>' +
                '            <input type="hidden" id="fID" name="fID" value="' + fromID + '"/>' +
                '            <input type="hidden" id="cID" name="cID" value="' + cID + '"/>' +
                '            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />' +
                '            <input type="text" class="write_msg" id="message" name="message" placeholder="Type a message" /> ' +
                '            @error('
                message ') ' +
                    '                <span style="color: red">{{ $message }}</span> ' +
                '            @enderror ' +
                '            <button class="msg_send_btn" id="mSubmitBtn" type="submit"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button> ' +
                '         ' +
                '        </form> ' +
                '    </div> ' +
                '</div> ';

            content.innerHTML = mContent;
        }

    </script>


    <script>
        $(document).ready(function(e) {
            $("#test").submit(function(event) {
                event.preventDefault();
                return false;
                $.ajax({
                    url: "/send",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        message: $("input[name=message]").val(),
                        mID: $("mID").val(),
                        fID: $("fID").val(),
                        cID: $("cID").val(),
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(dataResult) {
                        console.log(dataResult);
                        // do add the sent message to the body here 
                    }
                });
                event.preventDefault();
            });

        });

    </script>


@endsection
