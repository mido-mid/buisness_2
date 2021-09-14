@extends('User.groups.layout')

@section('sectionGroups')
@if($myggroup->privacy == 2 && $isAdmin == 0 && $myState != 1)
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">{{__('pages.privacy')}}</h3>
    </div>
</div>
@else
<div class="group-images my-3">
    <div class="row">
      @if(count($images)>0)
        @foreach($images as $image)
        <div class="col-md-4 my-2">
            <div class="group-img-container">
                <img src="{{asset('media')}}/{{$image->filename}}" alt="" class="group-img img-fluid pointer" data-toggle="modal" data-target="#images{{$image->id}}">
            </div>
        </div>
        @endforeach
        @endif
    </div>

    <!-- Modal Posts-->
    @if(count($images)>0)
    @foreach($images as $imagemodel)
    <div class="modal fade bd-example-modal-lg" id="images{{$imagemodel->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background: rgba(0,0,0,0);border: none">
          <div class="modal-body" >
              <div class="group-img-container text-center post-modal">
                <img  src="{{asset('media')}}/{{$imagemodel->filename}}" alt="" class="group-img img-fluid " ><br>
                <div class="modal-post" >
                  <a href="{{route('single-post-group',['id'=>$myggroup->id,'postId'=>$imagemodel->model_id])}}" style="color: #FFF">البوست</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    {{-- </div> --}}
    @endforeach
    @endif
  </div>
@endif

@endsection
