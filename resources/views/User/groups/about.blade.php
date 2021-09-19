@extends('User.groups.layout')

@section('sectionGroups')

<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">{{__('groups.describe')}}</h3>
        <p class="paragraph">
            {{$myggroup->description}}
        </p>
    </div>

    <div class="group-rules">
        <h3 class="heading-tertiary">{{__('groups.rules')}}</h3>
        <p class="paragraph">
            {{$myggroup->rules}}
        </p>
    </div>
</div>

@endsection
