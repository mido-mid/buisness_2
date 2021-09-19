
@section('friendship')
    <a id="search-friend-btn-{{$user->id}}" onclick="addFriendSubmit({{$user->id}},'search')" class="btn btn-warning text-white">{{$user->friendship}}</a>
    <form id="search-friend-form-{{$user->id}}" action="{{ route('addfriend') }}" method="POST" enctype="multipart/form-data" style="display: none;">
        @csrf
        <input type="hidden" id="receiver-{{$user->id}}" name="receiverId" value="{{$user->receiver}}">
        <input type="hidden" id="sender-{{$user->id}}" name="senderId" value="{{$user->sender}}">
        <input type="hidden" name="requestType" id="search-request-type-{{$user->id}}" value="{{$user->request_type}}">
    </form>
@endsection
