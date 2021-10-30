@extends('layouts.app')

@section('content')
    <div class="modal fade" id="progress-modal" data-controls-modal="progress-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title in" id="myModalLabel">{{__('user.please_wait')}}</h4>
                    <h4 class="modal-title hide" id="myModalLabel">{{__('user.complete')}}</h4>
                </div>
                <div class="modal-body center-block">
                    <div id="status">{{__('user.progress')}}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default hide" data-dismiss="modal" id="btnClose">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal" id="success-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="margin-top: 22vh">
            <div class="modal-content">
                <a class="success-close" href="#" data-dismiss="modal">&times;</a>
                <div class="page-body">
                    <div class="head">
                        <h3 style="margin-top:5px;" id="success-modal-message">Lorem ipsum dolor sit amet</h3>
                    </div>
                    <h1 style="text-align:center;">
                        <div class="checkmark-circle">
                            <div class="background"></div>
                            <div class="checkmark draw"></div>
                        </div>
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <section id="ez-body__center-content" class="col-lg-8 mt-3">
        <div class="post-container bg-white mt-3 p-3" id="post-{{$post->id}}">
            <div class="post-owner d-flex align-items-center">
                @if($post->source == "page")
                    <div class="owner-img">
                        <a style="display: inline" href="{{route('profile',$post->publisher->id)}}"><img src="{{asset('media')}}/{{$post->page->profile_image}}" class="rounded-circle" /></a>
                    </div>
                @else
                    @if($post->publisher->personal_image)
                        <div class="owner-img">
                            <a style="display: inline" href="{{route('profile',$post->publisher->id)}}"><img src="{{asset('media')}}/{{$post->publisher->personal_image}}" class="rounded-circle" /></a>
                        </div>
                    @else
                        <div class="owner-img">
                            <a style="display: inline" href="{{route('profile',$post->publisher->id)}}"><img src="{{asset('media')}}/img.jpg" class="rounded-circle" /></a>
                        </div>
                    @endif
                @endif
                <div class="owner-name pl-3">
                    @if($post->source == "page")
                        <a href="{{route('main-page',$post->page_id)}}"><b>
                                {{$post->page->name}}
                            </b></a>
                    @else
                        <a href="{{route('profile',$post->publisher->id)}}"><b>
                                @if($post->publisher->official == 1)
                                    <i class="fas fa-user-check" style="color: #ffc107"></i>
                                @endif
                                {{$post->publisher->name}}
                            </b></a>
                    @endif

                    @if($post->sponsored)
                        <div style="font-size: small">
                            <span><i class="fas fa-ad"></i></span>
                            {{__('home.sponsored')}}
                        </div>

                    @endif

                    @if($post->tags != null )
                        <a data-toggle="modal" data-target="#show-post-tags-modal-{{$post->id}}"><b>
                                {{__('home.with')}}
                                @if($post->tagged == true)
                                    {{__('home.you_and')}} {{count($post->tags_info) - 1}}
                                @else
                                    {{count($post->tags_info)}}
                                @endif
                                {{__('home.others')}}
                            </b></a>
                    @endif

                    @if($post->source == "group")
                        <a href="{{route('main-group',$post->group_id)}}">
                            <Span><i class="fas fa-caret-right"></i></Span>
                            {{$post->group->name}}
                        </a>
                    @endif

                    <span style="display: block">{{date('d/m/Y',strtotime($post->created_at))}}</span>
                </div>
                <!-- Post options -->
                <div class="post-options post-options-{{$post->id}}">
                    <ul class="options">
                        @if($post->source == 'page')
                            @if($post->isPageAdmin)
                                <li data-toggle="modal" data-target="#advertise-post-modal-{{$post->id}}">{{__('home.advertise')}}</li>
                            @endif
                        @else
                            @if($post->sponsored == false && $post->type == "post" && $post->publisherId == auth()->user()->id)
                                <li data-toggle="modal" data-target="#advertise-post-modal-{{$post->id}}">{{__('home.advertise')}}</li>
                            @endif
                        @endif
                        @if(!$post->saved)
                        <!-- ajax -->
                            <li><a id="save-post-{{$post->id}}" onclick="savePostSubmit({{$post->id}})">{{__('home.save_post')}}</a></li>
                            <form id="save-post-form-{{$post->id}}" action="{{ route('savepost') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                @csrf
                                <input type="hidden" name="post_id" value="{{$post->id}}">
                                <input type="hidden" id="save-post-flag-{{$post->id}}" name="flag" value="0">
                            </form>
                        @else
                            <li><a id="save-post-{{$post->id}}" onclick="savePostSubmit({{$post->id}})">{{__('home.saved')}}</a></li>
                            <form id="save-post-form-{{$post->id}}" action="{{ route('savepost') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                @csrf
                                <input type="hidden" name="post_id" value="{{$post->id}}">
                                <input type="hidden" id="save-post-flag-{{$post->id}}" name="flag" value="1">
                            </form>
                        @endif

                        @if($post->source == 'page')
                            @if($post->isPageAdmin)
                                <li data-toggle="modal" onclick="textAreaChange({{$post->id}})" data-target="#edit-post-modal-{{$post->id}}">{{__('user.edit')}}</li>
                                <form action="{{ route('posts.destroy', $post->id) }}" id="delete-post-form-{{$post->id}}" method="post">
                                @csrf
                                @method('delete')
                                <!-- ajax-->
                                    <li onclick="confirm('{{ __("home.confirm") }}') ? deletePostSubmit({{$post->id}}) : ''">
                                        {{__('user.delete')}}</li>
                                </form>
                            @endif
                        @else
                            @if($post->publisherId == auth()->user()->id)
                                <li data-toggle="modal" onclick="textAreaChange({{$post->id}})" data-target="#edit-post-modal-{{$post->id}}">{{__('user.edit')}}</li>
                                <form action="{{ route('posts.destroy', $post->id) }}" id="delete-post-form-{{$post->id}}" method="post">
                                @csrf
                                @method('delete')
                                <!-- ajax-->
                                    <li onclick="confirm('{{ __("home.confirm") }}') ? deletePostSubmit({{$post->id}}) : ''">
                                        {{__('user.delete')}}</li>
                                </form>
                            @endif
                        @endif
                        <li data-toggle="modal" data-target="#report-post-modal-{{$post->id}}" class="last-li">{{__('home.report')}}</li>
                    </ul>
                </div>
                <div class="post-option ml-auto pr-3" onclick="toggleOptions({{$post->id}});applySelect2();">
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>

            @if($post->tags != null)

                <div class="show-story-views-modal">
                    <div class="modal fade" id="show-post-tags-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin-top: 22vh">
                            <div class="modal-content">
                                <div class="modal-header d-flex justify-content-between">
                                    <h5 class="modal-title" id="exampleModalLabel">
                                        {{__('home.post_tags')}}
                                    </h5>
                                    <button type="button" class="close ml-0" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @foreach($post->tags_info as $tag)
                                        <div class="people-info d-flex align-items-center">
                                            @if($tag->personal_image != null)
                                                <a href="{{route('user.view.profile',$tag->id)}}">
                                                    <img style="width: 45px; height: 45px" class="profile-figure rounded-circle"
                                                         src="{{asset('media')}}/{{$tag->personal_image}}"
                                                         alt="User Profile Pic">
                                                </a>
                                            @else
                                                <a href="{{route('user.view.profile',$tag->id)}}">
                                                    <img style="width: 45px; height: 45px" class="profile-figure rounded-circle"
                                                         src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                                         alt="User Profile Pic">
                                                </a>
                                            @endif
                                            <p class="mb-0 ml-3"><b><a href="{{route('user.view.profile',$tag->id)}}">{{$tag->name}}</a></b></p>
                                        </div>
                                        @if($loop->last == false)
                                            <hr>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="post-edit-modal">
                <div class="modal fade" id="report-post-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
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
                                <div class="col-12" id="error-message-{{$post->id}}" style="display: none">
                                    <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$post->id}}" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                                <form action="{{route('userreports.store')}}" id="report-post-form-{{$post->id}}" method="POST" class="container" enctype="multipart/form-data">

                                @csrf

                                <!-- Post Desc -->
                                    <div class="post-desc d-flex justify-content-center mt-2">
                                                  <textarea class="w-75" name="body" id="post-text" cols="200" rows="4"
                                                            placeholder="{{__('home.text_area')}}" ></textarea>
                                    </div>
                                    <!-- Add Post Btn -->
                                    <input type="hidden" name="model_id" value="{{$post->id}}">
                                    <input type="hidden" name="model_type" value="post">

                                    <div class="post-add-btn d-flex justify-content-center mt-4">
                                        <button type="button" onclick="reportPostSubmit({{$post->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                            {{__('home.report')}}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="post-desc mt-3">
                <!-- if lang arabic -->
                @if(App::getlocale() == 'ar')
                    <pre style="text-align:right;"><?php echo $post->body ?></pre>
                @else
                    <pre style="text-align:left;"><?php echo $post->body ?></pre>
                @endif
                @if(count($post->media) > 0 && $post->type == "post")
                    <div class="media">
                    @if(count($post->media) == 1)

                        @foreach($post->media as $media)
                            @if($media->mediaType == 'image')
                                <!-- if media img and imgs=1 -->
                                    <div class="d-flex post-height" style="width: 100%">
                                        <img src="{{asset('media')}}/{{$media->filename}}" style="height: 400px" alt="opel car" />
                                    </div>
                                @else
                                    <video class="p-1" controls>
                                        <source src="{{asset('media')}}/{{$media->filename}}" style="height: 400px" type="video/mp4">
                                        {{__('home.no_browser')}}
                                    </video>
                                @endif
                            @endforeach

                        @else
                            <div class="w-100">
                                <div class="d-flex w-100">
                                @if($post->media[0]->mediaType == 'image')
                                    <!-- if media img and imgs=1 -->
                                        <div class="d-flex" style="width: 100%">
                                            <img src="{{asset('media')}}/{{$post->media[0]->filename}}" style="height: 400px" alt="opel car" />
                                        </div>
                                    @else
                                        <video class="p-1" controls>
                                            <source src="{{asset('media')}}/{{$post->media[0]->filename}}" type="video/mp4">
                                            {{__('home.no_browser')}}
                                        </video>
                                    @endif


                                    @if($post->media[1]->mediaType == 'image')
                                    <!-- if media img and imgs=1 -->
                                        <div class="p-1 w-100" style="width: 100%">
                                            <img src="{{asset('media')}}/{{$post->media[1]->filename}}" style="height: 400px" alt="opel car" />
                                        </div>
                                    @else
                                        <video class="p-1 w-100" controls>
                                            <source src="{{asset('media')}}/{{$post->media[1]->filename}}" type="video/mp4">
                                            {{__('home.no_browser')}}
                                        </video>
                                    @endif
                                </div>

                                @if(count($post->media) > 2)
                                    <div class="d-flex w-100">
                                    @if($post->media[2]->mediaType == 'image')
                                        <!-- if media img and imgs=1 -->
                                            <div class="p-1 w-100" style="width: 100%">
                                                <img src="{{asset('media')}}/{{$post->media[2]->filename}}" style="height: 400px" alt="opel car" />
                                            </div>
                                        @else
                                            <video class="p-1 w-100" controls>
                                                <source src="{{asset('media')}}/{{$post->media[2]->filename}}" type="video/mp4">
                                                {{__('home.no_browser')}}
                                            </video>
                                        @endif

                                        @if(count($post->media) == 4)

                                            @if($post->media[3]->mediaType == 'image')
                                            <!-- if media img and imgs=1 -->
                                                <div class="p-1 w-100" style="width: 100%">
                                                    <img src="{{asset('media')}}/{{$post->media[3]->filename}}" style="height: 400px" alt="opel car" />
                                                </div>
                                            @else
                                                <video class="p-1 w-100" controls>
                                                    <source src="{{asset('media')}}/{{$post->media[3]->filename}}" type="video/mp4">
                                                    {{__('home.no_browser')}}
                                                </video>
                                            @endif
                                        @endif

                                        @if(count($post->media) > 4)
                                            <div class="more-media w-50" data-toggle="modal" data-target="#more-media-modal-{{$post->id}}">
                                                <p>+{{count($post->media) - 3}}</p>
                                                <div class="overlay"></div>

                                            @if($post->media[3]->mediaType == 'image')
                                                <!-- if media img and imgs=1 -->
                                                    <div class="p-1 w-100" style="width: 100%">
                                                        <img src="{{asset('media')}}/{{$post->media[3]->filename}}" style="height: 400px" alt="opel car" />
                                                    </div>
                                                @else
                                                    <video class="p-1 w-100" controls>
                                                        <source src="{{asset('media')}}/{{$post->media[3]->filename}}" type="video/mp4">
                                                        {{__('home.no_browser')}}
                                                    </video>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="post-more-media-modal">
                        <div class="modal fade" id="more-media-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog" style="margin-top: 10vh">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div id="media-carousel-{{$post->id}}" class="carousel slide" data-ride="carousel">
                                            <div class="carousel-inner">
                                                @foreach($post->media as $media)
                                                    <div class="carousel-item @if ($loop->first == true) active @endif">
                                                        <img src="{{asset('media')}}/{{$media->filename}}" class="d-block w-100" alt="...">
                                                    </div>
                                                @endforeach
                                            </div>
                                            <a class="carousel-control-prev" href="#media-carousel-{{$post->id}}" role="button"
                                               data-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                            <a class="carousel-control-next" href="#media-carousel-{{$post->id}}" role="button"
                                               data-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($post->type == "share")
                    <div class="post-container shared-post bg-white p-2">
                        <div class="post-owner d-flex align-items-center">
                            @if($post->shared_post->source == "page")
                                <div class="owner-img">
                                    <a style="display: inline" href="{{route('profile',$post->shared_post->publisher->id)}}"><img src="/{{$post->shared_post->page->profile_image}}" class="rounded-circle" /></a>
                                </div>
                            @else
                                @if($post->shared_post->publisher->personal_image)
                                    <div class="owner-img">
                                        <a style="display: inline" href="{{route('profile',$post->shared_post->publisher->id)}}"><img src="{{asset('media')}}/{{$post->shared_post->publisher->personal_image}}" class="rounded-circle" /></a>
                                    </div>
                                @else
                                    <div class="owner-img">
                                        <a style="display: inline" href="{{route('profile',$post->shared_post->publisher->id)}}"><img src="{{asset('media')}}/img.jpg" class="rounded-circle" /></a>
                                    </div>
                                @endif
                            @endif
                            <div class="owner-name pl-3">
                                @if($post->shared_post->source == "page")
                                    <a href="{{route('main-page',$post->shared_post->page_id)}}"><b>
                                            {{$post->shared_post->page->name}}
                                        </b></a>
                                @else
                                    <a href="{{route('profile',$post->shared_post->publisher->id)}}"><b>
                                            @if($post->shared_post->publisher->official == 1)
                                                <i class="fas fa-user-check" style="color: #ffc107"></i>
                                            @endif
                                            {{$post->shared_post->publisher->name}}
                                        </b></a>
                                @endif

                                @if($post->shared_post->sponsored)
                                    <div style="font-size: small">
                                        <span><i class="fas fa-ad"></i></span>
                                        {{__('home.sponsored')}}
                                    </div>

                                @endif

                                @if($post->shared_post->tags != null )
                                    <a data-toggle="modal" data-target="#show-post-tags-modal-{{$post->shared_post->id}}"><b>
                                            with
                                            @if($post->shared_post->tagged == true)
                                                {{__('home.you_and')}} {{count($post->shared_post->tags_info) - 1}}
                                            @else
                                                {{count($post->shared_post->tags_info)}}
                                            @endif
                                            {{__('home.others')}}
                                        </b></a>
                                @endif

                                @if($post->shared_post->source == "group")
                                    <a href="{{route('main-group',$post->shared_post->group_id)}}">
                                        <Span><i class="fas fa-caret-right"></i></Span>
                                        {{$post->shared_post->group->name}}
                                    </a>
                                @endif

                                <span style="display: block">{{date('d/m/Y',strtotime($post->shared_post->created_at))}}</span>
                            </div>
                        </div>

                        <div class="post-desc mt-3">
                            <!-- if lang arabic -->
                            @if(App::getlocale() == 'ar')
                                <pre style="text-align:right;"><?php echo $post->shared_post->body ?></pre>
                            @else
                                <pre style="text-align:left;"><?php echo $post->shared_post->body ?></pre>
                            @endif
                            @if(count($post->shared_post->media) > 0)
                                <div class="media">
                                @if(count($post->shared_post->media) == 1)

                                    @foreach($post->shared_post->media as $media)
                                        @if($media->mediaType == 'image')
                                            <!-- if media img and imgs=1 -->
                                                <div class="d-flex" style="width: 100%">
                                                    <img src="{{asset('media')}}/{{$media->filename}}" style="height: 400px" alt="opel car" />
                                                </div>
                                            @else
                                                <video class="p-1" controls>
                                                    <source src="{{asset('media')}}/{{$media->filename}}" type="video/mp4">
                                                    {{__('home.no_browser')}}
                                                </video>
                                            @endif
                                        @endforeach

                                    @else
                                        <div class="w-100">
                                            <div class="d-flex w-100">
                                            @if($post->shared_post->media[0]->mediaType == 'image')
                                                <!-- if media img and imgs=1 -->
                                                    <div class="d-flex" style="width: 100%">
                                                        <img src="{{asset('media')}}/{{$post->shared_post->media[0]->filename}}" style="height: 400px" alt="opel car" />
                                                    </div>
                                                @else
                                                    <video class="p-1" controls>
                                                        <source src="{{asset('media')}}/{{$post->shared_post->media[0]->filename}}" type="video/mp4">
                                                        {{__('home.no_browser')}}
                                                    </video>
                                                @endif


                                                @if($post->shared_post->media[1]->mediaType == 'image')
                                                <!-- if media img and imgs=1 -->
                                                    <div class="p-1 w-100" style="width: 100%">
                                                        <img src="{{asset('media')}}/{{$post->shared_post->media[1]->filename}}" style="height: 400px" alt="opel car" />
                                                    </div>
                                                @else
                                                    <video class="p-1 w-100" controls>
                                                        <source src="{{asset('media')}}/{{$post->shared_post->media[1]->filename}}" type="video/mp4">
                                                        {{__('home.no_browser')}}
                                                    </video>
                                                @endif
                                            </div>

                                            @if(count($post->shared_post->media) > 2)
                                                <div class="d-flex w-100">
                                                @if($post->shared_post->media[2]->mediaType == 'image')
                                                    <!-- if media img and imgs=1 -->
                                                        <div class="p-1 w-100" style="width: 100%">
                                                            <img src="{{asset('media')}}/{{$post->shared_post->media[2]->filename}}" style="height: 400px" alt="opel car" />
                                                        </div>
                                                    @else
                                                        <video class="p-1 w-100" controls>
                                                            <source src="{{asset('media')}}/{{$post->shared_post->media[2]->filename}}" type="video/mp4">
                                                            {{__('home.no_browser')}}
                                                        </video>
                                                    @endif

                                                    @if(count($post->shared_post->media) == 4)

                                                        @if($post->shared_post->media[3]->mediaType == 'image')
                                                        <!-- if media img and imgs=1 -->
                                                            <div class="p-1 w-100" style="width: 100%">
                                                                <img src="{{asset('media')}}/{{$post->shared_post->media[3]->filename}}" style="height: 400px" alt="opel car" />
                                                            </div>
                                                        @else
                                                            <video class="p-1 w-100" controls>
                                                                <source src="{{asset('media')}}/{{$post->shared_post->media[3]->filename}}" type="video/mp4">
                                                                {{__('home.no_browser')}}
                                                            </video>
                                                        @endif
                                                    @endif

                                                    @if(count($post->shared_post->media) > 4)
                                                        <div class="more-media w-50" data-toggle="modal" data-target="#more-media-modal-{{$post->shared_post->id}}">
                                                            <p>+{{count($post->shared_post->media) - 3}}</p>
                                                            <div class="overlay"></div>

                                                        @if($post->shared_post->media[3]->mediaType == 'image')
                                                            <!-- if media img and imgs=1 -->
                                                                <div class="p-1 w-100" style="width: 100%">
                                                                    <img src="{{asset('media')}}/{{$post->shared_post->media[3]->filename}}" style="height: 400px" alt="opel car" />
                                                                </div>
                                                            @else
                                                                <video class="p-1 w-100" controls>
                                                                    <source src="{{asset('media')}}/{{$post->shared_post->media[3]->filename}}" type="video/mp4">
                                                                    {{__('home.no_browser')}}
                                                                </video>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="post-more-media-modal">
                                    <div class="modal fade" id="more-media-modal-{{$post->shared_post->id}}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog" style="margin-top: 10vh">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <div id="media-carousel-{{$post->shared_post->id}}" class="carousel slide" data-ride="carousel">
                                                        <div class="carousel-inner">
                                                            @foreach($post->shared_post->media as $media)
                                                                <div class="carousel-item @if ($loop->first == true) active @endif">
                                                                    <img src="{{asset('media')}}/{{$media->filename}}" class="d-block w-100" alt="...">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <a class="carousel-control-prev" href="#media-carousel-{{$post->shared_post->id}}" role="button"
                                                           data-slide="prev">
                                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                        <a class="carousel-control-next" href="#media-carousel-{{$post->shared_post->id}}" role="button"
                                                           data-slide="next">
                                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="post-statistics mt-3 d-flex">
                    <div class="likes">
                        @if($post->liked)
                            <div class="reaction-container" id="reaction-container-{{$post->id}}">
                                            <span class="reaction-btn">
                                                    <span class="reaction-btn-emo like-btn-{{$post->user_react[0]->name_en}}" id="reaction-btn-emo-{{$post->id}}"></span>
                                                    <span class="reaction-btn-text reaction-btn-text-{{$post->user_react[0]->name_en}} active" onclick="unlikeModelSubmit({{$post->id}},{{$post->user_react[0]->id}})" id="reaction-btn-text-{{$post->id}}">
                                                        @if(App::getLocale() == 'ar')
                                                            {{$post->user_react[0]->name_ar}}
                                                        @else
                                                            {{$post->user_react[0]->name_en}}
                                                        @endif
                                                            <form id="unlike-form-{{$post->id}}-{{$post->user_react[0]->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                                @csrf
                                                                <input type="hidden" name="model_id" value="{{$post->id}}">
                                                                <input type="hidden" name="model_type" value="post">
                                                                <input type="hidden" name="reactId" value="{{$post->user_react[0]->id}}">
                                                               <input type="hidden" name="requestType" id="like-request-type-{{$post->id}}" value="delete">
                                                            </form>
                                                    </span>
                                                    <ul class="emojies-box">
                                                        @foreach($reacts as $react)
                                                            <!-- Reaction buttons container-->
                                                                <li class="emoji emo-{{$react->name_en}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$post->id}},{{$react->id}})" data-reaction="{{$react->name_en}}"></li>
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
                                                <span class="like-emo" id="like-emo-{{$post->id}}">
                                                      <!-- like emotions container -->
                                                      <span class="like-btn-like"></span>
                                                        @if($post->user_react[0]->name_en != "like")
                                                        <span class="like-btn-{{$post->user_react[0]->name_en}}"></span>
                                                    @endif
                                                    </span>
                                    <span class="like-details" id="like-details-{{$post->id}}" data-toggle="modal" data-target="#likes-modal-{{$post->id}}">{{__('home.you')}} @if($post->likes->count-1 != 0) {{__('home.and')}} {{$post->likes->count-1}} @if($post->likes->count-1 > 1000) {{__('home.thousand')}} @endif {{__('home.others')}} @endif</span>
                                </div>
                                @if($post->likes->count > 0)
                                    <div class="likes-modal">
                                        <div class="modal fade" id="likes-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
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
                                                            <button onclick="filterPostLikes({{$post->id}},'all-{{$post->id}}')" class="btn btn-light active-{{$post->id}} filter-all-{{$post->id}} ez-active" id="{{$post->id}}" data-filter="all-{{$post->id}}">
                                                                {{__('home.all')}}
                                                            </button>
                                                            @foreach($post->reacts_stat as $react_stat)
                                                                @if(count($react_stat) > 0)
                                                                    <div class="btn btn-light active-{{$post->id}} filter-{{$react_stat[0]->react_name}}-{{$post->id}}" onclick='filterPostLikes({{$post->id}},"{{$react_stat[0]->react_name}}-{{$post->id}}")' id="{{$post->id}}" data-filter="{{$react_stat[0]->react_name}}-{{$post->id}}">
                                                                        <img src="{{asset('media')}}/{{$react_stat[0]->react_name}}.png"/>
                                                                        <span>{{count($react_stat)}}</span>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        <div class="likes-container mt-3">
                                                            @foreach($post->reacts_stat as $react_stat)
                                                                @if(count($react_stat) > 0)
                                                                    <div class="filter-{{$post->id}} {{$react_stat[0]->react_name}}-{{$post->id}}">
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
                            <div class="reaction-container" id="reaction-container-{{$post->id}}">
                                            <span class="reaction-btn">
                                                <span class="reaction-btn-emo like-btn-default" id="reaction-btn-emo-{{$post->id}}" style="display: none"></span>
                                                <span class="reaction-btn-text" id="reaction-btn-text-{{$post->id}}">
                                                    <div><i class="far fa-thumbs-up"></i>
                                                        @if($post->likes->count > 0)
                                                            <span data-toggle="modal" data-target="#likes-modal-{{$post->id}}">
                                                                {{$post->likes->count}}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </span>
                                                <ul class="emojies-box">
                                                    @foreach($reacts as $react)
                                                        <li class="emoji emo-{{$react->name_en}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$post->id}},{{$react->id}})" data-reaction="{{$react->name_en}}"></li>
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
                                                <span class="like-emo" id="like-emo-{{$post->id}}">
                                                  <span class="like-btn-like"></span>
                                                </span>
                                    <span class="like-details" id="like-details-{{$post->id}}" data-toggle="modal" data-target="#likes-modal-{{$post->id}}">@if($post->likes->count-1 > 0) {{__('home.and')}} {{$post->likes->count-1}} @if($post->likes->count-1 > 1000) {{__('home.thousand')}} @endif {{__('home.others')}} @endif</span>
                                </div>
                                @if($post->likes->count > 0)
                                    <div class="likes-modal">
                                        <div class="modal fade" id="likes-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
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
                                                            <button onclick="filterPostLikes({{$post->id}},'all-{{$post->id}}')" class="btn btn-light active-{{$post->id}} filter-all-{{$post->id}} ez-active" id="{{$post->id}}" data-filter="all-{{$post->id}}">
                                                                {{__('home.all')}}
                                                            </button>
                                                            @foreach($post->reacts_stat as $react_stat)
                                                                @if(count($react_stat) > 0)
                                                                    <div class="btn btn-light active-{{$post->id}} filter-{{$react_stat[0]->react_name}}-{{$post->id}}" onclick='filterPostLikes({{$post->id}},"{{$react_stat[0]->react_name}}-{{$post->id}}")' id="{{$post->id}}" data-filter="{{$react_stat[0]->react_name}}-{{$post->id}}">
                                                                        <img src="{{asset('media')}}/{{$react_stat[0]->react_name}}.png"/>
                                                                        <span>{{count($react_stat)}}</span>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        <div class="likes-container mt-3">
                                                            @foreach($post->reacts_stat as $react_stat)
                                                                @if(count($react_stat) > 0)
                                                                    <div class="filter-{{$post->id}} {{$react_stat[0]->react_name}}-{{$post->id}}">
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
                                    <h5 class="modal-title" id="exampleModalLabel">{{__('home.share')}}</h5>
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
                                        <input type="hidden" name="post_id" value="@if($post->type == "post"){{$post->id}}@else{{$post->shared_post->id}}@endif">
                                        <div class="post-privacy d-flex justify-content-between align-items-center m-auto w-75">
                                            <label for="cars">{{__('home.privacy')}}</label>
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
                                            <label for="cars">{{__('home.category')}}</label>
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
                                        <div class="post-desc d-flex justify-content-center mt-2">
                                                            <textarea class="w-75 p-2" name="body" id="textarea" cols="200" rows="4"
                                                                      placeholder="{{__('home.text_area')}}"></textarea>
                                            <div id="menu" class="menu" role="listbox"></div>
                                        </div>
                                        <!-- Add Post Btn -->
                                        <div class="post-add-btn d-flex justify-content-center mt-4">
                                            <button type="button" onclick="sharePostSubmit({{$post->id}},'{{App::getlocale()}}')" class="btn btn-warning btn-block w-75">
                                                {{__('home.share')}}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="shares">
                        <i data-toggle="modal" data-target="#share-post-modal-{{$post->id}}" class="fas fa-share ml-3"></i>
                        @if($post->shares > 0)
                            <span data-toggle="modal" data-target="#shares-modal-{{$post->id}}">
                                            {{$post->shares}}
                                        </span>
                        @endif
                    </div>
                </div>
                @if($post->publisher->id == auth()->user()->id)
                    <div class="show-story-views-modal">
                        <div class="modal fade" id="shares-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog" style="margin-top: 22vh">
                                <div class="modal-content">
                                    <div class="modal-header d-flex justify-content-between">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{__('home.users_shared')}}
                                        </h5>
                                        <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        @foreach($post->share_details as $share)
                                            <div class="people-info d-flex align-items-center">
                                                @if($share->publisher->personal_image != null)
                                                    <img class="rounded-circle" style="width: 50px;height: 50px"
                                                         src="{{asset('media')}}/{{$share->publisher->personal_image}}"
                                                         alt="User Profile Pic">
                                                @else
                                                    <img class="rounded-circle" style="width: 50px;height: 50px"
                                                         src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                                         alt="User Profile Pic">
                                                @endif
                                                <p class="mb-0 ml-3"><b>{{$share->publisher->name}}</b></p>
                                            </div>
                                            @if($loop->last == false)
                                                <hr>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="post-comment-list post-comment-list-{{$post->id}} mt-2">
                    <div class="hide-commnet-list d-flex flex-row-reverse">
                        <span onclick="toggleComments({{$post->id}})"><i class="fas fa-chevron-up"></i> {{__('home.hide')}}</span>
                    </div>
                    @if(count($post->comments) > 0)
                        @foreach($post->comments as $comment)
                            @if($comment->type == "comment" && $comment->reported == false)
                                <div class="comment d-flex justify-content-between" id="comment-{{$comment->id}}">
                                    <div class="comment-owner d-flex p-2 w-100">
                                        @if($comment->publisher->personal_image)
                                            <div class="owner-img">
                                                <a style="display: inline" href="{{route('profile',$comment->publisher->id)}}"><img src="{{asset('media')}}/{{$comment->publisher->personal_image}}" class="rounded-circle" /></a>
                                            </div>
                                        @else
                                            <div class="owner-img">
                                                <a style="display: inline" href="{{route('profile',$comment->publisher->id)}}"><img src="{{asset('media')}}/img.jpg" class="rounded-circle" /></a>
                                            </div>
                                        @endif
                                        <div class="owner-name pl-3 w-100">
                                            <a href="comment.userProfile" class="mb-0"><b>{{$comment->publisher->name}}</b></a>
                                            <p class="comment-content mb-0"><?php echo $comment->body ?></p>
                                            <!-- attatched img -->
                                            <!-- <img src="img.jpg" class="w-100 pt-3"> -->
                                            <!-- attatched vedio -->
                                            @if($comment->media != null)
                                                @if($comment->media->mediaType == 'image')
                                                    <img src="{{asset('media')}}/{{$comment->media->filename}}" style="height: 250px;width: auto" class="pt-3">
                                                @else
                                                    <video class="pt-3" controls>
                                                        <source src="{{asset('media')}}/{{$comment->media->filename}}" type="video/mp4">
                                                    </video>
                                                @endif
                                            @endif
                                            <ul class="comment-actions mt-1">
                                            @if($comment->liked)
                                                <!-- if post is liked by user -->
                                                    <!-- <div><i class="fas fa-thumbs-up"></i><span> 20</span></div> -->
                                                    <!-- if post isn't liked by user -->
                                                    <div class="reaction-container" id="reaction-container-{{$comment->id}}">
                                                        <!-- container div for reaction system -->
                                                        <span class="reaction-btn">
                                                                    <!-- Default like button -->
                                                                    <span class="reaction-btn-emo like-btn-{{$comment->user_react[0]->name_en}}" id="reaction-btn-emo-{{$comment->id}}"></span>
                                                            <!-- Default like button emotion-->
                                                                    <span class="reaction-btn-text reaction-btn-text-{{$comment->user_react[0]->name_en}} active" onclick="unlikeModelSubmit({{$comment->id}},{{$comment->user_react[0]->id}})" id="reaction-btn-text-{{$comment->id}}">
                                                                        @if(App::getLocale() == 'ar')
                                                                            {{$comment->user_react[0]->name_ar}}
                                                                        @else
                                                                            {{$comment->user_react[0]->name_en}}
                                                                        @endif
                                                                            <form id="unlike-form-{{$comment->id}}-{{$comment->user_react[0]->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                                                @csrf
                                                                                <input type="hidden" name="model_id" value="{{$comment->id}}">
                                                                                <input type="hidden" name="model_type" value="comment">
                                                                                <input type="hidden" name="reactId" value="{{$comment->user_react[0]->id}}">
                                                                               <input type="hidden" name="requestType" id="like-request-type-{{$comment->id}}" value="delete">
                                                                            </form>
                                                                    </span>
                                                            <!-- Default like button text,(Like, wow, sad..) default:Like  -->
                                                                    <ul class="emojies-box">
                                                                        @foreach($reacts as $react)
                                                                            <!-- Reaction buttons container-->
                                                                                <li class="emoji emo-{{$react->name_en}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$comment->id}},{{$react->id}})" data-reaction="{{$react->name_en}}"></li>
                                                                                <form id="like-form-{{$comment->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                                                @csrf
                                                                                <input type="hidden" name="model_id" value="{{$comment->id}}">
                                                                                <input type="hidden" name="model_type" value="comment">
                                                                                <input type="hidden" name="reactId" value="{{$react->id}}">
                                                                               <input type="hidden" name="requestType" id="like-request-type-{{$comment->id}}" value="update">
                                                                            </form>
                                                                            @endforeach
                                                                    </ul>
                                                                  </span>
                                                        <div class="like-stat">
                                                            <!-- Like statistic container-->
                                                            <span class="like-emo" id="like-emo-{{$comment->id}}">
                                                                          <!-- like emotions container -->
                                                                          <span class="like-btn-like"></span>
                                                                            @if($comment->user_react[0]->name_en != "like")
                                                                    <span class="like-btn-{{$comment->user_react[0]->name_en}}"></span>
                                                            @endif
                                                            <!-- given emotions like, wow, sad (default:Like) -->
                                                                        </span>
                                                            <span class="like-details" id="like-details-{{$comment->id}}" data-toggle="modal" data-target="#likes-modal-{{$comment->id}}">{{__('home.you')}} @if($comment->likes->count-1 != 0) {{__('home.and')}} {{$comment->likes->count-1}} @if($comment->likes->count-1 > 1000) {{__('home.thousand')}} @endif {{__('home.others')}} @endif</span>
                                                        </div>
                                                        @if($comment->likes->count > 0)
                                                            <div class="likes-modal">
                                                                <div class="modal fade" id="likes-modal-{{$comment->id}}" tabindex="-1" aria-hidden="true">
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
                                                                                    <button onclick="filterPostLikes({{$comment->id}},'all-{{$comment->id}}')" class="btn btn-light active-{{$post->id}} filter-all-{{$comment->id}} ez-active" id="{{$comment->id}}" data-filter="all-{{$comment->id}}">
                                                                                        {{__('home.all')}}
                                                                                    </button>
                                                                                    @foreach($comment->reacts_stat as $react_stat)
                                                                                        @if(count($react_stat) > 0)
                                                                                            <div class="btn btn-light active-{{$comment->id}} filter-{{$react_stat[0]->react_name}}-{{$comment->id}}" onclick='filterPostLikes({{$comment->id}},"{{$react_stat[0]->react_name}}-{{$comment->id}}")' id="{{$comment->id}}" data-filter="{{$react_stat[0]->react_name}}-{{$comment->id}}">
                                                                                                <img src="{{asset('media')}}/{{$react_stat[0]->react_name}}.png"/>
                                                                                                <span>{{count($react_stat)}}</span>
                                                                                            </div>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                                <div class="likes-container mt-3">
                                                                                    @foreach($comment->reacts_stat as $react_stat)
                                                                                        @if(count($react_stat) > 0)
                                                                                            <div class="filter-{{$post->id}} {{$react_stat[0]->react_name}}-{{$comment->id}}">
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
                                                    <div class="reaction-container" id="reaction-container-{{$comment->id}}">
                                                        <!-- container div for reaction system -->
                                                        <span class="reaction-btn">
                                                                        <span class="reaction-btn-emo like-btn-default" id="reaction-btn-emo-{{$comment->id}}" style="display: none"></span>
                                                            <!-- Default like button emotion-->
                                                                        <span class="reaction-btn-text" id="reaction-btn-text-{{$comment->id}}">
                                                                            <div><i class="far fa-thumbs-up"></i>
                                                                                @if($comment->likes->count > 0)
                                                                                    <span data-toggle="modal" data-target="#likes-modal-{{$comment->id}}">
                                                                                        {{$comment->likes->count}}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </span>
                                                            <!-- Default like button text,(Like, wow, sad..) default:Like  -->
                                                                        <ul class="emojies-box">
                                                                            @foreach($reacts as $react)
                                                                                <!-- Reaction buttons container-->
                                                                                    <li class="emoji emo-{{$react->name_en}}" id="react-{{$react->id}}" onclick="likeModelSubmit({{$comment->id}},{{$react->id}})" data-reaction="{{$react->name_en}}"></li>
                                                                                    <form id="like-form-{{$comment->id}}-{{$react->id}}" action="{{ route('likes.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                                                                    @csrf
                                                                                    <input type="hidden" name="model_id" value="{{$comment->id}}">
                                                                                    <input type="hidden" name="model_type" value="comment">
                                                                                    <input type="hidden" name="reactId" value="{{$react->id}}">
                                                                                </form>
                                                                                @endforeach
                                                                        </ul>
                                                                    </span>
                                                        <div class="like-stat" id="like-stat-{{$comment->id}}" style="display: none">
                                                            <!-- Like statistic container-->
                                                            <span class="like-emo" id="like-emo-{{$comment->id}}">
                                                                          <!-- like emotions container -->
                                                                          <span class="like-btn-like"></span>
                                                                <!-- given emotions like, wow, sad (default:Like) -->
                                                                        </span>
                                                            <span class="like-details" id="like-details-{{$comment->id}}" data-toggle="modal" data-target="#likes-modal-{{$comment->id}}">@if($comment->likes->count-1 > 0) {{__('home.and')}} {{$comment->likes->count-1}} @if($comment->likes->count-1 > 1000) {{__('home.thousand')}} @endif {{__('home.others')}} @endif</span>
                                                        </div>
                                                        @if($comment->likes->count > 0)
                                                            <div class="likes-modal">
                                                                <div class="modal fade" id="likes-modal-{{$comment->id}}" tabindex="-1" aria-hidden="true">
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
                                                                                    <button onclick="filterPostLikes({{$comment->id}},'all-{{$comment->id}}')" class="btn btn-light active-{{$post->id}} filter-all-{{$comment->id}} ez-active" id="{{$comment->id}}" data-filter="all-{{$comment->id}}">
                                                                                        {{__('home.all')}}
                                                                                    </button>
                                                                                    @foreach($comment->reacts_stat as $react_stat)
                                                                                        @if(count($react_stat) > 0)
                                                                                            <div class="btn btn-light active-{{$comment->id}} filter-{{$react_stat[0]->react_name}}-{{$comment->id}}" onclick='filterPostLikes({{$comment->id}},"{{$react_stat[0]->react_name}}-{{$comment->id}}")' id="{{$comment->id}}" data-filter="{{$react_stat[0]->react_name}}-{{$comment->id}}">
                                                                                                <img src="{{asset('media')}}/{{$react_stat[0]->react_name}}.png"/>
                                                                                                <span>{{count($react_stat)}}</span>
                                                                                            </div>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                                <div class="likes-container mt-3">
                                                                                    @foreach($comment->reacts_stat as $react_stat)
                                                                                        @if(count($react_stat) > 0)
                                                                                            <div class="filter-{{$post->id}} {{$react_stat[0]->react_name}}-{{$comment->id}}">
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
                                                <li class="ml-3 text-primary" onclick="toggleReply({{$comment->id}},'{{$comment->publisher->user_name}}')">{{__('home.reply')}}</li>
                                            </ul>
                                            @if($comment->replies)
                                                <div class="replays comment-1-replays" id="comment-replies-{{$comment->id}}" style="display: none">
                                                    <div class="mt-2">
                                                        @foreach($comment->replies as $reply)
                                                            @if($reply->reported == false)
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
                                                                                <li onclick="confirm('{{ __("home.confirm") }}') ? deleteCommentSubmit({{$reply->id}},{{$post->id}}) : ''" >{{__('user.delete')}}</li>
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
                                                            @endif
                                                        @endforeach
                                                        <div id="added-reply-{{$comment->id}}">

                                                        </div>
                                                    </div>
                                                    <button type="button" id="reply-submit-btn-{{$comment->id}}" onclick="event.preventDefault();
                                                        addReplySubmit({{$comment->id}},{{$post->id}})" hidden></button>
                                                    <div style="display: none" id="add-reply-div-{{$comment->id}}">
                                                        <form class="add-commnet mt-2 d-flex align-items-center" id="add-reply-form-{{$comment->id}}" onkeypress="if (event.keyCode === 13) { event.preventDefault(); $('#reply-submit-btn-{{$comment->id}}').click();}" action="{{route('comments.store')}}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="post_id" value="{{$post->id}}" />
                                                            <input type="hidden" name="comment_id" value="{{$comment->id}}" />
                                                            <input onfocus="mentionAdd('reply-text-{{$comment->id}}','menu-{{$comment->id}}')" id="reply-text-{{$comment->id}}" class="w-100 pl-2" type="text" name="body" placeholder="{{__('home.text_area')}}" />
                                                            <div id="menu-{{$comment->id}}" class="menu" role="listbox"></div>
                                                            <div class="d-flex align-items-center pr-3">
                                                                <i class="fas fa-paperclip" onclick="commentAttachClick({{$comment->id}})"></i>
                                                                <input type="file" id="comment-attach-{{$comment->id}}" onchange="readURL({{$comment->id}},this,'reply');" name="media" accept=".jpg,.jpeg,.png,.svg,.gif" />
                                                            </div>
                                                        </form>
                                                        <div id="img-div-reply-{{$comment->id}}" style="display: none;">
                                                            <img id="img-reply-{{$comment->id}}" src="#" alt="your image" style="margin: 10px" />
                                                            <button class="btn btn-warning text-white" onclick="$('#img-div-reply-{{$comment->id}}').css('display','none')">{{__('home.remove_image')}}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="comment-options comment-options-{{$comment->id}}">
                                        <ul class="options">
                                            <li data-toggle="modal" data-target="#report-comment-modal-{{$comment->id}}">
                                                {{__('home.report')}}</li>
                                            @if($comment->publisher->id == auth()->user()->id)
                                                <li data-toggle="modal" data-target="#edit-comment-modal-{{$comment->id}}">{{__('user.edit')}}</li>
                                                <li onclick="confirm('{{ __("home.confirm") }}') ? deleteCommentSubmit({{$comment->id}},{{$post->id}}) : ''" >{{__('user.delete')}}</li>
                                                <form action="{{ route('comments.destroy', $comment->id) }}" id="delete-comment-form-{{$comment->id}}" method="POST">
                                                @csrf
                                                @method('delete')
                                                <!-- ajax-->
                                                </form>
                                            @endif

                                            <div class="post-edit-modal">
                                                <div class="modal fade" id="edit-comment-modal-{{$comment->id}}" tabindex="-1" aria-hidden="true">
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
                                                                <div class="col-12" id="error-message-{{$comment->id}}" style="display: none">
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
                                                                                      <textarea onfocus="mentionAdd('text-edit-{{$comment->id}}','menu-edit-comment-{{$comment->id}}')" id="text-edit-{{$comment->id}}" class="w-75" name="body" cols="200" rows="4"
                                                                                                placeholder="{{__('home.text_area')}}" >@if($comment->mentions != null){{$comment->edit}}@else{{$comment->body}}@endif</textarea>
                                                                        <div id="menu-edit-comment-{{$comment->id}}" class="menu" role="listbox"></div>
                                                                    </div>

                                                                    <input type="hidden" name="model_id" value="{{$post->id}}">

                                                                    <div class="post-desc d-flex justify-content-center mt-2">
                                                                        <input class="form-control w-75 mt-2" type="file" name="media" id="imgs"/>
                                                                    </div>
                                                                    <!-- Add Post Btn -->
                                                                    <div class="post-add-btn d-flex justify-content-center mt-4">
                                                                        <button type="button" onclick="editCommentSubmit({{$comment->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                                                            {{__('home.save')}}
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
                                                                <h5 class="modal-title" id="exampleModalLabel">{{__('home.report')}}</h5>
                                                                <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="col-12" id="error-message-{{$comment->id}}" style="display: none">
                                                                    <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$comment->id}}" role="alert">
                                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <form action="{{route('userreports.store')}}" id="report-comment-form-{{$comment->id}}" method="POST" class="container" enctype="multipart/form-data">

                                                                @csrf

                                                                <!-- Post Desc -->
                                                                    <div class="post-desc d-flex justify-content-center mt-2">
                                                                                      <textarea class="w-75" name="body" id="post-text" cols="200" rows="4"
                                                                                                placeholder="{{__('home.text_area')}}" ></textarea>
                                                                    </div>
                                                                    <!-- Add Post Btn -->
                                                                    <input type="hidden" name="model_id" value="{{$comment->id}}">
                                                                    <input type="hidden" name="model_type" value="comment">

                                                                    <div class="post-add-btn d-flex justify-content-center mt-4">
                                                                        <button type="button" onclick="reportCommentSubmit({{$comment->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
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
                                        <i class="fas fa-ellipsis-v" onclick="toggleCommentOptions({{$comment->id}})"></i>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                    <div id="load-comments-{{$post->id}}" data-value="5">

                    </div>
                    <div id="added-comment-{{$post->id}}">

                    </div>
                    @if(isset($another_comments) && $post->comments_count > 5)
                        <p id="load-comments-message-{{$post->id}}" style="cursor: pointer" onclick="loadComments({{$post->id}})">{{__('home.load_more_comments')}}</p>
                    @endif
                </div>
                <button type="button" id="comment-submit-btn-{{$post->id}}" onclick="event.preventDefault();
                    addCommentSubmit({{$post->id}})" hidden></button>
                <form class="add-commnet mt-2 d-flex align-items-center" onkeypress="if (event.keyCode === 13) { event.preventDefault(); $('#comment-submit-btn-{{$post->id}}').click();}" id="add-comment-form-{{$post->id}}" action="{{route('comments.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="post_id" value="{{$post->id}}" />
                    <input onfocus="mentionAdd('reply-text-{{$post->id}}','menu-{{$post->id}}')" id="reply-text-{{$post->id}}" class="w-100 pl-2" type="text" name="body" placeholder="{{__('home.text_area')}}" />
                    <div id="menu-{{$post->id}}" class="menu" role="listbox"></div>
                    <div class="d-flex align-items-center pr-3">
                        <i class="fas fa-paperclip" onclick="commentAttachClick({{$post->id}})"></i>
                        <input type="file" id="comment-attach-{{$post->id}}" onchange="readURL({{$post->id}},this,'comment');" name="media" accept=".jpg,.jpeg,.png,.svg,.gif" />
                    </div>
                </form>
                <div id="img-div-comment-{{$post->id}}" style="display: none;">
                    <img id="img-comment-{{$post->id}}" src="#" alt="your image" style="margin: 10px" />
                    <button class="btn btn-warning text-white" onclick="$('#img-div-comment-{{$post->id}}').css('display','none')">{{__('home.remove_image')}}</button>
                </div>
                <div class="post-advertise-modal">
                    <div class="modal fade" id="advertise-post-modal-{{$post->id}}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin-top: 10vh">
                            <div class="modal-content">
                                <div class="modal-header d-flex justify-content-between">
                                    <span></span>
                                    <h5 class="modal-title" id="exampleModalLabel">
                                        {{__('home.advertise')}}
                                    </h5>
                                    <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body pl-5 pr-5">
                                    <form action="{{route('sponsor')}}" id="sponsor-post-form-{{$post->id}}" class="container" method="POST">
                                        @csrf
                                        <p>{{__('home.select_duration')}}</p>
                                        @foreach($times as $time)
                                            <div class="form-group form-check mb-1">
                                                <input id="time-{{$post->id}}" data-value="{{$time->price}}" type="radio" name="timeId" onclick="getPrice({{$post->id}})" value="{{$time->duration}}" class="form-check-input"/>
                                                <label class="form-check-label" for="exampleCheck1">{{$time->duration}} days</label>
                                            </div>
                                            <hr class="m-1">
                                        @endforeach

                                        <p>{{__('home.select_reach')}}</p>
                                        @foreach($reaches as $reach)
                                            <div class="form-group form-check mb-1">
                                                <input id="reach-{{$post->id}}" type="radio" data-value="{{$reach->price}}" onclick="getPrice({{$post->id}})" name="reachId" value="{{$reach->id}}" class="form-check-input"/>
                                                <label class="form-check-label" for="exampleCheck1">{{$reach->reach}} persons</label>
                                            </div>
                                            <hr class="m-1">
                                        @endforeach
                                        <div class="form-group d-flex justify-content-between">
                                            <label for="exampleInputEmail1">{{__('home.target_audience')}}</label>
                                            <select name="gender">
                                                <option value="male">{{__('home.male')}}</option>
                                                <option value="female">{{__('home.female')}}</option>
                                            </select>
                                        </div>
                                        <div class="form-group d-flex justify-content-between">
                                            <label for="exampleInputEmail1">{{__('home.target_age')}}</label>
                                            <select name="age_id">
                                                @foreach($ages as $age)
                                                    <option value="{{$age->id}}">From {{$age->from}} To {{$age->to}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                            <label for="cars">{{__('home.category')}}</label>
                                            <select class="js-example-basic-single" name="category_id">
                                                @foreach($categories as $category)
                                                    @if(App::getlocale() == 'en')
                                                        <option value="{{$category->id}}">{{$category->name_en}}</option>
                                                    @else
                                                        <option value="{{$category->id}}">{{$category->name_ar}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                            <label for="exampleInputEmail1">{{__('user.country')}}</label>
                                            <select onchange="addServiceCities(this)" name="country_id" style="width: 200px" class="js-example-basic-single">
                                                <option value="0">choose target country</option>
                                                @foreach($countries as $country)
                                                    <option value="{{$country->id}}">{{$country->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                            <label for="exampleInputEmail1">{{__('user.city')}}</label>
                                            <select name="city_id" style="width: 200px" class="select-city js-example-basic-single" disabled>
                                                @foreach($cities as $city)
                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Select Service Price -->
                                        <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                            <input name="price" id="sponsored-post-price-{{$post->id}}" class="w-100 border" type="number" placeholder="{{__('home.price')}}" readonly/>
                                        </div>
                                        <input type="hidden" name="postId" value="{{$post->id}}">
                                        <button onclick="sponsorPost({{$post->id}})" class="btn btn-warning btn-block" data-dismiss="modal">
                                            {{__('home.sponsor')}}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal" id="payment-modal-{{$post->id}}" tabindex="-1" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog" style="margin-top: 22vh">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between">
                                <span></span>
                                <h5 class="modal-title" id="exampleModalLabel">Payment Modal</h5>
                                <button type="button" id="success-modal-dismiss" class="close ml-0" data-dismiss="modal" aria-label="Close">
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
                                let's pay
                                <form action="{{route('sponsor.payment')}}" id="sponsor-payment-form-{{$post->id}}" method="POST" class="container" enctype="multipart/form-data">

                                @csrf
                                <!-- Post Desc -->
                                    <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                        <input id="payment-text-box-{{$post->id}}" class="w-100 border" type="number" placeholder="Price $" readonly/>
                                    </div>

                                    <!-- Add Post Btn -->
                                    <div class="post-add-btn d-flex justify-content-center mt-4">
                                        <button type="button" onclick="sponsorPaymentSubmit({{$post->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                            Pay Now
                                        </button>
                                    </div>
                                </form>
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
                                    <h5 class="modal-title" id="exampleModalLabel">{{__('user.edit')}}</h5>
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



                                        @if($post->type == "share")
                                            <input type="hidden" value="{{$post->post_id}}" name="post_id">
                                        @endif

                                    <!-- Select post Privacy -->
                                        <div class="post-privacy d-flex justify-content-between align-items-center m-auto w-75">
                                            <label for="cars">{{__('home.privacy')}}</label>
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
                                        <!-- Select Service Category -->
                                        <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                            <label for="cars">{{__('home.category')}}</label>
                                            <select class="js-example-basic-single" name="category_id">
                                                @foreach($categories as $category)
                                                    @if(App::getlocale() == 'en')
                                                        <option value="{{$category->id}}" @if($category->id == $post->categoryId) selected @endif>{{$category->name_en}}</option>
                                                    @else
                                                        <option value="{{$category->id}}" @if($category->id == $post->categoryId) selected @endif>{{$category->name_ar}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                            <label>{{__('home.tag_friends')}}</label>
                                            <select style="width: 200px" class="js-example-basic-multiple" name="tags[]" multiple="multiple" data-placeholder="c{{__('home.choose_friends')}}">
                                                @foreach($friends_info as $friend)
                                                    <option value="{{$friend->id}}" @if($post->tags != null) @if(in_array($friend->id,$post->tags_ids)) selected @endif @endif>{{$friend->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Post Desc -->
                                        <div class="post-desc d-flex justify-content-center mt-2">
                                                      <textarea onfocus="mentionAdd('textarea-edit-{{$post->id}}','menu-edit-{{$post->id}}')" class="w-75" name="body" id="textarea-edit-{{$post->id}}" cols="200" rows="4" placeholder="{{__('home.text_area')}}"
                                                      >@if($post->mentions != null) {{$post->edit}}@else{{$post->body}}@endif</textarea>
                                            <div id="menu-edit-{{$post->id}}" class="menu" role="listbox"></div>
                                        </div

                                            @if($post->type == "post")
                                            <!-- Post Images -->
                                        <div class="post-desc d-flex justify-content-center mt-2">
                                            <input class="form-control w-75 mt-2" type="file" accept=".mpeg,.ogg,.mp4,.webm,.3gp,.mov,.flv,.avi,.wmv,.ts,.jpg,.jpeg,.png,.svg,.gif" name="media[]" id="imgs"
                                                   multiple />
                                        </div>

                                        @if(count($post->media) > 0)
                                            <p>{{__('home.media')}}</p>
                                            <div class="imgsContainer d-flex flex-wrap">
                                            @foreach($post->media as $media)
                                                @if($media->mediaType == 'image')
                                                    <!-- if media img and imgs=1 -->
                                                        <div class="p-3" style="width: 33%;">
                                                            <img src="{{asset('media')}}/{{$media->filename}}" alt="" width="100%">
                                                            <div class="w-100 text-center">
                                                                <input checked type="checkbox" value="{{$media->filename}}" name="checkedimages[]">
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="p-3" style="width: 33%;">
                                                            <video class="p-1" controls width="100%">
                                                                <source src="{{asset('media')}}/{{$media->filename}}" type="video/mp4">
                                                                {{__('home.no_browser')}}
                                                            </video>
                                                            <div class="w-100 text-center">
                                                                <input checked type="checkbox" value="{{$media->filename}}" name="checkedimages[]">
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                    @endif

                                    @endif
                                    <!-- Add Post Btn -->
                                        <div class="post-add-btn d-flex justify-content-center mt-4">
                                            <button type="button" onclick="editPostSubmit({{$post->id}},'{{App::getlocale()}}')" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                                {{__('home.save')}}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="ez-body__right-sidebar" class="col-lg-2 ez-sidebar">
        <ul class="pt-4" id="right-sidebar__items">
            @if(count($expected_groups) > 0)
                <li class="mt-3">
                    <h6 class="pb-2" style="font-weight: bold;font-size: 15px">{{__('home.expected_groups')}}</h6>
                    <div class="suggested-groups">
                        @foreach($expected_groups as $group)
                            <div class="group">
                                <a href="{{route('main-group',$group->id)}}">
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
                                            <p id="members-{{$group->id}}">{{$group->members}} {{__('groups.members')}}</p>
                                        </div>
                                        <a id="join-btn-{{$group->id}}" onclick="joinGroupSubmit({{$group->id}},'{{App::getlocale()}}')" class="btn btn-warning text-white">{{__('groups.join')}}</a>
                                        <form id="join-group-form-{{$group->id}}" action="{{ route('join_group') }}" method="POST" style="display: none;">
                                            @csrf
                                            <input type="hidden" name="group_id" value="{{$group->id}}">
                                            <input type="hidden" id="join-flag-{{$group->id}}" name="flag" value="0">
                                        </form>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </li>
            @endif
            @if(count($expected_pages) > 0)
                <li class="mt-3">
                        <h6 class="pb-2" style="font-weight: bold;font-size: 15px">{{__('home.expected_pages')}}</h6>
                        <div class="suggested-groups">
                            @foreach($expected_pages as $page)
                                <div class="group">
                                    <a href="{{route('main-page',$page->id)}}">
                                        <div class="group-banner">
                                            @if($page->cover_image)
                                                <img
                                                    width="100%"
                                                    src="{{asset('media')}}/{{$page->profile_image}}"
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
                                                <p id="page-members-{{$page->id}}">{{$page->members}} {{__('home.likes')}}</p>
                                            </div>
                                            <a id="like-page-btn-{{$page->id}}" onclick="likePageSubmit({{$page->id}},'{{App::getlocale()}}')" class="btn btn-warning text-white">{{__('pages.like')}}</a>
                                            <form id="like-page-form-{{$page->id}}" action="{{ route('like_page') }}" method="POST" style="display: none;">
                                                @csrf
                                                <input type="hidden" name="page_id" value="{{$page->id}}">
                                                <input type="hidden" id="like-page-flag-{{$page->id}}" name="flag" value="0">
                                            </form>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </li>
            @endif
        </ul>
    </section>
@endsection

