
@section('reply')
    <div class="comment d-flex justify-content-between" id="reply-{{$reply->id}}">
        <div class="comment-owner d-flex p-2">
            @if($reply->publisher->personal_image)
                <div class="owner-img">
                    <a style="display: inline" href="{{route('profile',$reply->publisher->id)}}"><img src="{{asset('media')}}/{{$reply->publisher->personal_image}}" class="rounded-circle" /></a>
                </div>
            @else
                <div class="owner-img">
                    <a style="display: inline" href="{{route('profile',$reply->publisher->id)}}"><img src="{{asset('media')}}/img.jpg" class="rounded-circle" /></a>
                </div>
            @endif
            <div class="owner-name pl-3 w-100">
                <a href="comment.userProfile" class="mb-0"><b>{{$reply->publisher->name}}</b></a>
                <p class="comment-content mb-0"><?php echo $reply->body ?></p>
                <!-- attatched img -->
                <!-- <img src="img.jpg" class="w-100 pt-3"> -->
                <!-- attatched vedio -->
                @if($reply->media != null)
                    @if($reply->media->mediaType == 'image')
                        <img src="{{asset('media')}}/{{$reply->media->filename}}" style="height: 250px;width: auto" class="pt-3">
                    @else
                        <video class="pt-3" controls>
                            <source src="{{asset('media')}}/{{$reply->media->filename}}" type="video/mp4">
                        </video>
                    @endif
                @endif
                <ul class="comment-actions mt-1">
                @if($reply->liked)
                    <!-- if post is liked by user -->
                        <!-- <div><i class="fas fa-thumbs-up"></i><span> 20</span></div> -->
                        <!-- if post isn't liked by user -->
                        <div class="reaction-container" id="reaction-container-{{$reply->id}}">
                            <!-- container div for reaction system -->
                            <span class="reaction-btn">
                                                                                                        <!-- Default like button -->
                                                                                                        <span class="reaction-btn-emo like-btn-{{$reply->user_react[0]->name_en}}" id="reaction-btn-emo-{{$reply->id}}"></span>
                                <!-- Default like button emotion-->
                                                                                                        <span class="reaction-btn-text reaction-btn-text-{{$reply->user_react[0]->name_en}} active" onclick="unlikeModelSubmit({{$reply->id}},{{$reply->user_react[0]->id}})" id="reaction-btn-text-{{$reply->id}}">
                                                                                                            @if(App::getLocale() == 'ar')
                                                                                                                {{$reply->user_react[0]->name_ar}}
                                                                                                            @else
                                                                                                                {{$reply->user_react[0]->name_en}}
                                                                                                            @endif
                                                                                                            <form id="unlike-form-{{$reply->id}}-{{$reply->user_react[0]->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                                                                                @csrf
                                                                                                                <input type="hidden" name="model_id" value="{{$reply->id}}">
                                                                                                                <input type="hidden" name="model_type" value="comment">
                                                                                                                <input type="hidden" name="reactId" value="{{$reply->user_react[0]->id}}">
                                                                                                               <input type="hidden" name="requestType" id="like-request-type-{{$reply->id}}" value="delete">
                                                                                                            </form>
                                                                                                        </span>
                                <!-- Default like button text,(Like, wow, sad..) default:Like  -->
                                                                                                        <ul class="emojies-box">
                                                                                                            @foreach($reacts as $react)
                                                                                                                <!-- Reaction buttons container-->
                                                                                                                    <li class="emoji emo-{{$react->name_en}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$reply->id}},{{$react->id}})" data-reaction="{{$react->name_en}}"></li>
                                                                                                                    <form id="like-form-{{$reply->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                                                                                    @csrf
                                                                                                                    <input type="hidden" name="model_id" value="{{$reply->id}}">
                                                                                                                    <input type="hidden" name="model_type" value="comment">
                                                                                                                    <input type="hidden" name="reactId" value="{{$react->id}}">
                                                                                                                    <input type="hidden" name="requestType" id="like-request-type-{{$reply->id}}" value="update">
                                                                                                                </form>
                                                                                                                @endforeach
                                                                                                        </ul>
                                                                                                    </span>
                            <div class="like-stat">
                                <!-- Like statistic container-->
                                <span class="like-emo" id="like-emo-{{$reply->id}}">
                                                                                                          <!-- like emotions container -->
                                                                                                            <span class="like-btn-like"></span>
                                                                                                            @if($reply->user_react[0]->name_en != "like")
                                        <span class="like-btn-{{$reply->user_react[0]->name_en}}"></span>
                                @endif
                                <!-- given emotions like, wow, sad (default:Like) -->
                                                                                                        </span>
                                <span class="like-details" id="like-details-{{$reply->id}}" data-toggle="modal" data-target="#likes-modal-{{$reply->id}}">{{__('home.you')}} @if($reply->likes->count-1 != 0) {{__('home.and')}} {{$reply->likes->count-1}} @if($reply->likes->count-1 > 1000) {{__('home.thousnad')}} @endif {{__('home.others')}} @endif</span>
                            </div>
                            @if($reply->likes->count > 0)
                                <div class="likes-modal">
                                    <div class="modal fade" id="likes-modal-{{$reply->id}}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog" style="margin-top: 10vh;">
                                            <div class="modal-content">
                                                <div class="modal-header d-flex justify-content-between">
                                                    <span></span>
                                                    <h5 class="modal-title" id="exampleModalLabel">
                                                        Reactions
                                                    </h5>
                                                    <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="services-controller m-3 text-left">
                                                        <button onclick="filterPostLikes({{$reply->id}},'all-{{$reply->id}}')" class="btn btn-light active-{{$reply->id}} filter-all-{{$reply->id}} ez-active" id="{{$reply->id}}" data-filter="all-{{$reply->id}}">
                                                            {{__('home.all')}}
                                                        </button>
                                                        @foreach($reply->reacts_stat as $react_stat)
                                                            @if(count($react_stat) > 0)
                                                                <div class="btn btn-light active-{{$reply->id}} filter-{{$react_stat[0]->react_name}}-{{$reply->id}}" onclick='filterPostLikes({{$comment->id}},"{{$react_stat[0]->react_name}}-{{$reply->id}}")' id="{{$reply->id}}" data-filter="{{$react_stat[0]->react_name}}-{{$reply->id}}">
                                                                    <img src="{{asset('media')}}/{{$react_stat[0]->react_name}}.png"/>
                                                                    <span>{{count($react_stat)}}</span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <div class="likes-container mt-3">
                                                        @foreach($reply->reacts_stat as $react_stat)
                                                            @if(count($react_stat) > 0)
                                                                <div class="filter-{{$post->id}} {{$react_stat[0]->react_name}}-{{$reply->id}}">
                                                                    @foreach($react_stat as $react_emoji)
                                                                        <div class="people-info d-flex align-items-center">
                                                                            @if($react_emoji->publisher->personal_image != null)
                                                                                <img class="profile-figure rounded-circle"
                                                                                     src="{{asset('media')}}/{{$react_emoji->publisher->personal_image}}" />
                                                                            @else
                                                                                <img class="profile-figure rounded-circle"
                                                                                     src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                                            @endif
                                                                            <p class="mb-0 ml-3"><b>{{$react_emoji->publisher->name}}</b></p>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="reaction-container" id="reaction-container-{{$reply->id}}">
                            <!-- container div for reaction system -->
                            <span class="reaction-btn">
                                                                                                    <span class="reaction-btn-emo like-btn-default" id="reaction-btn-emo-{{$reply->id}}" style="display: none"></span>
                                <!-- Default like button emotion-->
                                                                                                    <span class="reaction-btn-text" id="reaction-btn-text-{{$reply->id}}">
                                                                                                        <div><i class="far fa-thumbs-up"></i>
                                                                                                            @if($reply->likes->count > 0)
                                                                                                                <span data-toggle="modal" data-target="#likes-modal-{{$reply->id}}">
                                                                                                                    {{$reply->likes->count}}
                                                                                                                </span>
                                                                                                            @endif
                                                                                                        </div>
                                                                                                    </span>
                                <!-- Default like button text,(Like, wow, sad..) default:Like  -->
                                                                                                    <ul class="emojies-box">
                                                                                                        @foreach($reacts as $react)
                                                                                                            <!-- Reaction buttons container-->
                                                                                                                <li class="emoji emo-{{$react->name_en}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$reply->id}},{{$react->id}})" data-reaction="{{$react->name_en}}"></li>
                                                                                                                <form id="like-form-{{$reply->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                                                                                @csrf
                                                                                                                <input type="hidden" name="model_id" value="{{$reply->id}}">
                                                                                                                <input type="hidden" name="model_type" value="comment">
                                                                                                                <input type="hidden" name="reactId" value="{{$react->id}}">
                                                                                                            </form>
                                                                                                            @endforeach
                                                                                                    </ul>
                                                                                                </span>
                            <div class="like-stat" id="like-stat-{{$reply->id}}" style="display: none">
                                <!-- Like statistic container-->
                                <span class="like-emo" id="like-emo-{{$reply->id}}">
                                                                                                          <!-- like emotions container -->
                                                                                                          <span class="like-btn-like"></span>
                                    <!-- given emotions like, wow, sad (default:Like) -->
                                                                                                        </span>
                                <span class="like-details" id="like-details-{{$reply->id}}" data-toggle="modal" data-target="#likes-modal-{{$reply->id}}">@if($reply->likes->count-1 > 0) {{__('home.and')}} {{$reply->likes->count-1}} @if($reply->likes->count-1 > 1000) {{__('home.thousand')}} @endif {{__('home.others')}} @endif</span>
                            </div>
                            @if($reply->likes->count > 0)
                                <div class="likes-modal">
                                    <div class="modal fade" id="likes-modal-{{$reply->id}}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog" style="margin-top: 10vh;">
                                            <div class="modal-content">
                                                <div class="modal-header d-flex justify-content-between">
                                                    <span></span>
                                                    <h5 class="modal-title" id="exampleModalLabel">
                                                        Reactions
                                                    </h5>
                                                    <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="services-controller m-3 text-left">
                                                        <button onclick="filterPostLikes({{$reply->id}},'all-{{$reply->id}}')" class="btn btn-light active-{{$reply->id}} filter-all-{{$reply->id}} ez-active" id="{{$reply->id}}" data-filter="all-{{$reply->id}}">
                                                            {{__('home.all')}}
                                                        </button>
                                                        @foreach($reply->reacts_stat as $react_stat)
                                                            @if(count($react_stat) > 0)
                                                                <div class="btn btn-light active-{{$reply->id}} filter-{{$react_stat[0]->react_name}}-{{$reply->id}}" onclick='filterPostLikes({{$comment->id}},"{{$react_stat[0]->react_name}}-{{$reply->id}}")' id="{{$reply->id}}" data-filter="{{$react_stat[0]->react_name}}-{{$reply->id}}">
                                                                    <img src="{{asset('media')}}/{{$react_stat[0]->react_name}}.png"/>
                                                                    <span>{{count($react_stat)}}</span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <div class="likes-container mt-3">
                                                        @foreach($reply->reacts_stat as $react_stat)
                                                            @if(count($react_stat) > 0)
                                                                <div class="filter-{{$post->id}} {{$react_stat[0]->react_name}}-{{$reply->id}}">
                                                                    @foreach($react_stat as $react_emoji)
                                                                        <div class="people-info d-flex align-items-center">
                                                                            @if($react_emoji->publisher->personal_image != null)
                                                                                <img class="profile-figure rounded-circle"
                                                                                     src="{{asset('media')}}/{{$react_emoji->publisher->personal_image}}" />
                                                                            @else
                                                                                <img class="profile-figure rounded-circle"
                                                                                     src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                                                            @endif
                                                                            <p class="mb-0 ml-3"><b>{{$react_emoji->publisher->name}}</b></p>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                    <li class="ml-3 text-primary" onclick="makeReply({{$comment->id}},'{{$reply->publisher->user_name}}')">{{__('home.reply')}}</li>
                </ul>
            </div>
        </div>
        <div class="comment-options comment-options-{{$reply->id}}">
            <ul class="options">
                <li data-toggle="modal" data-target="#report-comment-modal-{{$reply->id}}">
                    {{__('home.report')}}</li>
                @if($reply->publisher->id == auth()->user()->id)
                    <li data-toggle="modal" data-target="#edit-reply-modal-{{$reply->id}}">{{__('user.edit')}}</li>
                    <li onclick="confirm('{{ __("Are you sure you want to delete this reply ?") }}') ? deleteCommentSubmit({{$reply->id}},{{$post->id}}) : ''" >{{__('user.delete')}}</li>
                    <form action="{{ route('comments.destroy', $reply->id) }}" id="delete-comment-form-{{$reply->id}}" method="POST">
                    @csrf
                    @method('delete')
                    <!-- ajax-->
                    </form>
                @endif

                <div class="post-edit-modal">
                    <div class="modal fade" id="edit-reply-modal-{{$reply->id}}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin-top: 22vh">
                            <div class="modal-content">
                                <div class="modal-header d-flex justify-content-between">
                                    <span></span>
                                    <h5 class="modal-title" id="exampleModalLabel">{{__('user.edit')}}</h5>
                                    <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="col-12" id="error-message-{{$reply->id}}" style="display: none">
                                        <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$post->id}}" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    </div>
                                    <form action="{{route('comments.update',$reply->id)}}" id="edit-reply-form-{{$reply->id}}" method="POST" class="container" enctype="multipart/form-data">

                                    @csrf
                                    @method('put')

                                    <!-- Post Desc -->
                                        <div class="post-desc d-flex justify-content-center mt-2">
                                                                                                                  <textarea onfocus="mentionAdd('text-edit-{{$reply->id}}','menu-edit-reply-{{$reply->id}}')" id="text-edit-{{$reply->id}}" class="w-75" name="body" cols="200" rows="4"
                                                                                                                            placeholder="{{__('home.text_area')}}" >@if($reply->mentions != null){{$reply->edit}}@else{{$reply->body}}@endif</textarea>
                                            <div id="menu-edit-reply-{{$reply->id}}" class="menu" role="listbox"></div>
                                        </div>

                                        <input type="hidden" name="model_id" value="{{$post->id}}">

                                        <div class="post-desc d-flex justify-content-center mt-2">
                                            <input class="form-control w-75 mt-2" type="file" name="media" id="imgs"/>
                                        </div>
                                        <!-- Add Post Btn -->
                                        <div class="post-add-btn d-flex justify-content-center mt-4">
                                            <button type="button" onclick="editReplySubmit({{$reply->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                                {{__('user.save')}}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="post-edit-modal">
                    <div class="modal fade" id="report-comment-modal-{{$reply->id}}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin-top: 22vh">
                            <div class="modal-content">
                                <div class="modal-header d-flex justify-content-between">
                                    <span></span>
                                    <h5 class="modal-title" id="exampleModalLabel">{{__('home.report')}}</h5>
                                    <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="col-12" id="error-message-{{$reply->id}}" style="display: none">
                                        <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$reply->id}}" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    </div>
                                    <form action="{{route('userreports.store')}}" id="report-comment-form-{{$reply->id}}" method="POST" class="container" enctype="multipart/form-data">

                                    @csrf

                                    <!-- Post Desc -->
                                        <div class="post-desc d-flex justify-content-center mt-2">
                                                                                                                  <textarea class="w-75" name="body" id="post-text" cols="200" rows="4"
                                                                                                                            placeholder="{{__('home.text_area')}}" ></textarea>
                                        </div>
                                        <!-- Add Post Btn -->
                                        <input type="hidden" name="model_id" value="{{$reply->id}}">
                                        <input type="hidden" name="model_type" value="comment">

                                        <div class="post-add-btn d-flex justify-content-center mt-4">
                                            <button type="button" onclick="reportCommentSubmit({{$reply->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                                {{__('home.report')}}
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
            <i class="fas fa-ellipsis-v" onclick="toggleCommentOptions({{$reply->id}})"></i>
        </div>
    </div>
@endsection
