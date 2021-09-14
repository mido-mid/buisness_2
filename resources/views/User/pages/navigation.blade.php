<div class="page-section-header py-3 d-flex justify-content-between">
  <div class="page-info">
    <img src="{{asset('media')}}/{{$page->profile_image}}" alt="" class="page-profile-picture pointer" data-toggle="modal" data-target="#image">
    <!-- Modal img-->
    <div class="modal fade bd-example-modal-lg" id="image" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background: rgba(0,0,0,0);border: none">
          <div class="modal-body" >
            <div class="owl-carousel owl-theme">
              <div class="group-img-container text-center post-modal">
                <img src="{{asset('media')}}/{{$page->profile_image}}" alt="group-header" class="d-block w-100">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="page-info_heading-memers-count">
      <h1 class="heading-primary d-inline-block">{{$page->name}}</h1>
      <p class="page-members-count" id="{{$page->id}}">{{count($page_members)}} {{__('pages.member')}}</p>
    </div>
  </div>
  <div>
    @if($myState == 1 && $isAdmin != 1)
        <button class="button-2 totyPage" id="leave|{{$page->id}}|2">{{__('pages.dislike')}}</button>
    @elseif($myState == 2)
        <button class="button-2 totyPage" id="leave|{{$page->id}}|2">{{__('pages.dislike_request')}}</button>
      @elseif($myState == 3)
        <button class="button-2 totyPage" id="leave|{{$page->id}}|2">{{__('pages.refuse_invite')}}</button>
        <button class="button-4 totyPage" id="confirm|{{$page->id}}|2">{{__('pages.confirm_invite')}}</button>
    @elseif($isAdmin == 1)
    <form method="post" action="{{route('adminLeftPage',['id'=>$page->id])}}" enctype="multipart/form-data">
      @csrf
      <button class="button-2">{{__('pages.leave_page')}}</button>
    </form>
    @else
      @if(Auth::guard('web')->user())
        <button class="button-4 totyPage" id="join|{{$page->id}}|2">{{__('pages.like')}} </button>
      @else
        <form action="/login" method="post">
          @csrf
          <button class="button-4">{{__('pages.like')}}</button>
        </form>
      @endif
    @endif
  </div>
</div>

<div class="page-nav py-3">
  @if(Auth::guard('web')->user())
    <a class="button-3" href="{{route('main-page',['id'=>$page->id])}}"> {{__('pages.main')}}</a>
    <a class="button-3" href="{{route('members-page',['id'=>$page->id])}}">{{__('pages.members')}}</a>
    <a class="button-3" href="{{route('videos-page',['id'=>$page->id])}}">{{__('pages.videos')}}</a>
    <a class="button-3" href="{{route('images-page',['id'=>$page->id])}}">{{__('pages.images')}}</a>
    <a class="button-3" href="{{route('about-page',['id'=>$page->id])}}">{{__('pages.about')}}</a>
    @if($isAdmin == 1)
      {{-- <a class="button-3" href="{{route('requests-page',['id'=>$page->id])}}">{{__('pages.requests_page')}}</a> --}}
      <a class="button-3" href="/pages/{{$page->id}}/edit">{{__('pages.edit')}}</a>
      <button class="button-4"  data-toggle="modal" data-target="#confirm">{{__('pages.delet_page')}}</button>
      <!-- Confirm delete-->
    <div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog " role="document">
        <div class="modal-content" style="border: none">
          <div class="modal-body" >
            <div class="owl-carousel owl-theme">
              <div class="group-img-container text-center post-modal">
                <h3 >{{__('pages.delet_page_confirm')}}</h3>
                <form method="post" action="{{ route('pages.destroy', $page['id'])}}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button-3 button-danger">{{__('pages.delet_page')}}</button>
                </form>
                <button class="button-3" style="background: #92e019" data-dismiss="modal">{{__('pages.cancel_delete')}}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
  @else
    <a class="button-3" href="/login"> {{__('pages.main')}}</a>
    <a class="button-3" href="/login">{{__('pages.members')}}</a>
    <a class="button-3" href="/login">{{__('pages.videos')}}</a>
    <a class="button-3" href="/login">{{__('pages.images')}}</a>
    <a class="button-3" href="/login">{{__('pages.about')}}</a>
  @endif
  @if ($message = Session::get('error'))
    <div class="alert alert-danger alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
    @endif


</div>
