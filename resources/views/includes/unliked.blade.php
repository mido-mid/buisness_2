
@section('unliked')
    <span class="reaction-btn">
        <span class="reaction-btn-emo like-btn-default" id="reaction-btn-emo-{{$post->id}}" style="display: none"></span>
        <!-- Default like button emotion-->
        <span class="reaction-btn-text" id="reaction-btn-text-{{$post->id}}">
            <div><i class="far fa-thumbs-up"></i>
                @if($post->likes->count > 0)
                    <span>
                        {{$post->likes->count}}
                    </span>
                @endif
            </div>
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
            </form>
                @endforeach
        </ul>
    </span>
    <div class="like-stat" id="like-stat-{{$post->id}}" style="display: none">
        <!-- Like statistic container-->
        <span class="like-emo" id="like-emo-{{$post->id}}">
                                              <!-- like emotions container -->
                                              <span class="like-btn-like"></span>
            <!-- given emotions like, wow, sad (default:Like) -->
                                            </span>
        <span class="like-details" id="like-details-{{$post->id}}">@if($post->likes->count-1 > 0) and {{$post->likes->count-1}} @if($post->likes->count-1 > 1000) k @endif others @endif</span>
    </div>
@endsection
