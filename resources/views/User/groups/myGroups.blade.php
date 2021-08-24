@extends('layouts.app')

@section('content')

<section id="ez-body__center-content" class="col-lg-8 mt-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="search-bar row" style="margin: 5px">
                <input type="text" onkeyup="homeSearchSubmit()" id="home-search" placeholder="Search" />
            </div>
            <section class="group-section px-3">
              <div class="group-mygroups py-3">
                <div class="row text-center ">
                    @if(Auth::guard('web')->user())
                  <a  class="button-4 col-5 mx-auto tablinks" href="/my-group">{{__('groups.my_groups')}}</a>
                  @else
                  <a  class="button-4 col-5 mx-auto tablinks" href="/login">{{__('groups.my_groups')}}</a>
                  @endif
                  <a class="button-4 col-5  mx-auto tablinks" href="/all-group">{{__('groups.all_groups')}}</a>

                </div><br>


                @if(Auth::guard('web')->user())

                <div id="my-groups" class=" tabcontent" >
                    <div  class="row ">
                        @foreach($my_groups as $my_group)
                          <div class="col-lg-4" id="{{$my_group->group->id}}|0">
                              <div class="card">
                                  <a href="{{route('main-group',$all_group->id)}}">
                                      <img src="{{asset('assets/images/groups/profile')}}/{{$my_group->group->profile_image}}" class="card-img-top" width="200" height="200" alt="...">
                                  </a>
                                  <div class="d-flex justify-content-between">
                                      <div class="card-body">
                                          <a href="{{route('main-group',$all_group->id)}}" style="color:black !important">
                                              <h3 class="card-title">{{$my_group->name}}</h3>
                                          </a>
                                          <p class="card-text"><small class="text-muted" id="{{$my_group->group->id}}">
                                              <?php
                                                  $member = App\models\GroupMember::where('group_id',$my_group->group->id)->count();
                                                  echo $member;
                                              ?>
                                              {{__('groups.member')}}</small>
                                          </p>
                                      </div>
                                      <div class="p-2">
                                          @if(Auth::guard('web')->user())
                                              <?php
                                                  $checkState = App\models\GroupMember::where('group_id',$my_group->group->id)->where('user_id',auth::user()->id)->get();
                                              ?>
                                              @if (count($checkState)==0)
                                              <div class="p-2">
                                                      <button class="button-4 totyAllgroups" id="join|{{$my_group->group->id}}" >{{__('groups.join')}} </button>
                                              </div>

                                              @elseif (count($checkState)>0)
                                                  @if ($checkState[0]->state == 1)
                                                      <div class="p-2">
                                                              <button class="button-2 totyAllgroups" id="leave|{{$my_group->group->id}}">{{__('groups.left')}}</button>
                                                      </div>

                                                  @elseif ($checkState[0]->state == 2)
                                                      <div class="p-2">
                                                          <button class="button-2 totyAllgroups" id="leave|{{$my_group->group->id}}|0">{{__('groups.left_request')}}</button>
                                                      </div>

                                                  @elseif ($checkState[0]->isAdmin == 1)
                                                      <div class="p-2">
                                                          <button class="button-2">{{__('groups.admin')}}</button>
                                                      </div>
                                                  @endif
                                              @endif
                                          @else
                                              <form action="/login" method="post">
                                                  @csrf
                                                  <button class="button-4">{{__('groups.join')}}</button>
                                              </form>
                                          @endif
                                      </div>
                                  </div>
                              </div>

                          </div>

                          @endforeach
                    </div>
                </div>

                @endif

              </div>
            </section>
      </div>
    </div>
</section>
<section id="ez-body__right-sidebar" class="col-lg-2 ez-sidebar">
    <ul class="pt-4" id="right-sidebar__items">
        @if(count($expected_groups) > 0)
            <li class="mt-3">
                <h6 class="pb-2" style="font-weight: bold;font-size: 15px">Groups You May Like</h6>
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
                                        <p>{{$group->members}} members</p>
                                    </div>
                                    <a id="join-btn-{{$group->id}}" onclick="joinGroupSubmit({{$group->id}})" class="btn btn-warning text-white">Join</a>
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
    </ul>
</section>

@endsection

