
@section('liked')
    <span class="reaction-btn">
        <!-- Default like button -->
        <span class="reaction-btn-emo like-btn-{{$model->user_react[0]->name_en}}" id="reaction-btn-emo-{{$model->id}}"></span>
        <!-- Default like button emotion-->
        <span class="reaction-btn-text reaction-btn-text-{{$model->user_react[0]->name_en}} active" onclick="unlikeModelSubmit({{$model->id}},{{$model->user_react[0]->id}})" id="reaction-btn-text-{{$model->id}}">
            @if(App::getLocale() == 'ar')
                {{$model->user_react[0]->name_ar}}
            @else
                {{$model->user_react[0]->name_en}}
            @endif
            <form id="unlike-form-{{$model->id}}-{{$model->user_react[0]->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                <input type="hidden" name="model_id" value="{{$model->id}}">
                <input type="hidden" name="model_type" value="{{$model_type}}">
                <input type="hidden" name="reactId" value="{{$model->user_react[0]->id}}">
               <input type="hidden" name="requestType" id="like-request-type-{{$model->id}}" value="delete">
            </form>
        </span>
        <!-- Default like button text,(Like, wow, sad..) default:Like  -->
        <ul class="emojies-box">
            @foreach($reacts as $react)
            <!-- Reaction buttons container-->
                <li class="emoji emo-{{$react->name_en}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$model->id}},{{$react->id}})" data-reaction="{{$react->name_en}}"></li>
                <form id="like-form-{{$model->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                    @csrf
                    <input type="hidden" name="model_id" value="{{$model->id}}">
                    <input type="hidden" name="model_type" value="{{$model_type}}">
                    <input type="hidden" name="reactId" value="{{$react->id}}">
                    <input type="hidden" name="requestType" id="like-request-type-{{$model->id}}" value="update">
                </form>
            @endforeach
        </ul>
    </span>
    <div class="like-stat">
        <!-- Like statistic container-->
        <span class="like-emo" id="like-emo-{{$model->id}}">
                                                                          <!-- like emotions container -->
                                                                          <span class="like-btn-like"></span>
                                                                            @if($model->user_react[0]->name_en != "like")
                <span class="like-btn-{{$model->user_react[0]->name_en}}"></span>
        @endif
        <!-- given emotions like, wow, sad (default:Like) -->
                                                                        </span>
        <span class="like-details" id="like-details-{{$model->id}}" data-toggle="modal" data-target="#likes-modal-{{$model->id}}">{{__('home.you')}} @if($model->likes->count-1 != 0) {{__('home.and')}} {{$model->likes->count-1}} @if($model->likes->count-1 > 1000) {{__('home.thousand')}} @endif {{__('home.others')}} @endif</span>
    </div>
    @if($model->likes->count > 0)
        <div class="likes-modal">
            <div class="modal fade" id="likes-modal-{{$model->id}}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog" style="margin-top: 10vh;">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <span></span>
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{__('home.reacts')}}
                            </h5>
                            <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="services-controller m-3 text-left">
                                <button onclick="filterPostLikes({{$model->id}},'all-{{$model->id}}')" class="btn btn-light active-{{$model->id}} filter-all-{{$model->id}} ez-active" id="{{$model->id}}" data-filter="all-{{$model->id}}">
                                    {{__('home.all')}}
                                </button>
                                @foreach($model->reacts_stat as $react_stat)
                                    @if(count($react_stat) > 0)
                                        <div class="btn btn-light active-{{$model->id}} filter-{{$react_stat[0]->react_name}}-{{$model->id}}" onclick='filterPostLikes({{$model->id}},"{{$react_stat[0]->react_name}}-{{$model->id}}")' id="{{$model->id}}" data-filter="{{$react_stat[0]->react_name}}-{{$model->id}}">
                                            <img src="{{asset('media')}}/{{$react_stat[0]->react_name}}.png"/>
                                            <span>{{count($react_stat)}}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="likes-container mt-3">
                                @foreach($model->reacts_stat as $react_stat)
                                    @if(count($react_stat) > 0)
                                        <div class="filter-{{$model->id}} {{$react_stat[0]->react_name}}-{{$model->id}}">
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
@endsection
