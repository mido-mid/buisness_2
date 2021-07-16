@extends('layouts.app')

@section('content')
    <section id="ez-body__center-content" class="col-lg-8 mt-3">
        <div class="search-bar">
            <input type="text" placeholder="Search" />
        </div>
        <div class="stories d-flex mt-2" id="story">
            <div class="my-story story" data-toggle="modal" data-target="#add-story-modal">
                <img
                    src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                    alt="Story Pic" />
                <i class="far fa-plus-square"></i>
            </div>
            <div class="add-story-modal">
                <div class="modal fade" id="add-story-modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog" style="margin-top: 22vh">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between">
                                <span></span>
                                <h5 class="modal-title" id="exampleModalLabel">
                                    Add Story
                                </h5>
                                <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="col-12" id="error-message-story" style="display: none">
                                    <div class="alert alert-danger alert-dismissible fade show" id="error-status-story" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                                <form action="{{route('stories.store')}}" method="POST" id="add-story-form" class="container" enctype="multipart/form-data">

                                    @csrf

                                    <!-- Story Text -->
                                    <div class="post-desc d-flex justify-content-center mt-2">
                                        <textarea name="body" id="story-text" cols="200" rows="4"
                                                  placeholder="Start Typing..."></textarea>
                                    </div>

                                    <div class="post-privacy d-flex justify-content-between align-items-center m-auto w-75">
                                        <label for="cars">Choose Story Privacy:</label>
                                        <select id="post-privacy" name="privacy_id">
                                            @foreach($privacy as $storyprivacy)
                                                @if(App::getlocale() == 'en')
                                                    <option value="{{$storyprivacy->id}}">{{$storyprivacy->name_en}}</option>
                                                @else
                                                    <option value="{{$storyprivacy->id}}">{{$storyprivacy->name_ar}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Story Images -->
                                    <div class="post-desc d-flex justify-content-center mt-2">
                                        <input class="form-control w-100 mt-2" type="file" name="media[]" id="story-img" multiple/>
                                    </div>


                                    <div class="post-desc d-flex justify-content-center mt-2">
                                        <input class="form-control w-100 mt-2" type="file" name="cover_image" id="story-img"/>
                                    </div>
                                    <!-- Add Story Btn -->
                                    <div class="post-add-btn d-flex justify-content-center mt-4">
                                        <button type="button" onclick="addStorySubmit()" class="btn btn-secondary btn-block w-75">
                                            add story
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="addedstory">


            </div>
            @foreach($stories as $story)
                <div onclick="addStoryViews({{$story->id}})" class="story" data-toggle="modal" data-target="#showStoryModal-{{$story->id}}" id="story-{{$story->id}}">
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
            @endforeach
        </div>

{{--        <div class="col-12">--}}
{{--            @if (session('status'))--}}
{{--                <div class="alert alert-success alert-dismissible fade show" role="alert">--}}
{{--                    {{ session('status') }}--}}
{{--                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--        </div>--}}

        <div class="col-12" id="success-message" style="display: none">
            <div class="alert alert-success alert-dismissible fade show" id="success-status" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>

        <div class="modal" id="success-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" style="margin-top: 22vh">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between">
                        <span></span>
                        <h5 class="modal-title" id="exampleModalLabel">Success Modal</h5>
                        <button type="button" id="success-modal-dismiss" class="close ml-0" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="success-modal-message" >
                        post created successfully
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="add-post mt-2">
            <!-- Add Post Modal -->
            <div class="modal" id="add-post-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" style="margin-top: 22vh">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <span></span>
                            <h5 class="modal-title" id="exampleModalLabel">Add Post</h5>
                            <button type="button" id="add-post-modal-dismiss" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="col-12" id="error-message" style="display: none">
                                <div class="alert alert-danger alert-dismissible fade show" id="error-status" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                            <form action="{{route('posts.store')}}" method="POST" class="container" id="add-post-form" enctype="multipart/form-data">

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
                                                <option value="{{$category->id}}">{{$category->name_en}}</option>
                                            @else
                                                <option value="{{$category->id}}">{{$category->name_ar}}</option>
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
                                <!-- Post Images -->
                                <div class="post-desc d-flex justify-content-center mt-2">
                                    <input class="form-control w-75 mt-2" type="file" name="media[]" id="imgs"
                                           multiple />
                                </div>
                                <!-- Add Post Btn -->
                                <div class="post-add-btn d-flex justify-content-center mt-4">
                                    <button type="button" onclick="addPostSubmit()" class="btn btn-warning btn-block w-75">
                                        post
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <input type="text" placeholder="Add New Post" class="w-100" data-toggle="modal"
                   data-target="#add-post-modal" />
        </div>
        <div id="addedpost">


        </div>
        @foreach($posts as $post)
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
                        <div class="comments" onclick="toggleComments({{$post->id}})"><i class="far fa-comment ml-3"></i>
                                <span id="comment-count-{{$post->id}}">
                                    @if($post->comments->count > 0)
                                        {{$post->comments->count}}
                                    @endif
                                </span>
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
                    <div class="post-comment-list post-comment-list-{{$post->id}} mt-2">
                        <div class="hide-commnet-list d-flex flex-row-reverse">
                            <span onclick="toggleComments({{$post->id}})"><i class="fas fa-chevron-up"></i> Hide</span>
                        </div>
                        @if($post->comments)
                            @foreach($post->comments as $comment)
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
                                @if(count($post->comments) > 1)
                                    <hr class="m-0" />
                                @endif
                            <!-- if there is multi comments then take uncomment hr -->
    {{--                        <hr class="m-0" /> --}}
                            @endforeach
                            <div id="added-comment-{{$post->id}}">

                            </div>
                        @endif
                    </div>
                    <button type="button" id="comment-submit-btn-{{$post->id}}" onclick="event.preventDefault();
                              addCommentSubmit({{$post->id}})" hidden></button>
                    <form class="add-commnet mt-2 d-flex align-items-center" onkeypress="if (event.keyCode === 13) { event.preventDefault(); $('#comment-submit-btn-{{$post->id}}').click();}" id="add-comment-form-{{$post->id}}" action="{{route('comments.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="post_id" value="{{$post->id}}" />
                        <input class="w-100 pl-2" type="text" name="body" placeholder="Add Your Comment" />
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
        @endforeach
    </section>
    <section id="ez-body__right-sidebar" class="col-lg-2 ez-sidebar">
        <ul class="pt-4" id="right-sidebar__items">
            @if(count($expected_posts) > 0)
                <li>
                    <h6 class="pb-2" style="font-weight: bold;font-size: 15px">Posts You May Like</h6>
                    <div class="suggested-posts mb-1">
                        @foreach($expected_posts as $post)
                            <div class="post">
                                <section class="posted-by">
                                    @if($post->publisher->personal_image)
                                        <img
                                            class="profile-figure"
                                            src="{{asset('media')}}/{{$post->publisher->personal_image}}"
                                            alt="User Profile Pic"
                                        />
                                    @else
                                        <img
                                            class="profile-figure"
                                            src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                            alt="User Profile Pic"
                                        />
                                    @endif
                                    <span>{{$post->publisher->name}}</span>
                                </section>
                                <section class="post-desc">
                                    <p>{{$post->body}}</p>
                                </section>
                                <section class="post-img">
                                    @if(count($post->media) > 0)
                                        @foreach($post->media as $media)
                                            <img
                                                class="profile-figure"
                                                src="{{asset('media')}}/{{$media->filename}}"
                                                alt="User Profile Pic"
                                            />
                                        @endforeach
                                    @endif
                                </section>
                            </div>
                        @endforeach
                    </div>
                </li>
            @endif
            @if(count($expected_users) > 0)
                <li class="mt-3">
                    <h6 class="pb-2" style="font-weight: bold;font-size: 15px">People You May Know</h6>
                    <div class="suggested-peoples">
                        @foreach($expected_users as $user)
                            <div class="people mt-2">
                                <div class="people-info d-flex">
                                    @if($user->personal_image)
                                        <img
                                            class="profile-figure"
                                            src="{{asset('media')}}/{{$user->personal_image}}"
                                            alt="User Profile Pic"
                                        />
                                    @else
                                        <img
                                            class="profile-figure"
                                            src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                            alt="User Profile Pic"
                                        />
                                    @endif
                                        <div class="d-flex flex-column align-items-center">
                                            <p><b>{{$user->name}}</b></p>
                                            <p>
                                                @if($user->followers > 1000)
                                                    {{$user->followers}}K Follower
                                                @else
                                                    {{$user->followers}} Follower
                                                @endif
                                            </p>
                                        </div>
                                </div>

                                <a id="friend-btn-{{$user->id}}" onclick="addFriendSubmit({{$user->id}})" class="btn btn-warning text-white">add friend</a>
                                <form id="friend-form-{{$user->id}}" action="{{ route('addfriend') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="receiverId" value="{{$user->id}}">
                                    <input type="hidden" name="requestType" id="request-type-{{$user->id}}" value="addFriendRequest">
                                </form>

                            </div>
                        @endforeach
                    </div>
                </li>
            @endif
            @if(count($expected_groups) > 0)
                <li class="mt-3">
                    <h6 class="pb-2" style="font-weight: bold;font-size: 15px">Groups You May Like</h6>
                    <div class="suggested-groups">
                        @foreach($expected_groups as $group)
                            <div class="group">
                                <div class="group-banner">
                                    @if($group->cover_image)
                                        <img
                                            width="100%"
                                            src="{{asset('media')}}/{{$group->cover_image}}"
                                            alt="User Profile Pic"
                                        />
                                    @else
                                        <img
                                            width="100%"
                                            src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                            alt="User Profile Pic"
                                        />
                                    @endif
                                </div>
                                <div class="mt-2 group-info">
                                    <div>
                                        <p><b>{{$group->name}}</b></p>
                                        <p>{{$group->members}} members</p>
                                    </div>
                                    <a id="join-btn-{{$group->id}}" onclick="joinGroupSubmit({{$group->id}})" class="btn btn-warning text-white">Join</a>
                                    <form id="join-group-form-{{$group->id}}" action="{{ route('join_group') }}" method="POST" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="group_id" value="{{$group->id}}">
                                        <input type="hidden" id="join-flag-{{$group->id}}" name="flag" value="0">
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </li>
            @endif
            @if(count($expected_pages) > 0)
                <li class="mt-3">
                    <h6 class="pb-2" style="font-weight: bold;font-size: 15px">Pages You May Like</h6>
                    <div class="suggested-groups">
                        @foreach($expected_pages as $page)
                            <div class="group">
                                <div class="group-banner">
                                    @if($page->cover_image)
                                        <img
                                            width="100%"
                                            src="{{asset('media')}}/{{$page->cover_image}}"
                                            alt="User Profile Pic"
                                        />
                                    @else
                                        <img
                                            width="100%"
                                            src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                            alt="User Profile Pic"
                                        />
                                    @endif
                                </div>
                                <div class="mt-2 group-info">
                                    <div>
                                        <p><b>{{$page->name}}</b></p>
                                        <p>{{$page->members}} likes</p>
                                    </div>
                                    <a id="like-page-btn-{{$page->id}}" onclick="likePageSubmit({{$page->id}})" class="btn btn-warning text-white">Like</a>
                                    <form id="like-page-form-{{$page->id}}" action="{{ route('like_page') }}" method="POST" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="page_id" value="{{$page->id}}">
                                        <input type="hidden" id="like-page-flag-{{$page->id}}" name="flag" value="0">
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </li>
            @endif
        </ul>
    </section>
@endsection

