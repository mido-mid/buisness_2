@extends('layouts.app')

@section('content')

<section id="ez-body__center-content" class="col-lg-8 mt-3">
    <div class="row">
        <div class="col-lg-12">
            <input type="text" class="search-box" placeholder="{{__('pages.search')}}">
            <section class="group-section px-3">
              <div class="group-mygroups py-3">
                <div class="row text-center ">
                    @if(Auth::guard('web')->user())
                  <a  class="button-4 col-5 mx-auto tablinks" href="/my-page">{{__('pages.my_pages')}}</a>
                  @else
                  <a  class="button-4 col-5 mx-auto tablinks" href="/login">{{__('pages.my_pages')}}</a>
                  @endif
                  <a class="button-4 col-5  mx-auto tablinks" href="/all-page">{{__('pages.all_pages')}}</a>

                </div><br>

                <div id="all-pages" class=" tabcontent" >
                  <div  class="row ">
                      @foreach($all_pages as $all_page)
                        <div class="col-lg-4">
                            <div class="card">
                                <a href="{{route('main-page',$all_page->id)}}">
                                    @if($all_page->profile_image)
                                        <img src="{{asset('media')}}/{{$all_page->profile_image}}" class="card-img-top" width="200" height="200" alt="...">
                                    @else
                                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80" class="card-img-top" width="200" height="200" alt="...">
                                    @endif
                                </a>
                                <div class="d-flex justify-content-between">
                                    <div class="card-body">
                                        <a href="{{route('main-page',$all_page->id)}}" style="color:black !important">
                                            <h3 class="card-title">{{$all_page->name}}</h3>
                                        </a>
                                        <p class="card-text"><small class="text-muted" id="{{$all_page->id}}">
                                            <?php
                                                $member = App\models\PageMember::where('page_id',$all_page->id)->count();
                                                echo $member;
                                            ?>
                                            {{__('pages.member')}}</small>
                                        </p>
                                    </div>
                                    <div class="p-2">
                                        @if(Auth::guard('web')->user())
                                            <?php
                                                $checkState = App\models\PageMember::where('page_id',$all_page->id)->where('user_id',auth::user()->id)->get();
                                            ?>
                                            @if (count($checkState)==0)
                                            <div class="p-2">
                                                    <button class="button-4 totyAllpages" id="join|{{$all_page->id}}" >{{__('pages.like')}} </button>
                                            </div>

                                            @elseif (count($checkState)>0)
                                                @if ($checkState[0]->state == 1)
                                                    <div class="p-2">
                                                            <button class="button-2 totyAllpages" id="leave|{{$all_page->id}}">{{__('pages.dislike')}}</button>
                                                    </div>

                                                @elseif ($checkState[0]->state == 2)
                                                    <div class="p-2">
                                                        <button class="button-2 totyAllpages" id="leave|{{$all_page->id}}">{{__('pages.dislike_request')}}</button>
                                                    </div>

                                                @elseif ($checkState[0]->isAdmin == 1)
                                                    <div class="p-2">
                                                        <button class="button-2">{{__('pages.admin')}}</button>
                                                    </div>
                                                @endif
                                            @endif
                                        @else
                                            <form action="/login" method="post">
                                                @csrf
                                                <button class="button-4">{{__('pages.like')}}</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                        @endforeach

                        <div class="w-100">
                            {{ $all_pages->links() }}
                        </div>

                  </div>
                </div>


              </div>
            </section>
      </div>
        {{-- @include('User.pages.related') --}}
    </div>
</section>

<section id="ez-body__right-sidebar" class="col-lg-2 ez-sidebar">
    <ul class="pt-4" id="right-sidebar__items">
        @if(count($expected_pages) > 0)
            <li class="mt-3">
                <h6 class="pb-2" style="font-weight: bold;font-size: 15px">Pages You May Like</h6>
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
                                        <p>{{$page->members}} likes</p>
                                    </div>
                                    <a id="like-page-btn-{{$page->id}}" onclick="likePageSubmit({{$page->id}})" class="btn btn-warning text-white">Like</a>
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

