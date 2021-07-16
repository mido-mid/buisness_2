

@section('comment')
<div class="comment d-flex justify-content-between" id="comment-{{$comment->id}}">
    <div class="comment-owner d-flex p-2">
        @if($comment->publisher->personal_image)
            <div class="owner-img">
                <a style="display: inline" href="{{route('profile',$comment->publisher->id)}}"><img src="{{asset('media')}}/{{$comment->publisher->personal_image}}" class="rounded-circle" /></a>
            </div>
        @else
            <div class="owner-img">
                <a style="display: inline" href="{{route('profile',$comment->publisher->id)}}"><img src="{{asset('media')}}/img.jpg" class="rounded-circle" /></a>
            </div>
        @endif
        <div class="owner-name pl-3">
            <a href="comment.userProfile" class="mb-0"><b>{{$comment->publisher->name}}</b></a>
            <p class="comment-content mb-0">{{$comment->body}}</p>
            <!-- attatched img -->
            <!-- <img src="img.jpg" class="w-100 pt-3"> -->
            <!-- attatched vedio -->
            @if($comment->media != null)
                @if($comment->media->mediaType == 'image')
                    <img src="{{asset('media')}}/{{$comment->media->filename}}" class="w-100 pt-3">
                @else
                    <video class="pt-3" controls>
                        <source src="{{asset('media')}}/{{$comment->media->filename}}" type="video/mp4">
                    </video>
                @endif
            @endif
        </div>
    </div>
    <div class="comment-options comment-options-{{$comment->id}}">
        <ul class="options">
            <li>Report This Comment</li>
            <li>edit</li>
            <li>delete</li>
        </ul>
    </div>
    <div class="comment-option ml-auto pr-3 pt-2">
        <i class="fas fa-ellipsis-v" onclick="toggleCommentOptions({{$comment->id}})"></i>
    </div>
</div>
<hr class="m-0" />

@endsection
