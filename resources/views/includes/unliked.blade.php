
@section('unliked')
    <span class="reaction-btn">
        <span class="reaction-btn-emo like-btn-default" id="reaction-btn-emo-{{$model->id}}" style="display: none"></span>
        <!-- Default like button emotion-->
        <span class="reaction-btn-text" id="reaction-btn-text-{{$model->id}}">
            <div><i class="far fa-thumbs-up"></i>
                @if($model->likes->count > 0)
                    <span data-toggle="modal" data-target="#likes-modal-{{$model->id}}">
                        {{$model->likes->count}}
                    </span>
                @endif
            </div>
        </span>
        <!-- Default like button text,(Like, wow, sad..) default:Like  -->
        <ul class="emojies-box">
            @foreach($reacts as $react)
                <!-- Reaction buttons container-->
                    <li class="emoji emo-{{$react->name}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$model->id}},{{$react->id}})" data-reaction="{{$react->name}}"></li>
                    <form id="like-form-{{$model->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                <input type="hidden" name="model_id" value="{{$model->id}}">
                <input type="hidden" name="model_type" value="{{$model_type}}">
                <input type="hidden" name="reactId" value="{{$react->id}}">
            </form>
                @endforeach
        </ul>
    </span>
    <div class="like-stat" id="like-stat-{{$model->id}}" style="display: none">
        <!-- Like statistic container-->
        <span class="like-emo" id="like-emo-{{$model->id}}">
                                              <!-- like emotions container -->
                                              <span class="like-btn-like"></span>
            <!-- given emotions like, wow, sad (default:Like) -->
                                            </span>
        <span class="like-details" id="like-details-{{$model->id}}" data-toggle="modal" data-target="#likes-modal-{{$model->id}}">@if($model->likes->count-1 > 0) and {{$model->likes->count-1}} @if($model->likes->count-1 > 1000) k @endif others @endif</span>
    </div>

    @if($model->likes->count > 0)
        <div class="likes-modal">
            <div class="modal fade" id="likes-modal-{{$model->id}}" tabindex="-1" aria-hidden="true">
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
                                <button onclick="filterPostLikes({{$model->id}},'all-{{$model->id}}')" class="btn btn-light active-{{$model->id}} filter-all-{{$model->id}} ez-active" id="{{$model->id}}" data-filter="all-{{$model->id}}">
                                    All
                                </button>
                                @if(count($model->like_stat) > 0)
                                    <div class="btn btn-light active-{{$model->id}} filter-like-{{$model->id}}" onclick="filterPostLikes({{$model->id}},'like-{{$model->id}}')" id="{{$model->id}}" data-filter="like-{{$model->id}}">
                                        <img src="{{asset('media')}}/like.png"/>
                                        <span>{{count($model->like_stat)}}</span>
                                    </div>
                                @endif
                                @if(count($model->love_stat) > 0)
                                    <div class="btn btn-light active-{{$model->id}} filter-love-{{$model->id}}" onclick="filterPostLikes({{$model->id}},'love-{{$model->id}}')" id="{{$model->id}}" data-filter="love-{{$model->id}}">
                                        <img src="{{asset('media')}}/love.png"/>
                                        <span>{{count($model->love_stat)}}</span>
                                    </div>
                                @endif
                                @if(count($model->haha_stat) > 0)
                                    <div class="btn btn-light active-{{$model->id}} filter-haha-{{$model->id}}" onclick="filterPostLikes({{$model->id}},'haha-{{$model->id}}')" id="{{$model->id}}" data-filter="haha-{{$model->id}}">
                                        <img src="{{asset('media')}}/haha.png"/>
                                        <span>{{count($model->haha_stat)}}</span>
                                    </div>
                                @endif
                                @if(count($model->sad_stat) > 0)
                                    <div class="btn btn-light active-{{$model->id}} filter-sad-{{$model->id}}" onclick="filterPostLikes({{$model->id}},'sad-{{$model->id}}')" id="{{$model->id}}" data-filter="sad-{{$model->id}}">
                                        <img src="{{asset('media')}}/sad.png"/>
                                        <span>{{count($model->sad_stat)}}</span>
                                    </div>
                                @endif
                                @if(count($model->angry_stat) > 0)
                                    <div class="btn btn-light active-{{$model->id}} filter-angry-{{$model->id}}" onclick="filterPostLikes({{$model->id}},'angry-{{$model->id}}')" id="{{$model->id}}" data-filter="angry-{{$model->id}}">
                                        <img src="{{asset('media')}}/angry.png"/>
                                        <span>{{count($model->angry_stat)}}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="likes-container mt-3">
                                <div class="filter-{{$model->id}} like-{{$model->id}}">
                                    @foreach($model->like_stat as $like_emoji)
                                        <div class="people-info d-flex align-items-center">
                                            @if($like_emoji->publisher->personal_image != null)
                                                <img class="profile-figure rounded-circle"
                                                     src="{{asset('media')}}/{{$like_emoji->publisher->personal_image}}" />
                                            @else
                                                <img class="profile-figure rounded-circle"
                                                     src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                            @endif
                                            <p class="mb-0 ml-3"><b>{{$like_emoji->publisher->name}}</b></p>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="filter-{{$model->id}} love-{{$model->id}}">
                                    @foreach($model->love_stat as $love)
                                        <div class="people-info d-flex align-items-center">
                                            @if($love->publisher->personal_image != null)
                                                <img class="profile-figure rounded-circle"
                                                     src="{{asset('media')}}/{{$love->publisher->personal_image}}" />
                                            @else
                                                <img class="profile-figure rounded-circle"
                                                     src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                            @endif
                                            <p class="mb-0 ml-3"><b>{{$love->publisher->name}}</b></p>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="filter-{{$model->id}} haha-{{$model->id}}">
                                    @foreach($model->haha_stat as $haha)
                                        <div class="people-info d-flex align-items-center">
                                            @if($haha->publisher->personal_image != null)
                                                <img class="profile-figure rounded-circle"
                                                     src="{{asset('media')}}/{{$haha->publisher->personal_image}}" />
                                            @else
                                                <img class="profile-figure rounded-circle"
                                                     src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                            @endif
                                            <p class="mb-0 ml-3"><b>{{$haha->publisher->name}}</b></p>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="filter-{{$model->id}} sad-{{$model->id}}">
                                    @foreach($model->sad_stat as $sad)
                                        <div class="people-info d-flex align-items-center">
                                            @if($sad->publisher->personal_image != null)
                                                <img class="profile-figure rounded-circle"
                                                     src="{{asset('media')}}/{{$sad->publisher->personal_image}}" />
                                            @else
                                                <img class="profile-figure rounded-circle"
                                                     src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                            @endif
                                            <p class="mb-0 ml-3"><b>{{$sad->publisher->name}}</b></p>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="filter-{{$model->id}} angry-{{$model->id}}">
                                    @foreach($model->angry_stat as $angry)
                                        <div class="people-info d-flex align-items-center">
                                            @if($angry->publisher->personal_image != null)
                                                <img class="profile-figure rounded-circle"
                                                     src="{{asset('media')}}/{{$angry->publisher->personal_image}}" />
                                            @else
                                                <img class="profile-figure rounded-circle"
                                                     src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                                            @endif
                                            <p class="mb-0 ml-3"><b>{{$angry->publisher->name}}</b></p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
