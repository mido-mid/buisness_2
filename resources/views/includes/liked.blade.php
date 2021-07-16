
@section('liked')
    <span class="reaction-btn">
        <!-- Default like button -->
        <span class="reaction-btn-emo like-btn-{{$post->user_react[0]->name}}" id="reaction-btn-emo-{{$post->id}}"></span>
    <!-- Default like button emotion-->
        <span class="reaction-btn-text reaction-btn-text-{{$post->user_react[0]->name}} active" onclick="unlikePostSubmit({{$post->id}},{{$post->user_react[0]->id}})" id="reaction-btn-text-{{$post->id}}">
            {{$post->user_react[0]->name}}
                <form id="unlike-form-{{$post->id}}-{{$post->user_react[0]->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                    @csrf
                    <input type="hidden" name="model_id" value="{{$post->id}}">
                    <input type="hidden" name="model_type" value="post">
                    <input type="hidden" name="reactId" value="{{$post->user_react[0]->id}}">
                   <input type="hidden" name="requestType" id="like-request-type-{{$post->id}}" value="delete">
                </form>
        </span>
    <!-- Default like button text,(Like, wow, sad..) default:Like  -->
        <ul class="emojies-box">
            @foreach($reacts as $react)
                <!-- Reaction buttons container-->
                    <li class="emoji emo-{{$react->name}}" id="react-{{$react->id}}" onclick="likePostSubmit({{$post->id}},{{$react->id}})" data-reaction="{{$react->name}}"></li>
                    <form id="like-form-{{$post->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                    @csrf
                    <input type="hidden" name="model_id" value="{{$post->id}}">
                    <input type="hidden" name="model_type" value="post">
                    <input type="hidden" name="reactId" value="{{$react->id}}">
                   <input type="hidden" name="requestType" id="like-request-type-{{$post->id}}" value="update">
                </form>
                @endforeach
        </ul>
  </span>
    <div class="like-stat">
        <!-- Like statistic container-->
        <span class="like-emo" id="like-emo-{{$post->id}}">
                                              <!-- like emotions container -->
                                              <span class="like-btn-like"></span>
                                                @if($post->user_react[0]->name != "like")
                <span class="like-btn-{{$post->user_react[0]->name}}"></span>
        @endif
        <!-- given emotions like, wow, sad (default:Like) -->
                                            </span>
        <span class="like-details" id="like-details-{{$post->id}}">You @if($post->likes->count-1 != 0) and {{$post->likes->count-1}} @if($post->likes->count-1 > 1000) k @endif others @endif</span>
    </div>
@endsection
