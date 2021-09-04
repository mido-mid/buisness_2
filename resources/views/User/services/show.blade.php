@extends('layouts.app')

@section('content')
    <section id="ez-body__center-content" class="col-lg-10 mt-3">
        <div class="services-controller m-3 text-center">
            <a class="btn btn-light rounded-5 filter-button @if($category_id == null) ez-active @endif" href="{{route('services')}}"> All </a>
            @foreach($categories as $category)
                    <a class="btn btn-light rounded-5 filter-button @if($category_id == $category->id) ez-active @endif" href="{{route('services',$category->id)}}">
                        @if(App::getlocale() == 'en')
                            {{$category->name_en }}
                        @else
                            {{$category->name_ar }}
                        @endif
                    </a>
            @endforeach
            <a href="{{route('myservices.show')}}" class="btn btn-warning add-service">My Services</a>
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
