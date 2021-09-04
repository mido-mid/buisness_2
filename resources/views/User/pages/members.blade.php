@extends('User.pages.layout')
 
@section('sectionPages')
@if($page['privacy'] == 0 && $isAdmin == 0 && $myState == 0)
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">{{__('pages.privacy')}}</h3>
    </div>
</div> 
@else
<div class="page-members my-3">
    <div class="invite-friends d-flex justify-content-between">
      <h3 class="heading-tertiary">{{__('pages.invite_friends')}}</h3>
      @if(Auth::guard('web')->user())
      <button class="button-4"  data-toggle="modal" data-target="#friends">{{__('pages.invite')}}</button>
      @else
      <a class="button-4" href="/login">{{__('pages.invite')}}</a>
      @endif
    </div>
    <h3 class="heading-tertiary my-5">{{__('pages.admins')}}:{{count($admins)}}</h3>
    <ul class="members-list list-unstyled" id="adddmin">
      @foreach($admins as $admin)
      <li class="members-item">
        <div class="page-member d-flex justify-content-between">
              <a href="#" class="page-member-link d-flex align-items-center">
                <img src="{{asset('assets/images/users')}}/{{$admin->member->personal_image}}" alt="#" class="member-img img-fluid">
                <span class="d-inline-block page-member-link_span">
                  <p class="user-name">{{$admin->member->name}}</p>
                  <p class="user-followers" id="{{$admin->user_id}}" > {{count($admin->member->followers)}} {{__('pages.follower')}}</p>
                </span>
              </a>
            <div>
              @if(Auth::guard('web')->user())
                @if($admin->user_id != Auth::guard('web')->user()->id)
                  @if($admin->friendship == 'guest')
                    <button class="button-4 totyFrientshepPage" id="add|{{$admin->user_id}}">{{__('pages.add_friend')}}</button>
                  @elseif($admin->friendship == 'accepted')
                    <button class="button-2 totyFrientshepPage" id="remove|{{$admin->user_id}}">{{__('pages.un_friend')}}</button>
                  @elseif($admin->friendship == 'pending')
                    <button class="button-2 totyFrientshepPage" id="remove|{{$admin->user_id}}">{{__('pages.un_friend')}}</button>
                  @elseif($admin->friendship == 'request')
                    <button class="button-4 totyFrientshepPage" id="confirm|{{$admin->user_id}}">{{__('pages.confirm_friend')}}</button>
                  @endif

                  @if($admin->follow == 0)
                    <button class="button-4 totyFollowingPage " id="addFollowing|{{$admin->user_id}}">{{__('pages.add_following')}}</button>
                  @elseif($admin->follow == 1)
                    <button class="button-2 totyFollowingPage" id="removeFollowing|{{$admin->user_id}}">{{__('pages.un_following')}}</button>
                  @endif

                  
                @endif
                @else
                <a class="button-4" href="/login">{{__('pages.add_friend')}}</a>
                <a class="button-4" href="/login">{{__('pages.add_following')}}</a>

              @endif
              
            </div>
          </div>
      </li>
      @endforeach
    </ul>
    <h3 class="heading-tertiary my-5">الاعضاء:{{count($accepteds)}}</h3>
    <ul class="members-list list-unstyled">
      @foreach($accepteds as $accepted)
      <li class="members-item" id="{{$accepted->user_id}}|{{$page['id']}}">
        <div class="page-member d-flex justify-content-between">
          <a href="#" class="page-member-link d-flex align-items-center">
            <img src="{{asset('assets/images/users')}}/{{$accepted->member->personal_image}}" alt="#" class="member-img img-fluid">
            <span class="d-inline-block page-member-link_span">
              <p class="user-name">{{$accepted->member->name}}</p>
              <p class="user-followers" id="{{$accepted->user_id}}" > {{count($accepted->member->followers)}} {{__('pages.follower')}}</p>
            </span>
          </a>
          <div>
            @if(Auth::guard('web')->user())
              @if($accepted->friendship == 'guest')
                <button class="button-4 totyFrientshepPage" id="add|{{$accepted->user_id}}">{{__('pages.add_friend')}}</button>
              @elseif($accepted->friendship == 'accepted')
                <button class="button-2 totyFrientshepPage" id="remove|{{$accepted->user_id}}">{{__('pages.un_friend')}}</button>
              @elseif($accepted->friendship == 'pending')
                <button class="button-2 totyFrientshepPage" id="remove|{{$accepted->user_id}}">{{__('pages.un_friend_request')}}</button>
              @elseif($accepted->friendship == 'request')
                <button class="button-4 totyFrientshepPage" id="confirm|{{$accepted->user_id}}">{{__('pages.confirm_friend')}}</button>
                {{-- <button class="button-2 totyFrientshep" id="remove|{{$admin->user_id}}">حذف طلب الصداقة</button> --}}
              @endif

              @if($accepted->follow == 0)
                <button class="button-4 totyFollowingPage " id="addFollowing|{{$accepted->user_id}}">{{__('pages.add_following')}}</button>
              @elseif($accepted->follow == 1)
                <button class="button-2 totyFollowingPage" id="removeFollowing|{{$accepted->user_id}}">{{__('pages.un_following')}}</button>
              @endif
              @if($isAdmin == 1 )
              <button class="button-4 totyAdminPage" id="addAdmin|{{$accepted->user_id}}|{{$page['id']}}">{{__('pages.as_admin')}}</button>
              <button class="button-4 totyAdminPage" id="removeMember|{{$accepted->user_id}}|{{$page['id']}}">{{__('pages.delete_member')}}</button>
              @endif

              @else
              <a class="button-4" href="/login">{{__('pages.add_friend')}}</a>
              <a class="button-4" href="/login">{{__('pages.add_following')}}</a>
              @endif
          </div>
        </div>
      </li>
      @endforeach
    </ul>
  </div>
  @if(Auth::guard('web')->user())
  <!-- invite friends -->
  <div class="modal fade" id="friends" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="exampleModalLabel">{{__('pages.friends')}}</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: 0px;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" >
          <section class="group-section " style="min-height:auto">
            <div class="group-members my-3">
              <ul class="members-list list-unstyled">
                @foreach($myData->friends as $friend)
                @if($friend->stateId == 1 && $friend->userResever->pagemember['state'] != '1' && $friend->userResever->pagemember['state'] != '3'  && $friend->userResever->groupmember['isAdmin'] != '1')
                <li class="members-item" id="{{$friend->userResever->id}}|{{$page['id']}}">
                  <div class="group-member d-flex justify-content-between">
                    <a href="#" class="group-member-link d-flex align-items-center">
                      <img src="{{asset('assets/images/users')}}/{{$friend->userResever->personal_image}}" alt="#" class="member-img img-fluid">
                      <span class="d-inline-block group-member-link_span">
                          <p class="user-name">{{$friend->userResever->name}}</p>
                        </span>
                    </a>
                    <div>
                      <button class="button-4 totyAdminPage" id="invite|{{$friend->userResever->id}}|{{$page['id']}}">{{__('pages.invite')}}</button>
                    </div>
                  </div>
                </li>
                @endif
                @endforeach
                @foreach($myData->myfriends as $myfriends)
                @if($myfriends->stateId == 1 && $myfriends->userSender->pagemember['state'] != '1' && $myfriends->userSender->pagemember['state'] != '2' && $myfriends->userSender->groupmember['state'] != '3' && $myfriends->userResever->groupmember['isAdmin'] != '1')
                <li class="members-item" id="{{$myfriends->userSender->id}}|{{$page['id']}}">
                  <div class="page-member d-flex justify-content-between">
                    <a href="#" class="page-member-link d-flex align-items-center">
                      <img src="{{asset('assets/images/users')}}/{{$myfriends->userSender->personal_image}}" alt="#" class="member-img img-fluid">
                      <span class="d-inline-block page-member-link_span">
                          <p class="user-name">{{$myfriends->userSender->name}}</p>
                        </span>
                    </a>
                    <div>
                      <button class="button-4 totyAdminPage" id="invite|{{$myfriends->userSender->id}}|{{$page['id']}}">{{__('pages.invite')}}</button>
                    </div>
                  </div>
                </li>
                @endif
                @endforeach
              </ul>
            </div>
          </section>
        </div>
        <div class="modal-footer">
          <button type="button" class="button-4 " data-dismiss="modal">{{__('pages.close')}}</button>

        </div>
      </div>
    </div>
  </div>
  @endif
@endif

@endsection