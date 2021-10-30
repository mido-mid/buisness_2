

@section('home_stories')
@foreach($stories as $story)
    <div onclick="addStoryViews({{$story->publisher->id}})" class="story" data-toggle="modal" data-target="#show-story-modal-{{$story->publisher->id}}" id="story-{{$story->publisher->id}}">
        <div class="owner-info d-flex align-items-center">
            @if($story->publisher->personal_image != null)
                <img
                    src="{{asset('media')}}/{{$story->publisher->personal_image}}" class="profile-figure rounded-circle" />
            @else
                <img
                    src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" class="profile-figure rounded-circle"/>
            @endif
            <p class="mb-0 p-2"><b>{{$story->publisher->name}}</b></p>
        </div>
        @if($story->publisher->personal_image != null)
            <img
                src="{{asset('media')}}/{{$story->publisher->personal_image}}"/>
        @else
            <img
                src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
            />
        @endif
    </div>
    <div class="show-story-modal">
        <div class="modal fade load-modal" data-controls-modal="show-story-modal-{{$story->publisher->id}}" data-backdrop="static" data-keyboard="false" id="show-story-modal-{{$story->publisher->id}}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="story-carousel-{{$story->publisher->id}}" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($story as $inner_story)
                                    <div class="carousel-item @if ($loop->first == true) active @endif carousel-{{$story->publisher->id}}" @if($inner_story->media != null && $inner_story->media->mediaType == "video" ) data-type="video" @else data-type="not video"  @endif id="carousel-item-{{$inner_story->id}}">
                                        <div class="controllers d-flex justify-content-between">
                                            @if($story->publisher->id == auth()->user()->id)
                                                <i class=" fas fa-eye p-2" data-toggle="modal" data-target="#show-story-views-modal-{{$inner_story->id}}"> {{count($inner_story->viewers)}} </i>
                                            @endif
                                            <div>
                                                @if($story->publisher->id == auth()->user()->id)
                                                    <i class="fas fa-trash p-2" onclick="confirm('{{ __("home.confirm") }}') ? deleteStorySubmit({{$inner_story->id}},{{$story->publisher->id}}) : ''"></i>
                                                    <form action="{{ route('stories.destroy', $inner_story->id) }}" id="delete-story-form-{{$inner_story->id}}" method="post" style="display: none">
                                                    @csrf
                                                    @method('delete')
                                                    <!-- ajax-->
                                                    </form>
                                                @endif
                                                <i class="fas fa-times p-2" data-dismiss="modal" aria-label="Close" onclick="removeCurrent({{$story->publisher->id}})"></i>
                                            </div>
                                        </div>
                                    @if($inner_story->body != null && is_null($inner_story->media) )
                                        <!-- If Content Text -->
                                            <p class="m-auto text-center w-100 p-5 h2 h-100">{{$inner_story->body}}</p>
                                    @else
                                        @if($inner_story->media->mediaType == 'image')
                                            <!-- If Content Img -->
                                                <img class="w-100"
                                                     src="{{asset('media')}}/{{$inner_story->media->filename}}" />
                                                @if($inner_story->body != null)
                                                    <div class="carousel-caption d-none d-md-block">
                                                        <p>{{$inner_story->body}}</p>
                                                    </div>
                                                @endif
                                            @else
                                            <!-- If Content Vedio -->
                                                <div class="story-content-vedio">
                                                    <video class="w-100 h-100" id="story-video-{{$inner_story->id}}">
                                                        <source src="{{asset('media')}}/{{$inner_story->media->filename}}" type="video/mp4" />
                                                        {{__('home.no_browser_support')}}
                                                    </video>
                                                    @if($inner_story->body != null)
                                                        <div class="carousel-caption d-none d-md-block">
                                                            <p>{{$inner_story->body}}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                    <form id="view-story-form-{{$inner_story->id}}" action="{{ route('story.view') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="story_id" value="{{$inner_story->id}}">
                                    </form>

                                    <div class="show-story-views-modal">
                                        <div class="modal fade" id="show-story-views-modal-{{$inner_story->id}}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog" style="margin-top: -10px">
                                                <div class="modal-content">
                                                    <div class="modal-header d-flex justify-content-between">
                                                        <h5 class="modal-title" id="exampleModalLabel">
                                                            {{__('home.story_viewers')}}
                                                        </h5>
                                                        <button type="button" class="close ml-0" onclick="$('#show-story-views-modal-{{$inner_story->id}}').modal('hide');" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @if(count($inner_story->viewers) > 0)
                                                            @foreach($inner_story->viewers as $viewer)
                                                                <div class="people-info d-flex align-items-center">
                                                                    @if($viewer->personal_image != null)
                                                                        <img class="profile-figure rounded-circle"
                                                                             src="{{asset('media')}}/{{$viewer->personal_image}}"
                                                                             alt="User Profile Pic">
                                                                    @else
                                                                        <img class="profile-figure rounded-circle"
                                                                             src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                                                             alt="User Profile Pic">
                                                                    @endif
                                                                    <p class="mb-0 ml-3"><b>{{$viewer->name}}</b></p>
                                                                </div>
                                                                @if($loop->last == false)
                                                                    <hr>
                                                                @endif
                                                            @endforeach

                                                        @else
                                                            <p class="mb-0 ml-3"><b>{{__('home.no_viewers')}}</b></p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <a class="carousel-control-prev" href="#story-carousel-{{$story->publisher->id}}" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#story-carousel-{{$story->publisher->id}}" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach


@endsection
