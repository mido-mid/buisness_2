@extends('layouts.app')

@section('content')

<section id="ez-body__center-content" class="col-lg-8 mt-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="search-bar row" style="margin: 5px">
                <input type="text" onkeyup="mysearchtoty()" id="search" placeholder="Search" />
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
                        @if(count($my_groups) > 0)
                            @foreach($my_groups as $my_group)
                          <div class="col-lg-4" id="{{$my_group->group->id}}|0">
                              <div class="card">
                                  <a href="{{route('main-group',$my_group->group_id)}}">
                                      <img src="{{asset('media')}}/{{$my_group->group->profile_image}}" class="card-img-top" width="200" height="200" alt="...">
                                  </a>
                                  <div class="d-flex justify-content-between">
                                      <div class="card-body">
                                          <a href="{{route('main-group',$my_group->group_id)}}" style="color:black !important">
                                              <h3 class="card-title">{{$my_group->group->name}}</h3>
                                          </a>
                                          <p class="card-text"><small class="text-muted" id="{{$my_group->group->id}}">
                                              <?php
                                                $member = App\models\GroupMember::where('group_id',$my_group->group->id)->where('state',1)->where('isAdmin','!=', 1)->count();
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
                                                      <button class="button-4 totyMygroups" id="join|{{$my_group->id}}|0" >{{__('groups.join')}} </button>
                                              </div>

                                              @elseif (count($checkState)>0)
                                                  @if ($checkState[0]->state == 1 && $checkState[0]->isAdmin != 1)
                                                      <div class="p-2">
                                                              <button class="button-2 totyMygroups" id="leave|{{$my_group->group->id}}|0">{{__('groups.left')}}</button>
                                                      </div>

                                                  @elseif ($checkState[0]->state == 2)
                                                      <div class="p-2">
                                                          <button class="button-2 totyMygroups" id="leave|{{$my_group->group->id}}|0">{{__('groups.left_request')}}</button>
                                                      </div>

                                                  @elseif ($checkState[0]->state == 1 && $checkState[0]->isAdmin == 1)
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
                        @else
                            <div class="col-md-8" id="no-service">
                                <div class="card">
                                    <div class="card-body">
                                        {{ __('no groups found!') }}
                                    </div>
                                </div>
                            </div>
                        @endif
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
                    <div class="card">
                        <a href="{{route('main-group',$group->id)}}">
                            <img src="{{asset('media')}}/{{$group->profile_image}}" class="card-img-top" alt="...">
                        </a>
                        <div class="d-flex justify-content-between">
                            <div class="card-body">
                                <a href="{{route('main-group',$group->id)}}" style="color:black !important">
                                    <h3 class="card-title">{{$group->name}}</h3>
                                </a>
                                <p class="card-text"><small class="text-muted" id="{{$group->id}}|1">
                                    <?php
                                        $member = App\models\GroupMember::where('group_id',$group->id)->where('state',1)->count();
                                        echo $member;
                                    ?>
                                    </small>
                                    {{__('groups.member')}}
                                </p>
                            </div>
                            <div class="p-2">
                                @if(Auth::guard('web')->user())
                                    <?php
                                        $checkState = App\models\GroupMember::where('group_id',$group->id)->where('user_id',auth::user()->id)->get();
                                    ?>
                                    @if (count($checkState)==0)
                                    <div class="p-2">
                                            <button class="button-4 toty" id="join|{{$group->id}}|1" >{{__('groups.join')}} </button>
                                    </div>

                                    @elseif (count($checkState)>0)
                                        @if ($checkState[0]->state == 1)
                                            <div class="p-2">
                                                    <button class="button-2 toty" id="leave|{{$group->id}}|1">{{__('groups.left')}}</button>
                                            </div>

                                        @elseif ($checkState[0]->state == 2)
                                            <div class="p-2">
                                                <button class="button-2 toty" id="leave|{{$group->id}}|1">{{__('groups.left_request')}}</button>
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
                    @endforeach
                </div>
            </li>
        @endif
    </ul>
</section>

@endsection

