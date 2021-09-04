@extends('User.groups.layout')

@section('sectionGroups')
@if($group->privacy == 0 && $isAdmin == 0 && $myState != 1)
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">{{__('pages.privacy')}}</h3>
    </div>
</div>
@else
<div class="group-images my-3">
    <div class="row">
        @foreach($videos as $video)
        <div class="col-md-4 my-2">
            <div class="group-img-container">
                <video width="100%" height="100%" controls>
                    <source src="{{asset('media')}}/{{$video->filename}}">
                    Your browser does not support HTML video.
                </video>
            </div>

        </div>
        @endforeach
    </div>

    <!-- Modal Posts-->
    {{-- @foreach($videos as $videomodel)
    <div class="modal fade bd-example-modal-lg" id="images{{$videomodel->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background: rgba(0,0,0,0);border: none">
            <div class="modal-body" >
              <div class="group-img-container text-center post-modal">
                <video class="p-1" height="100%" width="100%" controls>
                    <source src="{{asset('media')}}/{{$videomodel->filename}}" style="height: 400px" type="video/mp4">
                    Your browser does not support HTML video.
                </video>                <div class="modal-post" >
                  <a href="{{route('single-post-group',['id'=>$group->id,'postId'=>$videomodel->model_id])}}" style="color: #FFF">البوست</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach --}}
  </div>
@endif

@endsection
