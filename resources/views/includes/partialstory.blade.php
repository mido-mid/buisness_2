

@section('story')
    <div onclick="addStoryViews({{$story->id}})" class="story" data-toggle="modal" data-target="#show-story-modal-{{$story->id}}" id="story-{{$story->id}}">
        @if($story->cover_image != null)
            <img
                src="{{asset('media')}}/{{$story->cover_image}}" />
        @else
            <img
                src="{{asset('media')}}/story_cover_image.jpg" />
        @endif

        <form id="view-story-form-{{$story->id}}" action="{{ route('story.view') }}" method="POST" enctype="multipart/form-data" style="display: none;">
            @csrf
            <input type="hidden" name="story_id" value="{{$story->id}}">
        </form>

    </div>
    <div class="show-story-modal">
        <div class="modal fade" id="show-story-modal-{{$story->id}}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between">
                        <span></span>
                        @if($story->publisher->id == auth()->user()->id)
                            <button onclick="confirm('{{ __("Are you sure you want to delete this story ?") }}') ? deleteStorySubmit({{$story->id}}) : ''">
                                Delete</button>
                            <form action="{{ route('stories.destroy', $story->id) }}" id="delete-story-form-{{$story->id}}" method="post">
                            @csrf
                            @method('delete')
                            <!-- ajax-->
                            </form>
                        @endif
                    </div>
                    <div class="modal-body">
                        <!-- If Content Img -->
                        <!-- <div class="story-content-img">
                          <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                          <img class="w-100"
                            src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" />
                          <p class="m-auto text-center w-100 p-2">Some Caption</p>
                        </div> -->
                        <!-- If Content Text -->
                        <!-- <div class="story-content-text">
                          <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                          <p class="m-auto text-center w-100 p-5 h2 h-100">Lorem ipsum dolor sit, amet consectetur
                            adipisicing
                            elit. Nemo libero laudantium vero est quae, perspiciatis, enim quaerat modi alias quos laborum
                            porro exercitationem, delectus officia inventore cupiditate at nesciunt adipisci.</p>
                        </div> -->
                        <!-- If Content Vedio -->

                        @if($story->body != null && is_null($story->media) )
                            <div class="story-content-vedio">
                                <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                @if($story->body != null)
                                    <p class="m-auto text-center w-100 p-2">{{$story->body}}</p>
                                @endif
                            </div>
                        @else
                            @if($story->media->mediaType == 'image')
                                <div class="story-content-img">
                                    <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <img class="w-100"
                                         src="{{asset('media')}}/{{$story->media->filename}}" />
                                    @if($story->body != null)
                                        <p class="m-auto text-center w-100 p-2">{{$story->body}}</p>
                                    @endif
                                </div>
                            @else
                                <div class="story-content-vedio">
                                    <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <video class="story-video w-100 h-100" autoplay muted>
                                        <source src="{{asset('media')}}/{{$story->media->filename}}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    @if($story->body != null)
                                        <p class="m-auto text-center w-100 p-2">{{$story->body}}</p>
                                    @endif
                                </div>
                            @endif

                        @endif

                        @if($story->publisher->id == auth()->user()->id)
                            <button data-toggle="modal" data-target="#story-viewers-modal-{{$story->id}}">story views : {{count($story->viewers)}}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="show-story-modal">
        <div class="modal fade" id="story-viewers-modal-{{$story->id}}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between">
                        <span></span>
                        <h5 class="modal-title" id="exampleModalLabel">Story Viewers</h5>
                    </div>
                    <div class="modal-body">
                        @foreach($story->viewers as $viewer)
                            {{$viewer->name}}
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
