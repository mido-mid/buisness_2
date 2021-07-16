

@section('post')
<div class="post-container bg-white mt-3 p-3" id="post-{{$post->id}}">
    <div class="post-owner d-flex align-items-center">
        @if($post->publisher->personal_image)
            <div class="owner-img">
                <a style="display: inline" href="{{route('profile',$post->publisher->id)}}"><img src="{{asset('media')}}/{{$post->publisher->personal_image}}" class="rounded-circle" /></a>
            </div>
        @else
            <div class="owner-img">
                <a style="display: inline" href="{{route('profile',$post->publisher->id)}}"><img src="{{asset('media')}}/img.jpg" class="rounded-circle" /></a>
            </div>
        @endif
        <div class="owner-name pl-3">
            <a href="{{route('profile',$post->publisher->id)}}"><b>{{$post->publisher->name}}</b></a><br>
            <span>{{date('d/m/Y',strtotime($post->created_at))}}</span>
        </div>
        <!-- Post options -->
        <div class="post-options post-options-{{$post->id}}">
            <ul class="options">
                <li data-toggle="modal" data-target="#advertiseModal">Advertise</li>
            @if(!$post->saved)
                <!-- ajax -->
                    <li><a id="save-post-{{$post->id}}" onclick="savePostSubmit({{$post->id}})">Save Post</a></li>
                    <form id="save-post-form-{{$post->id}}" action="{{ route('savepost') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                        @csrf
                        <input type="hidden" name="post_id" value="{{$post->id}}">
                        <input type="hidden" id="save-post-flag-{{$post->id}}" name="flag" value="0">
                    </form>
                @else
                    <li><a id="save-post-{{$post->id}}" onclick="savePostSubmit({{$post->id}})">Saved</a></li>
                    <form id="save-post-form-{{$post->id}}" action="{{ route('savepost') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                        @csrf
                        <input type="hidden" name="post_id" value="{{$post->id}}">
                        <input type="hidden" id="save-post-flag-{{$post->id}}" name="flag" value="1">
                    </form>
                @endif
                <form action="{{ route('posts.destroy', $post->id) }}" id="delete-post-form-{{$post->id}}" method="post">
                @csrf
                @method('delete')
                <!-- ajax-->
                    <li data-toggle="modal" data-target="#edit-post-modal-{{$post->id}}">Edit</li>
                    <li onclick="confirm('{{ __("Are you sure you want to delete this post ?") }}') ? deletePostSubmit({{$post->id}}) : ''" class="last-li">
                        Delete</li>
                </form>
            </ul>
        </div>
        <div class="post-option ml-auto pr-3" onclick="toggleOptions({{$post->id}})">
            <i class="fas fa-ellipsis-v"></i>
        </div>
    </div>
    <div class="post-desc mt-3">
        <!-- if lang arabic -->
        @if(App::getlocale() == 'ar')
            <pre style="text-align:right;">{{$post->body}}</pre>
        @else
            <pre style="text-align:left;">{{$post->body}}</pre>
        @endif
        @if($post->media)
            <div class="media">
            @foreach($post->media as $media)
                @if($media->mediaType == 'image')
                    <!-- if media img and imgs=1 -->
                        <div class="d-flex" style="width: 100%">
                            <img src="{{asset('media')}}/{{$media->filename}}" alt="opel car" />
                        </div>
                    @else
                        <video class="p-1" controls>
                            <source src="{{asset('media')}}/{{$media->filename}}" type="video/mp4">
                            Your browser does not support HTML video.
                        </video>
                    @endif

                <!-- if media img and imgs=2 -->
                    <!-- <div class="d-flex">
                      <img src="img.jpg" alt="opel car" class="w-50 p-1" />
                      <img src="img.jpg" alt="opel car" class="w-50 p-1" />
                    </div> -->
                    <!-- if media img and imgs=3 -->
                    <!-- <div>
                      <img class="d-block w-100" src="img.jpg" alt="opel car" />
                      <div class="d-flex">
                        <img src="img.jpg" alt="opel car" class="w-50 pr-1 pt-2" />
                        <img src="img.jpg" alt="opel car" class="w-50 pl-1 pt-2" />
                      </div>
                    </div> -->
                    <!-- if media img and imgs=4 -->
                    <!-- <div>
                      <div class="d-flex">
                        <img src="img.jpg" alt="opel car" class="w-50 pr-1" />
                        <img src="img.jpg" alt="opel car" class="w-50 pl-1" />
                      </div>
                      <div class="d-flex">
                        <img src="img.jpg" alt="opel car" class="w-50 pr-1 pt-2" />
                        <img src="img.jpg" alt="opel car" class="w-50 pl-1 pt-2" />
                      </div>
                    </div> -->
                    <!-- if media img and imgs>4 -->
                    <!-- <div>
                      <div class="d-flex">
                        <img src="img.jpg" alt="opel car" class="w-50 pr-1" />
                        <img src="img.jpg" alt="opel car" class="w-50 pl-1" />
                      </div>
                      <div class="d-flex">
                        <img src="img.jpg" alt="opel car" class="w-50 pr-1 pt-2" />
                        <img src="img.jpg" alt="opel car" class="w-50 pl-1 pt-2" />
                      </div>
                    </div> -->
                    <!-- if media img and vedio=1 -->
                    <!-- <div class="w-100">
                      <video controls>
                        <source src="VID.mp4" type="video/mp4">
                        Your browser does not support HTML video.
                      </video>
                    </div> -->
                    <!-- if media img and vedio=2 -->
                    <!-- <div class="d-flex w-100">
                      <video class="p-1" controls>
                        <source src="VID.mp4" type="video/mp4">
                        Your browser does not support HTML video.
                      </video>
                      <video class="p-1" controls>
                        <source src="VID.mp4" type="video/mp4">
                        Your browser does not support HTML video.
                      </video>
                    </div> -->
                    <!-- if media img and vedio=3 -->
                    <!-- <div class="w-100">
                      <video class="p-1" controls>
                        <source src="VID.mp4" type="video/mp4">
                        Your browser does not support HTML video.
                      </video>
                      <div class="d-flex">
                        <video class="p-1" controls>
                          <source src="VID.mp4" type="video/mp4">
                          Your browser does not support HTML video.
                        </video>
                        <video class="p-1" controls>
                          <source src="VID.mp4" type="video/mp4">
                          Your browser does not support HTML video.
                        </video>
                      </div>
                    </div> -->
                    <!-- if media img and vedio=4 -->
                    <!-- <div class="w-100">
                      <div class="d-flex w-100">
                        <div class="d-flex w-100">
                          <video class="p-1" controls>
                            <source src="VID.mp4" type="video/mp4">
                            Your browser does not support HTML video.
                          </video>
                          <video class="p-1" controls>
                            <source src="VID.mp4" type="video/mp4">
                            Your browser does not support HTML video.
                          </video>
                        </div>
                      </div>
                      <div class="d-flex w-100">
                        <div class="d-flex w-100">
                          <video class="p-1" controls>
                            <source src="VID.mp4" type="video/mp4">
                            Your browser does not support HTML video.
                          </video>
                          <video class="p-1" controls>
                            <source src="VID.mp4" type="video/mp4">
                            Your browser does not support HTML video.
                          </video>
                        </div>
                      </div>
                    </div> -->
                @endforeach
            </div>
        @endif
        <div class="post-statistics mt-3 d-flex">
            <div class="likes">

            @if($post->liked)
                <!-- if post is liked by user -->
                    <!-- <div><i class="fas fa-thumbs-up"></i><span> 20</span></div> -->
                    <!-- if post isn't liked by user -->
                    <div class="reaction-container" id="reaction-container-{{$post->id}}">
                        <!-- container div for reaction system -->
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
                    </div>
                @else

                    <div class="reaction-container" id="reaction-container-{{$post->id}}">
                        <!-- container div for reaction system -->
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
                    </div>
                @endif
            </div>
            <div class="comments" onclick="toggleComments(1)"><i class="far fa-comment ml-3"></i>
                @if($post->comments->count > 0)
                    <span>
                                    {{$post->comments->count}}
                                </span>
                @endif
            </div>
            <div class="modal" id="share-post-modal-{{$post->id}}" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" style="margin-top: 22vh">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <span></span>
                            <h5 class="modal-title" id="exampleModalLabel">share Post</h5>
                            <button type="button" id="add-post-modal-dismiss" class="close ml-0" data-dismiss="modal" aria-label="Close">
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
                            <form action="{{route('posts.store')}}" method="POST" class="container" id="share-post-form-{{$post->id}}" enctype="multipart/form-data">

                            @csrf
                            <!-- Select Post Type -->
                            {{--                                <div class="post-type d-flex justify-content-between align-items-center m-auto w-75">--}}
                            {{--                                    <div>Post As:</div>--}}
                            {{--                                    <div class="d-flex align-items-center">--}}
                            {{--                                        <input type="radio" name="post-type" value="post" id="post-type-post" checked />--}}
                            {{--                                        <span class="pl-2">Post</span>--}}
                            {{--                                    </div>--}}
                            {{--                                    <div class="d-flex align-items-center">--}}
                            {{--                                        <input class="m-0" type="radio" name="post-type" value="service" id="post-type-service" />--}}
                            {{--                                        <span class="pl-2">Service</span>--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            <!-- Select post Privacy -->
                                <input type="hidden" name="post_id" value="{{$post->id}}">
                                <div class="post-privacy d-flex justify-content-between align-items-center m-auto w-75">
                                    <label for="cars">Choose Post Privacy:</label>
                                    <select id="post-privacy" name="privacy_id">
                                        @foreach($privacy as $postprivacy)
                                            @if(App::getlocale() == 'en')
                                                <option value="{{$postprivacy->id}}">{{$postprivacy->name_en}}</option>
                                            @else
                                                <option value="{{$postprivacy->id}}">{{$postprivacy->name_ar}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                    <label for="cars">Choose A Category:</label>
                                    <select id="post-category" name="category_id">
                                        @foreach($categories as $category)
                                            @if(App::getlocale() == 'en')
                                                <option value="{{$category->id}}" @if($category->id == $post->categoryId) selected @endif>{{$category->name_en}}</option>
                                            @else
                                                <option value="{{$category->id}}" @if($category->id == $post->categoryId) selected @endif>{{$category->name_ar}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            {{--                                <div id="post-type-service-content" class="d-none">--}}
                            {{--                                    <!-- Select Service Category -->--}}
                            {{--                                    <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">--}}
                            {{--                                        <label for="cars">Choose A Category:</label>--}}
                            {{--                                        <select id="post-category" name="categoryId">--}}
                            {{--                                            @foreach($categories as $category)--}}
                            {{--                                                @if(App::getlocale() == 'en')--}}
                            {{--                                                    <option value="{{$category->id}}">{{$category->name_en}}</option>--}}
                            {{--                                                @else--}}
                            {{--                                                    <option value="{{$category->id}}">{{$category->name_ar}}</option>--}}
                            {{--                                                @endif--}}
                            {{--                                            @endforeach--}}
                            {{--                                        </select>--}}
                            {{--                                    </div>--}}
                            {{--                                    <!-- Select Service Price -->--}}
                            {{--                                    <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">--}}
                            {{--                                        <input class="w-100 border" type="number" placeholder="Service Price $" />--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            <!-- Post Desc -->
                                <div class="post-desc d-flex justify-content-center mt-2">
                                                    <textarea class="w-75 p-2" name="body" id="post-text" cols="200" rows="4"
                                                              placeholder="Post Description..."></textarea>
                                </div>
                                <!-- Add Post Btn -->
                                <div class="post-add-btn d-flex justify-content-center mt-4">
                                    <button type="button" onclick="sharePostSubmit({{$post->id}})" class="btn btn-warning btn-block w-75">
                                        share
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="shares" data-toggle="modal" data-target="#share-post-modal-{{$post->id}}">
                <i class="fas fa-share ml-3"></i>
                @if($post->shares > 0)
                    <span>
                                {{$post->shares}}
                            </span>
                @endif
            </div>
        </div>
        <div class="post-comment-list post-comment-list-1 mt-2">
            <div class="hide-commnet-list d-flex flex-row-reverse">
                <span onclick="toggleComments(1)"><i class="fas fa-chevron-up"></i> Hide</span>
            </div>
            @if($post->comments)
                @foreach($post->comments as $comment)
                    <div class="comment d-flex justify-content-between">
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
                            </ul>
                        </div>
                        <div class="comment-option ml-auto pr-3 pt-2">
                            <i class="fas fa-ellipsis-v" onclick="toggleCommentOptions({{$comment->id}})"></i>
                        </div>
                    </div>
                    @if(count($post->comments) > 1)
                        <hr class="m-0" />
                    @endif
                <!-- if there is multi comments then take uncomment hr -->
                    {{--                        <hr class="m-0" /> --}}
                @endforeach
            @endif
        </div>
        <button type="button" id="comment-submit-btn-{{$post->id}}" onclick="addCommentSubmit({{$post->id}})" hidden></button>
        <form class="add-commnet mt-2 d-flex align-items-center" onkeypress="commentSubmitClick({{$post->id}})" id="add-comment-form-{{$post->id}}" action="{{route('comments.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="post_id" value="{{$post->id}}" />
            <input class="w-100 pl-2" type="text" name="body" placeholder="Add Your Commnet" />
            <div class="d-flex align-items-center pr-3">
                <i class="fas fa-paperclip" onclick="commentAttachClick({{$post->id}})"></i>
                <input type="file" id="comment-attach-{{$post->id}}" name="img" accept="image/*" />
            </div>
        </form>
        <div class="post-advertise-modal">
            <div class="modal fade" id="advertiseModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog" style="margin-top: 10vh">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <span></span>
                            <h5 class="modal-title" id="exampleModalLabel">
                                Advertise Post
                            </h5>
                            <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body pl-5 pr-5">
                            <form action="{{route('sponsor')}}" class="container">
                                <p>Select Duration:</p>
                                @foreach($times as $time)
                                    <div class="form-group form-check mb-1">
                                        <input type="radio" name="timeId" class="form-check-input" id="exampleCheck1" />
                                        <label class="form-check-label" for="exampleCheck1">3 days</label>
                                    </div>
                                    <hr class="m-1">
                                @endforeach

                                <p>Select Audience:</p>
                                @foreach($reaches as $reach)
                                    <div class="form-group form-check mb-1">
                                        <input type="radio" name="reachId" class="form-check-input" id="exampleCheck1" />
                                        <label class="form-check-label" for="exampleCheck1">From 100 To 1000</label>
                                    </div>
                                    <hr class="m-1">
                                @endforeach
                                <div class="form-group d-flex justify-content-between">
                                    <label for="exampleInputEmail1">Target Audience:</label>
                                    <select name="gender">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                @foreach($ages as $age)
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="exampleInputEmail1">Target Age:</label>
                                        <select name="age_id">
                                            <option value="male">From 20 To 30</option>
                                            <option value="female">From 30 To 40</option>
                                        </select>
                                    </div>
                                @endforeach
                                <div class="form-group d-flex justify-content-between">
                                    <label for="exampleInputEmail1">Target Countery:</label>
                                    <select name="country_id">
                                        <option value="male">Egypt</option>
                                    </select>
                                </div>
                                <input type="hidden" name="postId" value="{{$post->id}}">
                                <button type="submit" class="btn btn-warning btn-block">
                                    Sponsor
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="post-edit-modal">
            <div class="modal fade" id="edit-post-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog" style="margin-top: 22vh">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <span></span>
                            <h5 class="modal-title" id="exampleModalLabel">Edit Post</h5>
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
                            <form action="{{route('posts.update',$post->id)}}" id="edit-post-form-{{$post->id}}" method="POST" class="container" enctype="multipart/form-data">

                            @csrf
                            @method('put')

                            <!-- Select post Privacy -->
                                <div class="post-privacy d-flex justify-content-between align-items-center m-auto w-75">
                                    <label for="cars">Choose Post Privacy:</label>
                                    <select id="post-privacy" name="privacy_id">
                                        @foreach($privacy as $postprivacy)
                                            @if(App::getlocale() == 'en')
                                                <option value="{{$postprivacy->id}}" @if($postprivacy->id == $post->privacyId) selected @endif>{{$postprivacy->name_en}}</option>
                                            @else
                                                <option value="{{$postprivacy->id}}" @if($postprivacy->id == $post->privacyId) selected @endif>{{$postprivacy->name_ar}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div id="post-type-service-content">
                                    <!-- Select Service Category -->
                                    <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                        <label for="cars">Choose A Category:</label>
                                        <select id="post-category" name="category_id">
                                            @foreach($categories as $category)
                                                @if(App::getlocale() == 'en')
                                                    <option value="{{$category->id}}" @if($category->id == $post->categoryId) selected @endif>{{$category->name_en}}</option>
                                                @else
                                                    <option value="{{$category->id}}" @if($category->id == $post->categoryId) selected @endif>{{$category->name_ar}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Select Service Price -->
                                    {{--                                                <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">--}}
                                    {{--                                                    <input class="w-100 border" type="number" placeholder="Service Price $" />--}}
                                    {{--                                                </div>--}}
                                </div>
                                <!-- Post Desc -->
                                <div class="post-desc d-flex justify-content-center mt-2">
                                              <textarea class="w-75" name="body" id="post-text" cols="200" rows="4"
                                                        placeholder="Start Typing..." >{{$post->body}}</textarea>
                                </div>
                                <!-- Post Images -->
                                <div class="post-desc d-flex justify-content-center mt-2">
                                    <input class="form-control w-75 mt-2" type="file" name="media[]" id="imgs" accept="image/*"
                                           multiple />
                                </div>
                                <!-- Add Post Btn -->
                                <div class="post-add-btn d-flex justify-content-center mt-4">
                                    <button type="button" onclick="editPostSubmit({{$post->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="post-delete-modal">
            <div class="modal fade" id="delete-post-modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog" style="margin-top: 22vh">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <h5 class="modal-title" id="exampleModalLabel">Confirm Delete Post</h5>
                            <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <button type="button" onclick="deletePost(1)" class="btn btn-warning btn-block w-100"
                                    data-dismiss="modal">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
