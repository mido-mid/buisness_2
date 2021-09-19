@extends('layouts.app')

@section('content')
<section id="ez-body__center-content" class="col-lg-8 mt-3">

<div class="row"> 
  <div class="col-lg-12">
      <section class="group-section px-3">
        <div class="group-new-group">
          <h2 class="heading-secondary">{{__('groups.create')}}</h2>
          <form method="post" action="{{ route('groups.store') }}" enctype="multipart/form-data">
              @csrf
            <input class="form-control form-control-lg my-5" type="text" placeholder="{{__('groups.name_group')}}" name="name" required>
            <select class="form-control form-control-lg my-5" name="privacy" required>
              <option value="" disabled selected>{{__('groups.pivacy_add')}}</option>
              <option value="1">{{__('groups.public')}}</option>
              <option value="2">{{__('groups.private')}}</option>
            </select>
            <select class="form-control form-control-lg my-5" name="category_id" required>
              <option value="" disabled selected>{{__('groups.category')}}</option>
              @foreach($categroys as $categroy)
              <option value="{{$categroy->id}}">{{$categroy->name_ar}}</option>
              @endforeach
              
            </select>
            <div class="uimage-div my-5 text-center">
              <canvas id= "canv1" style="width:50%;height:150px;border: none"></canvas><br>
              <label class="select-image-label" for="finput" >{{__('groups.upload_profile')}}</label><br>
              <input class="select-image-input" type="file" dir="rtl" multiple="false" name="profile_image" accept="image/*" id=finput onchange="uploadimg()">

            </div>
            <div class="uimage-div my-5 text-center" >
              <canvas id= "canv2" style="width: 100%;height: 150px;border: none"></canvas><br>
              <label class="select-image-label" for="finput2" >{{__('groups.upload_cover')}}</label><br>
              <input class="select-image-input" type="file" multiple="false" accept="image/*" name="cover_image" id=finput2 onchange="uploadcover()"  >
            </div>
            <textarea dir="rtl" class="form-control form-control-lg my-5" placeholder="{{__('groups.describe')}}" name="description" required></textarea>
            <textarea dir="rtl" class="form-control form-control-lg my-5" placeholder="{{__('groups.rules')}}" name="rules" required></textarea>

            @if(Auth::guard('web')->user())
            <button type="submit" class="button-4 mt-5">{{__('groups.addGroup')}}</button>
            @else
            <a href="/login" class="button-4 mt-5">{{__('groups.addGroup')}}</a>
            @endif
          </form>
        </div>
      </section>
</div>
  {{-- @include('User.groups.related') --}}
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
                      <a href="/groups/{{$group->id}}">
                          <img src="{{asset('media')}}/{{$group->profile_image}}" class="card-img-top" alt="...">
                      </a>
                      <div class="d-flex justify-content-between">
                          <div class="card-body">
                              <a href="groups/{{$group->id}}" style="color:black !important">
                                  <h3 class="card-title">{{$group->name}}</h3>
                              </a>
                              <p class="card-text"><small class="text-muted" id="{{$group->id}}|1">
                                  <?php
                                      $member = App\models\GroupMember::where('group_id',$group->id)->where('state',1)->where('isAdmin','!=', 1)->count();
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
                                      @if ($checkState[0]->state == 1 && $checkState[0]->isAdmin != 1)
                                          <div class="p-2">
                                                  <button class="button-2 toty" id="leave|{{$group->id}}|1">{{__('groups.left')}}</button>
                                          </div>
      
                                      @elseif ($checkState[0]->state == 2)
                                          <div class="p-2">
                                              <button class="button-2 toty" id="leave|{{$group->id}}|1">{{__('groups.left_request')}}</button>
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
                  @endforeach
              </div>
          </li>
      @endif
  </ul>
</section>

@endsection

