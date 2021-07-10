@extends('layouts.app')

@section('content')
    <section id="ez-body__center-content" class="col-lg-10 mt-3">
        <div class="search-bar d-flex justify-content-center">
            <input class="w-75" type="text" placeholder="Search" />
        </div>
        <div class="services-controller m-3 text-center">

            <button
                class="btn btn-light rounded-5 filter-button @if($category_id == null) ez-active @endif"
            >
                    All
            </button>
            @foreach($categories as $category)
                <button
                    class="btn btn-light rounded-5 filter-button @if($category_id == $category->id) ez-active @endif"
                >
                    @if(App::getlocale() == 'en')
                        {{$category->name_en }}
                    @else
                        {{$category->name_ar }}
                    @endif
                </button>
            @endforeach
        </div>
        <div class="services-container d-flex flex-wrap mt-3">
            @if(count($services) > 0)
                @foreach($services as $service)
                    <div class="service card m-2" data-toggle="modal"
                         data-target="#exampleModal-{{$service->id}}">

                        @if(count($service->media) > 0)
                            @if($service->media[0]->mediaType == 'image')
                                <img src="{{asset('media')}}/{{$service->media[0]->filename}}" width="100%">
                            @else
                                <video class="pt-3" controls>
                                    <source src="{{asset('media')}}/{{$service->media[0]->filename}}" type="video/mp4" width="100%">
                                </video>
                            @endif
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{$service->body}}</h5>
                            <p class="card-text">{{$service->price}} $</p>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div
                        class="modal fade"
                        id="exampleModal-{{ $service->id }}"
                        tabindex="-1"
                        aria-labelledby="exampleModalLabel"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header pb-0">
                                    <h5 class="modal-title" id="exampleModalLabel">
                                        Service
                                    </h5>
                                    <button
                                        type="button"
                                        class="close"
                                        data-dismiss="modal"
                                        aria-label="Close"
                                    >
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body row">
                                    <div
                                        class="
                        col-4
                        p-3
                        d-flex
                        flex-column
                        justify-content-between
                      "
                                    >
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
                                                        <img
                                                            class="profile-figure rounded-circle"
                                                            src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                                            alt="User Profile Pic"
                                                        />
                                                        <div
                                                            class="
                                  d-flex
                                  flex-column
                                  align-items-center
                                  justify-content-center
                                  pl-2
                                "
                                                        >
                                                            <p><b>{{$service->publisher->name}}</b></p>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-warning text-white">

                                                        @if($service->follow)
                                                            Follow
                                                        @else
                                                            following
                                                        @endif
                                                    </button>
                                                </div>
                                            </div>
                                            <button class="btn btn-warning text-white mt-3 w-100">
                                                Contact The Creatoer
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-8 img-container">
                                        @if(count($service->media) > 0)
                                            @if($service->media[0]->mediaType == 'image')
                                                <img src="{{asset('media')}}/{{$service->media[0]->filename}}" width="100%">
                                            @else
                                                <video class="pt-3" controls>
                                                    <source src="{{asset('media')}}/{{$service->media[0]->filename}}" type="video/mp4">
                                                </video>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            @else
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            {{ __('no services found!') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
