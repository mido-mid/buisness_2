@extends('layouts.app')

@section('content')
    <section id="ez-body__center-content" class="col-lg-8 mt-3">
        <div class="search-bar">
            <input type="text" placeholder="Search" />
        </div>
        <div class="stories d-flex mt-2" id="story">
            <div class="my-story story" data-toggle="modal" data-target="#storyModal">
                <img
                    src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                    alt="Story Pic" />
                <i class="far fa-plus-square"></i>
            </div>
            <div class="add-story-modal">
                <div class="modal fade" id="storyModal" tabindex="-1" aria-hidden="true">
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
                                <form action="" class="container" enctype="multipart/form-data">
                                    <!-- Story Text -->
                                    <div class="post-desc d-flex justify-content-center mt-2">
                        <textarea name="story-text" id="story-text" cols="200" rows="4"
                                  placeholder="Start Typing..."></textarea>
                                    </div>
                                    <!-- Story Images -->
                                    <div class="post-desc d-flex justify-content-center mt-2">
                                        <input class="form-control w-100 mt-2" type="file" name="imgs" id="story-img"
                                               accept="image/*" />
                                    </div>
                                    <!-- Add Story Btn -->
                                    <div class="post-add-btn d-flex justify-content-center mt-4">
                                        <button type="button" class="btn btn-secondary btn-block w-75" data-dismiss="modal">
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @foreach($stories as $story)
                <div class="story" data-toggle="modal" data-target="#showStoryModal">
                    <img
                        src="{{asset('media')}}/{{$story->media->filename}}" />
                </div>
            @endforeach
            <div class="show-story-modal">
                <div class="modal fade" id="showStoryModal" tabindex="-1" aria-hidden="true">
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
                                <div class="story-content-vedio">
                                    <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <video class="w-100 h-100" controls>
                                        <source src="{{asset('media')}}/VID.mp4" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <p class="m-auto text-center w-100 p-2">Some Caption</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="add-post mt-2">
            <!-- Add Post Modal -->
            <div class="modal fade" id="add-post-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" style="margin-top: 22vh">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <span></span>
                            <h5 class="modal-title" id="exampleModalLabel">Add Post</h5>
                            <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" class="container" enctype="multipart/form-data">
                                <!-- Select Post Type -->
                                <div class="post-type d-flex justify-content-between align-items-center m-auto w-75">
                                    <div>Post As:</div>
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="post-type" value="post" id="post-type-post" checked />
                                        <span class="pl-2">Post</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input class="m-0" type="radio" name="post-type" value="service" id="post-type-service" />
                                        <span class="pl-2">Service</span>
                                    </div>
                                </div>
                                <!-- Select post Privacy -->
                                <div class="post-privacy d-flex justify-content-between align-items-center m-auto w-75">
                                    <label for="cars">Choose Post Privacy:</label>
                                    <select id="post-privacy" name="privacy">
                                        <option value="volvo">Volvo</option>
                                        <option value="saab">Saab</option>
                                        <option value="fiat">Fiat</option>
                                        <option value="audi">Audi</option>
                                    </select>
                                </div>
                                <div id="post-type-service-content" class="d-none">
                                    <!-- Select Service Category -->
                                    <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                        <label for="cars">Choose A Category:</label>
                                        <select id="post-category" name="category">
                                            <option value="volvo">Volvo</option>
                                            <option value="saab">Saab</option>
                                            <option value="fiat">Fiat</option>
                                            <option value="audi">Audi</option>
                                        </select>
                                    </div>
                                    <!-- Select Service Price -->
                                    <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                        <input class="w-100 border" type="number" placeholder="Service Price $" />
                                    </div>
                                </div>
                                <!-- Post Desc -->
                                <div class="post-desc d-flex justify-content-center mt-2">
                      <textarea class="w-75 p-2" name="post-text" id="post-text" cols="200" rows="4"
                                placeholder="Post Description..."></textarea>
                                </div>
                                <!-- Post Images -->
                                <div class="post-desc d-flex justify-content-center mt-2">
                                    <input class="form-control w-75 mt-2" type="file" name="imgs" id="imgs" accept="image/*"
                                           multiple />
                                </div>
                                <!-- Add Post Btn -->
                                <div class="post-add-btn d-flex justify-content-center mt-4">
                                    <button type="button" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                        Save
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
        @foreach($posts as $post)
            <div class="post-container bg-white mt-3 p-3" id="post-1">
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
                    <div class="post-options post-options-1">
                        <ul class="options">
                            <li data-toggle="modal" data-target="#advertiseModal">Advertise</li>
                            @if(!$post->saved)
                                <!-- ajax -->
                                <li><a href="">Save Post</a></li>
                            @else
                                <li>Saved</li>
                            @endif
                            <!-- ajax-->
                            <li data-toggle="modal" data-target="#edit-post-modal">Edit</li>
                            <li data-toggle="modal" data-target="#delete-post-modal" class="last-li">
                                Delete</li>
                        </ul>
                    </div>
                    <div class="post-option ml-auto pr-3" onclick="toggleOptions(1)">
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
                                <div><i class="fas fa-thumbs-up"></i>
                                    @if($post->likes->count > 0)
                                        <span>
                                            {{$post->likes->count}}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div><i class="far fa-thumbs-up"></i>
                                    @if($post->likes->count > 0)
                                        <span>
                                            {{$post->likes->count}}
                                        </span>
                                    @endif
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
                        <div class="shares"><i class="fas fa-share ml-3"></i>
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
                                            @if(count($comment->media) > 0)
                                                @if($comment->media[0]->mediaType == 'image')
                                                    <img src="{{asset('media')}}/{{$comment->media[0]->filename}}" class="w-100 pt-3">
                                                @else
                                                    <video class="pt-3" controls>
                                                        <source src="{{asset('media')}}/{{$comment->media[0]->filename}}" type="video/mp4">
                                                    </video>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="comment-options comment-options-1">
                                        <ul class="options">
                                            <li>Report This Comment</li>
                                        </ul>
                                    </div>
                                    <div class="comment-option ml-auto pr-3 pt-2">
                                        <i class="fas fa-ellipsis-v" onclick="toggleCommentOptions(1)"></i>
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
                    <form class="add-commnet mt-2 d-flex align-items-center">
                        <input class="w-100 pl-2" type="text" name="comment" placeholder="Add Your Commnet" />
                        <div class="d-flex align-items-center pr-3">
                            <i class="fas fa-paperclip" onclick="commentAttachClick(1)"></i>
                            <input type="file" id="comment-attach-1" name="img" accept="image/*" />
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
                                        <form action="" class="container">
                                            <p>Select Duration:</p>
                                            <div class="form-group form-check mb-1">
                                                <input type="radio" name="duration" class="form-check-input" id="exampleCheck1" />
                                                <label class="form-check-label" for="exampleCheck1">3 days</label>
                                            </div>
                                            <hr class="m-1">
                                            <div class="form-group form-check mb-1">
                                                <input type="radio" name="duration" class="form-check-input" id="exampleCheck1" />
                                                <label class="form-check-label" for="exampleCheck1">5 days</label>
                                            </div>
                                            <hr class="m-1">
                                            <div class="form-group form-check">
                                                <input type="radio" name="duration" class="form-check-input" id="exampleCheck1" />
                                                <label class="form-check-label" for="exampleCheck1">7 days</label>
                                            </div>
                                            <p>Select Audience:</p>
                                            <div class="form-group form-check mb-1">
                                                <input type="radio" name="audience" class="form-check-input" id="exampleCheck1" />
                                                <label class="form-check-label" for="exampleCheck1">From 100 To 1000</label>
                                            </div>
                                            <hr class="m-1">
                                            <div class="form-group form-check mb-1">
                                                <input type="radio" name="audience" class="form-check-input" id="exampleCheck1" />
                                                <label class="form-check-label" for="exampleCheck1">From 1000 To 2000</label>
                                            </div>
                                            <hr class="m-1">
                                            <div class="form-group form-check">
                                                <input type="radio" name="audience" class="form-check-input" id="exampleCheck1" />
                                                <label class="form-check-label" for="exampleCheck1">From 2000 To 5000</label>
                                            </div>
                                            <div class="form-group d-flex justify-content-between">
                                                <label for="exampleInputEmail1">Target Audience:</label>
                                                <select name="target-audience">
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                            </div>
                                            <div class="form-group d-flex justify-content-between">
                                                <label for="exampleInputEmail1">Target Age:</label>
                                                <select name="target-age">
                                                    <option value="male">From 20 To 30</option>
                                                    <option value="female">From 30 To 40</option>
                                                </select>
                                            </div>
                                            <div class="form-group d-flex justify-content-between">
                                                <label for="exampleInputEmail1">Target Countery:</label>
                                                <select name="target-countery">
                                                    <option value="male">Egypt</option>
                                                    <option value="female">Saudia</option>
                                                </select>
                                            </div>
                                            <div class="form-group d-flex justify-content-between">
                                                <label for="exampleInputEmail1">Target City:</label>
                                                <select name="target-countery">
                                                    <option value="male">Alex</option>
                                                    <option value="female">Cairo</option>
                                                </select>
                                            </div>
                                            <input type="hidden" name="postId" :value="post.id">
                                            <button type="submit" class="btn btn-warning btn-block">
                                                Submit
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="post-edit-modal">
                        <div class="modal fade" id="edit-post-modal" tabindex="-1" aria-hidden="true">
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
                                        <form action="" class="container" enctype="multipart/form-data">
                                            <!-- Select post Privacy -->
                                            <div class="post-privacy d-flex justify-content-between align-items-center m-auto w-75">
                                                <label for="cars">Choose Post Privacy:</label>
                                                <select id="post-privacy" name="privacy">
                                                    <option value="volvo">Volvo</option>
                                                    <option value="saab">Saab</option>
                                                    <option value="fiat">Fiat</option>
                                                    <option value="audi">Audi</option>
                                                </select>
                                            </div>
                                            <div v-if="post.postType == 'service'" id="post-type-service-content">
                                                <!-- Select Service Category -->
                                                <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                                    <label for="cars">Choose A Category:</label>
                                                    <select id="post-category" name="category">
                                                        <option value="volvo">Volvo</option>
                                                        <option value="saab">Saab</option>
                                                        <option value="fiat">Fiat</option>
                                                        <option value="audi">Audi</option>
                                                    </select>
                                                </div>
                                                <!-- Select Service Price -->
                                                <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                                    <input class="w-100 border" type="number" placeholder="Service Price $" />
                                                </div>
                                            </div>
                                            <!-- Post Desc -->
                                            <div class="post-desc d-flex justify-content-center mt-2">
                              <textarea class="w-75" name="post-text" id="post-text" cols="200" rows="4"
                                        placeholder="Start Typing..." :value="post.text"></textarea>
                                            </div>
                                            <!-- Post Images -->
                                            <div class="post-desc d-flex justify-content-center mt-2">
                                                <input class="form-control w-75 mt-2" type="file" name="imgs" id="imgs" accept="image/*"
                                                       multiple />
                                            </div>
                                            <!-- Add Post Btn -->
                                            <div class="post-add-btn d-flex justify-content-center mt-4">
                                                <button type="button" class="btn btn-warning btn-block w-75" data-dismiss="modal">
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

                                <a id="friend_btn" href="{{url('addfriend',["user_id" => $user->id ,"type" => "addFriendRequest"])}}" data-id="{{$user->id}}" class="btn btn-warning text-white">add friend</a>
{{--                                <form id="friend-form" action="{{ route('addfriend') }}" method="POST" enctype="multipart/form-data" style="display: none;">--}}
{{--                                    @csrf--}}
{{--                                    <input type="hidden" name="receiverId" value="{{$user->id}}">--}}
{{--                                    <input type="hidden" name="requestType" value="addFriendRequest">--}}
{{--                                </form>--}}

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
                                    <a id="join_btn" class="btn btn-warning text-white">Join</a>
                                    <form id="join-form" action="{{ route('join_group') }}" method="POST" style="display: none;">
                                        @csrf
{{--                                        <input type="hidden" name="receiverId" value="{{$user->id}}">--}}
                                        <input type="hidden" name="requestType" value="addFriendRequest">
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
                                    <a id="like_btn" class="btn btn-warning text-white">Like</a>
                                    <form id="like-form" action="{{ route('like_page') }}" method="POST" style="display: none;">
                                        @csrf
{{--                                        <input type="hidden" name="receiverId" value="{{$user->id}}">--}}
                                        <input type="hidden" name="requestType" value="addFriendRequest">
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

