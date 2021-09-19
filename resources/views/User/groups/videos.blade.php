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

  </div>
@endif

@endsection
