
@section('users')

    @if(count($users) > 0)

        @foreach($users as $user)
            <div class="post-container bg-white mt-3 p-3">
                <div class="post-owner d-flex align-items-center">
                    @if($user->personal_image)
                        <div class="owner-img">
                            <a style="display: inline" href="{{route('profile',$user->id)}}"><img src="{{asset('media')}}/{{$user->personal_image}}" class="rounded-circle" /></a>
                        </div>
                    @else
                        <div class="owner-img">
                            <a style="display: inline" href="{{route('profile',$user->id)}}"><img src="{{asset('media')}}/img.jpg" class="rounded-circle" /></a>
                        </div>
                    @endif
                    <div class="owner-name pl-3">
                        <a href="{{route('profile',$user->id)}}"><b>
                                {{$user->name}}
                            </b></a>
                    </div>
                    <div class="post-option ml-auto pr-3">
                        <a id="search-block-btn-{{$user->id}}" onclick="addBlockSubmit({{$user->id}})" class="btn btn-warning text-white">{{$user->block}}</a>
                        <form id="search-block-form-{{$user->id}}" action="{{ route('addfriend') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                            @csrf
                            <input type="hidden" id="block-receiver-{{$user->id}}" name="receiverId" value="{{$user->id}}">
                            <input type="hidden" id="block-sender-{{$user->id}}" name="senderId" value="{{auth()->user()->id}}">
                            <input type="hidden" name="requestType" id="block-request-type-{{$user->id}}" value="{{$user->block_type}}">
                        </form>
                        @if($user->friendship == 'receive friend request')
                            <div class="owner-name pl-3" id="friend-request-div-{{$user->id}}">
                                <a id="accept-friend-request-{{$user->id}}" onclick="friendRequestSubmit({{$user->id}},'accept')" class="btn btn-warning text-white">accept friend request</a>
                                <form id="accept-friend-request-form-{{$user->id}}" action="{{ route('addfriend') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="receiverId" value="{{$user->receiver}}">
                                    <input type="hidden" name="senderId" value="{{$user->sender}}">
                                    <input type="hidden" name="requestType" id="friend-request-type-{{$user->id}}" value="acceptFriendRequest">
                                </form>

                                <a id="remove-friend-request-{{$user->id}}" onclick="friendRequestSubmit({{$user->id}},'remove')" class="btn btn-warning text-white">remove friend request</a>
                                <form id="remove-friend-request-form-{{$user->id}}" action="{{ route('addfriend') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="receiverId" value="{{$user->receiver}}">
                                    <input type="hidden" name="senderId" value="{{$user->sender}}">
                                    <input type="hidden" name="requestType" id="friend-request-type-{{$user->id}}" value="refuseFriendRequest">
                                </form>
                            </div>
                        @else
                            <a id="search-friend-btn-{{$user->id}}" onclick="addFriendSubmit({{$user->id}},'search')" class="btn btn-warning text-white">{{$user->friendship}}</a>
                            <form id="search-friend-form-{{$user->id}}" action="{{ route('addfriend') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                @csrf
                                <input type="hidden" id="receiver-{{$user->id}}" name="receiverId" value="{{$user->receiver}}">
                                <input type="hidden" id="sender-{{$user->id}}" name="senderId" value="{{$user->sender}}">
                                <input type="hidden" name="requestType" id="search-request-type-{{$user->id}}" value="{{$user->request_type}}">
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection
