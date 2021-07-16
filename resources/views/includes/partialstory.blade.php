

@section('story')
    <div class="story" data-toggle="modal" data-target="#showStoryModal-{{$story->id}}">
        <img
            src="{{asset('media')}}/{{$story->cover_image}}" />
    </div>
    <div class="show-story-modal">
        <div class="modal fade" id="showStoryModal-{{$story->id}}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
