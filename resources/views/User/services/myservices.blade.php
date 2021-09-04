@extends('layouts.app')

@section('content')
    <section id="ez-body__center-content" class="col-lg-10 mt-3">
        <div class="modal fade" id="progress-modal" data-controls-modal="progress-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title in" id="myModalLabel">Please Wait</h4>
                        <h4 class="modal-title hide" id="myModalLabel">Complete</h4>
                    </div>
                    <div class="modal-body center-block">
                        <div id="status">PROGRESS HERE</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default hide" data-dismiss="modal" id="btnClose">Close</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal" id="success-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" style="margin-top: 22vh">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between">
                        <span></span>
                        <h5 class="modal-title" id="exampleModalLabel">Success Modal</h5>
                        <button type="button" id="success-modal-dismiss" class="close ml-0" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="success-modal-message" >
                        service created successfully
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="add-service-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" style="margin-top: 22vh">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between">
                        <span></span>
                        <h5 class="modal-title" id="exampleModalLabel">Add Service</h5>
                        <button type="button" id="add-post-modal-dismiss" class="close ml-0" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12" id="error-message" style="display: none">
                            <div class="alert alert-danger alert-dismissible fade show" id="error-status" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        <form action="{{route('services.store')}}" method="POST" class="container" id="add-service-form" enctype="multipart/form-data">

                            @csrf

                            <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                <label for="cars">Choose A Category:</label>
                                <select id="post-category" name="category_id">
                                    @foreach($categories as $category)
                                        @if(App::getlocale() == 'en')
                                            <option value="{{$category->id}}">{{$category->name_en}}</option>
                                        @else
                                            <option value="{{$category->id}}">{{$category->name_ar}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                <label for="exampleInputEmail1">Target Country:</label>
                                <select onchange="addServiceCities(this)" name="country_id" style="width: 200px" class="js-example-basic-single">
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                <label for="exampleInputEmail1">Target City:</label>
                                <select name="city_id" style="width: 200px" class="select-city js-example-basic-single" disabled>
                                    @foreach($cities as $city)
                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group d-flex justify-content-center mt-2">
                                    <textarea class="form-control w-75 p-2" name="body"  id="post-text" cols="200" rows="4"
                                              placeholder="Post Description..."></textarea>
                            </div>

                            <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                <label for="exampleInputEmail1">price</label>
                                <input name="price" class="form-control w-75 mt-2" type="number"/>
                            </div>
                            <!-- Post Images -->
                            <div class="form-group d-flex justify-content-center mt-2">
                                <input class="form-control w-75 mt-2" type="file" name="media[]" id="imgs" accept=".jpg,.jpeg,.png,.svg,.gif"
                                       multiple />
                            </div>
                            <!-- Add Post Btn -->
                            <div class="post-add-btn d-flex justify-content-center mt-4">
                                <button type="button" onclick="addServiceSubmit()" class="btn btn-warning btn-block w-75">
                                    add
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="services-controller m-3 text-center">
            <a class="btn btn-light rounded-5 filter-button @if($category_id == null) ez-active @endif" href="{{route('myservices.show')}}"> All </a>
            @foreach($categories as $category)
                    <a class="btn btn-light rounded-5 filter-button @if($category_id == $category->id) ez-active @endif" href="{{route('myservices.show',$category->id)}}">
                        @if(App::getlocale() == 'en')
                            {{$category->name_en }}
                        @else
                            {{$category->name_ar }}
                        @endif
                    </a>
            @endforeach
            <button data-toggle="modal" data-target="#add-service-modal" onclick="applySelect2();" class="btn btn-warning add-service">Add Service</button>
        </div>
        <div class="services-container d-flex flex-wrap mt-3" id="all-services">
            @if(count($services) > 0)
                @foreach($services as $service)
                    <div class="service card m-2 service-id-{{$service->id}}">
                        <div data-toggle="modal" data-target="#service-modal-{{$service->id}}">

                            @if(count($service->media) > 0)
                                <img src="{{asset('media')}}/{{$service->media[0]->filename}}" style="height: 220px;" width="100%">
                            @else
                                <img src="{{asset('media')}}/services.jpg" style="height: 220px;" width="100%">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{$service->body}}</h5>
                                <p class="card-text">{{$service->price}} $</p>
                            </div>
                        </div>
                        <div class="modal fade service-modal" id="service-modal-{{$service->id}}" tabindex="-1" aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header pb-0">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            Modal title
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body row">
                                        <div class="
                                                col-4
                                                p-3
                                                d-flex
                                                flex-column
                                                justify-content-between
                                                description
                                              ">
                                            <div class="upper-side">
                                                <p class="text-warning">{{$service->price}}</p>
                                                <h6><b>Product Description</b></h6>
                                                <p class="text-muted">
                                                    {{$service->body}}
                                                </p>
                                            </div>
                                            <div class="down-side">
                                                <h6><b>Created By</b></h6>
                                                <div class="d-flex justify-content-between">
                                                    <div class="people mt-2 w-100">
                                                        <div class="people-info d-flex">
                                                            @if($service->publisher->personal_image == null)
                                                                <img
                                                                    class="profile-figure rounded-circle"
                                                                    src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                                                    alt="User Profile Pic"
                                                                />
                                                            @else
                                                                <img
                                                                    class="profile-figure rounded-circle"
                                                                    src="{{asset('media')}}/{{$service->publisher->personal_image}}"
                                                                    alt="User Profile Pic"
                                                                />
                                                            @endif
                                                            <div class="
                                                                  d-flex
                                                                  flex-column
                                                                  align-items-center
                                                                  justify-content-center
                                                                  pl-2
                                                                ">
                                                                <p><b>{{$service->publisher->name}}</b></p>
                                                            </div>
                                                        </div>
                                                        @if($service->publisher->id != auth()->user()->id)
                                                            <button class="btn btn-warning text-white">
                                                                @if($service->follow)
                                                                    Follow
                                                                @else
                                                                    following
                                                                @endif
                                                            </button>
                                                        @else
                                                            <button onclick="confirm('{{ __("Are you sure you want to delete this service ?") }}') ? deleteServiceSubmit({{$service->id}}) : ''" class="btn btn-warning text-white">
                                                                Delete
                                                            </button>
                                                            <button onclick="$('#service-modal-{{ $service->id }}').modal('hide');$('#edit-service-modal-{{ $service->id }}').modal('show');applySelect2();" class="btn btn-warning btn-danger">
                                                                Edit
                                                            </button>
                                                            <form action="{{ route('services.destroy', $service->id) }}" id="delete-service-form-{{$service->id}}" method="post">
                                                                @csrf
                                                                @method('delete')
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($service->publisher->id != auth()->user()->id)
                                                    <button class="btn btn-warning text-white mt-3 w-100">
                                                        Contact The Creator
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-8 img-container">
                                            @if(count($service->media) > 0)
                                                @if(count($service->media) == 1)
                                                    @if($service->media[0]->mediaType == 'image')
                                                        <img src="{{asset('media')}}/{{$service->media[0]->filename}}" width="100%">
                                                    @else
                                                        <video class="pt-3" controls>
                                                            <source src="{{asset('media')}}/{{$service->media[0]->filename}}" type="video/mp4">
                                                        </video>
                                                    @endif
                                                @else
                                                    <div id="story-carousel-{{$service->id}}" class="carousel slide" data-ride="carousel">
                                                        <div class="carousel-inner">
                                                            @foreach($service->media as $media)
                                                                <div class="carousel-item @if ($loop->first == true) active @endif carousel-{{$service->id}}" id="carousel-item-{{$service->id}}">
                                                                    <div class="group-img-container text-center post-modal">
                                                                        <img src="{{asset('media')}}/{{$media->filename}}" width="100%" data-hash="slide-{{$media->id}}"/>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <a class="carousel-control-prev" href="#story-carousel-{{$service->id}}" role="button" data-slide="prev">
                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                        <span class="sr-only">Previous</span>
                                                    </a>
                                                    <a class="carousel-control-next" href="#story-carousel-{{$service->id}}" role="button" data-slide="next">
                                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                        <span class="sr-only">Next</span>
                                                    </a>
                                                @endif
                                            @else
                                                <img src="{{asset('media')}}/services.jpg" width="100%">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="post-edit-modal">
                            <div class="modal fade" id="edit-service-modal-{{$service->id}}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog" style="margin-top: 22vh">
                                    <div class="modal-content">
                                        <div class="modal-header d-flex justify-content-between">
                                            <span></span>
                                            <h5 class="modal-title" id="exampleModalLabel">Edit Service</h5>
                                            <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="col-12" id="error-message-{{$service->id}}" style="display: none">
                                                <div class="alert alert-danger alert-dismissible fade show" id="error-status-{{$service->id}}" role="alert">
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <form action="{{route('services.update',$service->id)}}" id="edit-service-form-{{$service->id}}" method="POST" class="container" enctype="multipart/form-data">

                                                @csrf
                                                @method('put')


                                                <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                                    <label for="cars">Choose A Category:</label>
                                                    <select id="post-category" name="category_id">
                                                        @foreach($categories as $category)
                                                            @if(App::getlocale() == 'en')
                                                                <option value="{{$category->id}}" @if($category->id == $service->categoryId) selected @endif>{{$category->name_en}}</option>
                                                            @else
                                                                <option value="{{$category->id}}" @if($category->id == $service->categoryId) selected @endif>{{$category->name_ar}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                                    <label for="exampleInputEmail1">Target Country:</label>
                                                    <select onchange="editServiceCities(this,{{$service->id}})" name="country_id" class="js-example-basic-single">
                                                        @foreach($countries as $country)
                                                            <option value="{{$country->id}}" @if($country->id == $service->country_id) selected @endif>{{$country->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                                <div class="form-group d-flex justify-content-between align-items-center m-auto w-75">
                                                    <label for="exampleInputEmail1">Target City:</label>
                                                    <select name="city_id" style="width: 200px" class="edit-city-{{$service->id}} js-example-basic-single">
                                                        @foreach($service->cities as $city)
                                                            <option value="{{$city->id}}" @if($city->id == $service->city_id) selected @endif>{{$city->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Post Desc -->
                                                <div class="post-desc d-flex justify-content-center mt-2">
                                                  <textarea class="w-75" name="body" id="post-text" cols="200" rows="4"
                                                            placeholder="Start Typing..." >{{$service->body}}</textarea>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center m-auto w-75">
                                                    <label for="exampleInputEmail1">price</label>
                                                    <input name="price" value="{{$service->price}}" class="form-control w-75 mt-2" type="number"/>
                                                </div>
                                                <!-- Post Images -->
                                                <div class="form-group d-flex justify-content-center mt-2">
                                                    <input class="form-control w-75 mt-2" type="file" name="media[]" id="imgs" accept=".jpg,.jpeg,.png,.svg,.gif"
                                                           multiple />
                                                </div>

                                                @if(count($service->media) > 0)
                                                    <p>Media</p>
                                                    <div class="imgsContainer d-flex flex-wrap">
                                                    @foreach($service->media as $media)
                                                        @if($media->mediaType == 'image')
                                                            <!-- if media img and imgs=1 -->
                                                                <div class="p-3" style="width: 25%">
                                                                    <img src="{{asset('media')}}/{{$media->filename}}" alt="" width="100%">
                                                                    <div class="w-100 text-center">
                                                                        <input checked type="checkbox"  value="{{$media->filename}}" name="checkedimages[]">
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="p-3" style="width: 25%">
                                                                    <video class="p-1" controls width="100%">
                                                                        <source src="{{asset('media')}}/{{$media->filename}}" type="video/mp4">
                                                                        Your browser does not support HTML video.
                                                                    </video>
                                                                    <div class="w-100 text-center">
                                                                        <input checked type="checkbox" value="{{$media->filename}}" name="checkedimages[]">
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                            @endif
                                            <!-- Add Post Btn -->
                                                <div class="post-add-btn d-flex justify-content-center mt-4">
                                                    <button type="button" onclick="editServiceSubmit({{$service->id}})" class="btn btn-warning btn-block w-75" data-dismiss="modal">
                                                        Save
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-md-8" id="no-service">
                    <div class="card">
                        <div class="card-body">
                            {{ __('no services found!') }}
                        </div>
                    </div>
                </div>
            @endif
    </section>
@endsection
