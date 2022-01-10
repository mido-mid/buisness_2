@extends('layouts.app')
<link href="{{ asset('css/styles/chat.css') }}" rel="stylesheet">
@section('content')
     <section id="ez-body__center-content" class="col-lg-8 mt-3">
	 <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />

<div class="container">
<div class="row clearfix">
    <div class="col-lg-12">
        <div id="selectMe" class="card chat-app">
            <div id="plist" class="people-list">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                    </div>
                    <input type="text" id="myInput" onkeyup="searchChats()" class="form-control" placeholder="Search...">
                </div>
                <ul id="myUL" class="list-unstyled chat-list mt-2 mb-0">
                @foreach($rooms as $room)
                
                    <li  onclick = printChat({{json_encode($room)}},{{json_encode($chats)}}),printSentMessage({{json_encode($room)}}),setAllMessagesRead() class="clearfix">
                        <img src={{$room['senderImage']}} alt="avatar">
                        <div class="about">
                            <div class="name"><a>{{$room['senderName']}}</a></div>
                            <div id = "lastmessage" class="status"><i class="fa fa-circle offline"></i><?=str_replace('-', ' ',  $room['lastMessage'])?></div>                                            
                        </div>
                    </li>
                @endforeach
                </ul>
            </div>
            <div id="unique-chat" class="chat">
                <form id="unique-form" method = "post">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>
</div>
    </section>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-firestore.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>
    <script src="{{ asset('js/firebase.js')}}"></script>
    <script src="{{ asset('js/chat.js')}}"></script>
    <script src="{{ asset('js/vanillaEmojiPicker.js')}}"></script>
    <Script>
        new EmojiPicker({
    trigger: [
        {
          selector: '.emojiIcon',
          insertInto: ['.messageInput'] // '.selector' can be used without array
        },
    ],
    closeButton: true,
});
    </script>
@endsection

