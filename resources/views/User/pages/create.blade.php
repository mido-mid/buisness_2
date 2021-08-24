@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-10">
            <section class="group-section px-3">
              <div class="group-new-group">
                <h2 class="heading-secondary">{{__('pages.create')}}</h2>
                <form method="post" action="{{ route('pages.store') }}" enctype="multipart/form-data">
                    @csrf
                  <input class="form-control form-control-lg my-5" type="text" placeholder="{{__('pages.name_page')}}" name="name" required>
                  <select class="form-control form-control-lg my-5" name="privacy" required>
                    <option value="" disabled selected>{{__('pages.pivacy_add')}}</option>
                    <option value="1">{{__('pages.public')}}</option>
                    <option value="0">{{__('pages.private')}}</option>
                  </select>
                  <select class="form-control form-control-lg my-5" name="category_id" required>
                    <option value="" disabled selected>{{__('pages.category')}}</option>
                    @foreach($categroys as $categroy)
                    <option value="{{$categroy->id}}">{{$categroy->name_ar}}</option>
                    @endforeach

                  </select>
                  <div class="uimage-div my-5 text-center">
                    <canvas id= "canv1" style="width:50%;height:150px;border: none"></canvas><br>
                    <label class="select-image-label" for="finput" >{{__('pages.upload_profile')}}</label><br>
                    <input class="select-image-input" type="file" dir="rtl" multiple="false" name="profile_image" accept="image/*" id=finput onchange="uploadimg()">

                  </div>
                  <div class="uimage-div my-5 text-center" >
                    <canvas id= "canv2" style="width: 100%;height: 150px;border: none"></canvas><br>
                    <label class="select-image-label" for="finput2" >{{__('pages.upload_cover')}}</label><br>
                    <input class="select-image-input" type="file" multiple="false" accept="image/*" name="cover_image" id=finput2 onchange="uploadcover()"  >
                  </div>
                  <textarea dir="rtl" class="form-control form-control-lg my-5" placeholder="{{__('pages.describe')}}" name="description" required></textarea>
                  <textarea dir="rtl" class="form-control form-control-lg my-5" placeholder="{{__('pages.rules')}}" name="rules" required></textarea>
                  @if(Auth::guard('web')->user())
                  <button type="submit" class="button-4 mt-5">{{__('pages.addPage')}}</button>
                  @else
                  <a href="/login" class="button-4 mt-5">{{__('pages.addPage')}}</a>
                  @endif
                </form>
              </div>
            </section>
      </div>
        {{-- @include('User.groups.related') --}}
    </div>

@endsection

