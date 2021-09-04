@extends('layouts.app')

@section('content')
<section id="ez-body__center-content" class="col-lg-8 mt-3">
    <div class="row">
        <div class="col-lg-12">
            <section class="group-section px-3">
              <div class="group-new-group">
                <h2 class="heading-secondary">{{__('pages.edit')}}</h2>
                <form method="post" action="{{route('pages.update',['page'=>$page->id])}}" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                  <input class="form-control form-control-lg my-5" type="text" placeholder="{{__('pages.name_page')}}" name="name" value="{{$page->name}}" required>
                  <select class="form-control form-control-lg my-5" name="category_id" required>
                    <option value="" disabled>{{__('pages.category')}}</option>
                    @foreach($categroys as $categroy)
                    @if($page->category_id == $categroy->id )
                    <option value="{{$categroy->id}}" selected>{{$categroy->name_ar}}</option>
                    @else
                    <option value="{{$categroy->id}}">{{$categroy->name_ar}}</option>
                    @endif
                    @endforeach

                  </select>
                  <div class="uimage-div my-5 text-center">
                    <canvas id= "canv1" style="width:50%;height:150px;border: none"></canvas><br>
                    <label class="select-image-label" for="finput" >{{__('pages.upload_profile')}}</label><br>
                    <input class="select-image-input" type="file" dir="rtl" multiple="false" name="profile_image" accept="image/*" id=finput  onchange="uploadimg()">

                  </div>
                  <div class="uimage-div my-5 text-center" >
                    <canvas id= "canv2" style="width: 100%;height: 150px;border: none"></canvas><br>
                    <label class="select-image-label" for="finput2" >{{__('pages.upload_cover')}}</label><br>
                    <input class="select-image-input" type="file" multiple="false" accept="image/*" name="cover_image" id=finput2 onchange="uploadcover()"  >
                  </div>
                  <textarea dir="rtl" class="form-control form-control-lg my-5" placeholder="{{__('pages.describe')}}" name="description" required>{{$page->description}}</textarea>
                  <textarea dir="rtl" class="form-control form-control-lg my-5" placeholder="{{__('pages.rules')}}" name="rules" required>{{$page->rules}}</textarea>

                  <button type="submit" class="button-4 mt-5">{{__('pages.update')}}</button>
                </form>
              </div>
            </section>
      </div>
        {{-- @include('User.groups.related') --}}
    </div>
</section>
    <script src="https://www.dukelearntoprogram.com/course1/common/js/image/SimpleImage.js"></script>
    <script>
      function draw() {
        var ctx = document.getElementById('canv2').getContext('2d');
        var img = new Image();
        ctx.canvas.width = window.innerWidth;
        ctx.canvas.height = window.innerHeight;
        img.onload = function() {
          ctx.drawImage(img, 0, 0,window.innerWidth,window.innerHeight);
        };
      img.src = "{{asset('media')}}/{{$page->cover_image}}";

      var ctx1 = document.getElementById('canv1').getContext('2d');
        var img1 = new Image();
        ctx1.canvas.width = window.innerWidth;
        ctx1.canvas.height = window.innerHeight;
        img1.onload = function() {
          ctx1.drawImage(img1, 0, 0,window.innerWidth,window.innerHeight);
        };
      img1.src = "{{asset('media')}}/{{$page->profile_image}}";
      }
      draw();
    </script>

<section id="ez-body__right-sidebar" class="col-lg-2 ez-sidebar">
  <ul class="pt-4" id="right-sidebar__items">
      @if(count($expected_pages) > 0)
          <li class="mt-3">
              <h6 class="pb-2" style="font-weight: bold;font-size: 15px">Pages You May Like</h6>
              <div class="suggested-groups">
                  @foreach($expected_pages as $page)
                  <div class="card">
                    <a href="/pages/{{$page->id}}">
                        <img src="{{asset('media')}}/{{$page->profile_image}}" class="card-img-top" alt="...">
                    </a>
                    <div class="d-flex justify-content-between">
                        <div class="card-body">
                            <a href="pages/{{$page->id}}" style="color:black !important">
                                <h3 class="card-title">{{$page->name}}</h3>
                            </a>
                            <p class="card-text"><small class="text-muted" id="{{$page->id}}">
                                <?php
                                    $member = App\models\PageMember::where('page_id',$page->id)->count();
                                    echo $member;
                                ?>
                                </small>
                                {{__('pages.member')}}
                            </p>
                        </div>
                              @if(Auth::guard('web')->user())
                                <?php
                                    $checkState = App\models\PageMember::where('page_id',$page->id)->where('user_id',auth::user()->id)->get();
                                ?>
                                @if (count($checkState)==0)
                                <div class="p-2">
                                        <button class="button-4 totyPage" id="join|{{$page->id}}" >{{__('pages.like')}} </button>
                                </div>
    
                                @elseif (count($checkState)>0)
                                    @if ($checkState[0]->state == 1)
                                        <div class="p-2">
                                                <button class="button-2 totyPage" id="leave|{{$page->id}}">{{__('pages.dislike')}}</button>
                                        </div>
    
                                    @elseif ($checkState[0]->state == 2)
                                        <div class="p-2">
                                            <button class="button-2 totyPage" id="leave|{{$page->id}}">{{__('pages.dislike_request')}}</button>
                                        </div>
    
                                    @elseif ($checkState[0]->isAdmin == 1)
                                        <div class="p-2">
                                            <button class="button-2">{{__('pages.admin')}}</button>
                                        </div>
                                    @endif
                                @endif
                              @else
                                  <form action="/login" method="post">
                                      @csrf
                                      <button class="button-4">{{__('pages.like')}}</button>
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

