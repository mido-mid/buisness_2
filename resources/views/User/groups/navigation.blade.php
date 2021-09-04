<div class="group-section-header py-3 d-flex justify-content-between">
    <h1 class="heading-primary">
        {{$group->name}}
    </h1>
    {{-- auth --}}
    @if($myState == 1)
        <button class="button-2 toty2" id="leave|{{$group->id}}|2">{{__('groups.left')}}</button>
    @elseif($myState == 2)
        <button class="button-2 toty2" id="leave|{{$group->id}}|2">{{__('groups.left_request')}}</button>
      @elseif($myState == 3)
        <button class="button-2 toty2" id="leave|{{$group->id}}|2">{{__('groups.refuse_invite')}}</button>
        <button class="button-4 toty2" id="confirm|{{$group->id}}|2">{{__('groups.confirm_invite')}}</button>
    @elseif($isAdmin == 1)
    <form method="post" action="{{route('adminLeft',['id'=>$group->id])}}" enctype="multipart/form-data">
      @csrf
      <button class="button-2">{{__('groups.leave_group')}}</button>
    </form>
    @else
      @if(Auth::guard('web')->user())
        <button class="button-4 toty2" id="join|{{$group->id}}|2">{{__('groups.join')}} </button>
      @else
        <form action="/login" method="post">
          @csrf
          <button class="button-4">{{__('groups.join')}}</button>
        </form>
      @endif
    @endif
</div>
<p class="group-members-count" id="{{$group->id}}">{{count($group_members)}} {{__('groups.member')}}</p>
<div class="group-nav py-3">
  @if(Auth::guard('web')->user())
    <a class="button-3" href="{{route('main-group',['id'=>$group->id])}}"> {{__('groups.main')}}</a>
    <a class="button-3" href="{{route('members-group',['id'=>$group->id])}}">{{__('groups.members')}}</a>
    <a class="button-3" href="{{route('videos-group',['id'=>$group->id])}}">{{__('groups.videos')}}</a>
    <a class="button-3" href="{{route('images-group',['id'=>$group->id])}}">{{__('groups.images')}}</a>
    <a class="button-3" href="{{route('about-group',['id'=>$group->id])}}">{{__('groups.about')}}</a>
    @if($isAdmin == 1)
      <a class="button-3" href="{{route('requests-group',['id'=>$group->id])}}">{{__('groups.requests_group')}}</a>
      <a class="button-3" href="/groups/{{$group->id}}/edit">{{__('groups.edit')}}</a>
      <button class="button-4"  data-toggle="modal" data-target="#confirm">{{__('groups.delet_group')}}</button>
      <!-- Confirm delete-->
    <div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog " role="document">
        <div class="modal-content" style="border: none">
          <div class="modal-body" >
            <div class="owl-carousel owl-theme">
              <div class="group-img-container text-center post-modal">
                <h3 >{{__('groups.delet_group_confirm')}}</h3>
                <form method="post" action="{{ route('groups.destroy', $group->id)}}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button-3 button-danger">{{__('groups.delet_group')}}</button>
                </form>
                <button class="button-3" style="background: #92e019" data-dismiss="modal">{{__('groups.cancel_delete')}}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
  @else
    <a class="button-3" href="/login">  {{__('groups.main')}}</a>
    <a class="button-3" href="/login">{{__('groups.members')}}</a>
    <a class="button-3" href="/login">{{__('groups.videos')}}</a>
    <a class="button-3" href="/login">{{__('groups.images')}}</a>
    <a class="button-3" href="/login">{{__('groups.about')}}</a>
  @endif
  @if ($message = Session::get('error'))
    <div class="alert alert-danger alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
    @endif


</div>
