
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
            <li data-toggle="modal" data-target="#report-comment-modal-{{$comment->id}}">
                Report this comment</li>
            <li data-toggle="modal" data-target="#edit-comment-modal-{{$comment->id}}">Edit</li>
            <li onclick="confirm('{{ __("Are you sure you want to delete this comment ?") }}') ? deleteCommentSubmit({{$comment->id}}) : ''" >Delete</li>
            <form action="{{ route('comments.destroy', $comment->id) }}" id="delete-comment-form-{{$comment->id}}" method="POST">
            @csrf
            @method('delete')
            <!-- ajax-->
            </form>

            <div class="post-edit-modal">
                <div class="modal fade" id="edit-comment-modal-{{$comment->id}}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog" style="margin-top: 22vh">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between">
                                <span></span>
                                <h5 class="modal-title" id="exampleModalLabel">Edit Comment</h5>
                                <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="col-12" id="error-message-{{$post->id}}" style="display: none">
                                    <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$post->id}}" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                                <form action="{{route('comments.update',$comment->id)}}" id="edit-comment-form-{{$comment->id}}" method="POST" class="container" enctype="multipart/form-data">

                                @csrf
                                @method('put')

                                <!-- Post Desc -->
                                    <div class="post-desc d-flex justify-content-center mt-2">
                                                                          <textarea class="w-75" name="body" id="post-text" cols="200" rows="4"
                                                                                    placeholder="Start Typing..." >{{$comment->body}}</textarea>
                                    </div>

                                    <div class="post-desc d-flex justify-content-center mt-2">
                                        <input class="form-control w-75 mt-2" type="file" name="media[]" id="imgs"/>
                                    </div>
                                    <!-- Add Post Btn -->
                                    <div class="post-add-btn d-flex justify-content-center mt-4">
                                        <button type="button" onclick="editCommentSubmit({{$comment->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="post-edit-modal">
                <div class="modal fade" id="report-comment-modal-{{$comment->id}}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog" style="margin-top: 22vh">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between">
                                <span></span>
                                <h5 class="modal-title" id="exampleModalLabel">Report Comment</h5>
                                <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="col-12" id="error-message-{{$post->id}}" style="display: none">
                                    <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$post->id}}" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                                <form action="{{route('reports.store')}}" id="report-comment-form-{{$comment->id}}" method="POST" class="container" enctype="multipart/form-data">

                                @csrf

                                <!-- Post Desc -->
                                    <div class="post-desc d-flex justify-content-center mt-2">
                                                                          <textarea class="w-75" name="body" id="post-text" cols="200" rows="4"
                                                                                    placeholder="Start Typing..." ></textarea>
                                    </div>
                                    <!-- Add Post Btn -->
                                    <input type="hidden" name="model_id" value="{{$comment->id}}">
                                    <input type="hidden" name="model_type" value="comment">

                                    <div class="post-add-btn d-flex justify-content-center mt-4">
                                        <button type="button" onclick="reportCommentSubmit({{$comment->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                            Report
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </ul>
    </div>
    <div class="comment-option ml-auto pr-3 pt-2">
        <i class="fas fa-ellipsis-v" onclick="toggleCommentOptions({{$comment->id}})"></i>
    </div>
</div>
<hr class="m-0" />

@endsection
