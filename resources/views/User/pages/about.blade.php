@extends('User.pages.layout')

@section('sectionPages')
@section('sectionPages')
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">{{__('pages.describe')}}</h3>
        <p class="paragraph">
            {{$page->description}}
        </p>
    </div>

    <div class="group-rules">
        <h3 class="heading-tertiary">{{__('pages.rules')}}</h3>
        <p class="paragraph">
            {{$page->rules}}
        </p>
    </div>
</div> 
@endsection