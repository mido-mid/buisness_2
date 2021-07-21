
@section('service')

    <div class="service-id-{{$service->id}}" data-toggle="modal" data-target="#service-modal-{{$service->id}}" id="added-service-div">

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
    <div class="modal fade"
        id="service-modal-{{ $service->id }}"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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
                    <div class="col-4 p-3 d-flex flex-column justify-content-between">
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
                                        @endif
                                    </div>
                                </div>
                                @if($service->publisher->id != auth()->user()->id)
                                    <button class="btn btn-warning text-white mt-3 w-100">
                                        Contact The Creator
                                    </button>
                                @else
                                    <button onclick="confirm('{{ __("Are you sure you want to delete this service ?") }}') ? deleteServiceSubmit({{$service->id}}) : ''" class="btn btn-warning text-white mt-3 w-100">
                                        Delete
                                    </button>
                                    <button onclick="$('#service-modal-{{ $service->id }}').modal('hide');$('#edit-service-modal-{{ $service->id }}').modal('show');" class="btn btn-warning text-white mt-3 w-100">
                                        Edit
                                    </button>
                                    <form action="{{ route('services.destroy', $service->id) }}" id="delete-service-form-{{$service->id}}" method="post">
                                        @csrf
                                        @method('delete')
                                    </form>
                                @endif
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

                            <div id="post-type-service-content">
                                <!-- Select Service Category -->
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
                                <!-- Select Service Price -->
                                {{--                                                <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">--}}
                                {{--                                                    <input class="w-100 border" type="number" placeholder="Service Price $" />--}}
                                {{--                                                </div>--}}
                            </div>
                            <!-- Post Desc -->
                            <div class="post-desc d-flex justify-content-center mt-2">
                                                          <textarea class="w-75" name="body" id="post-text" cols="200" rows="4"
                                                                    placeholder="Start Typing..." >{{$service->body}}</textarea>
                            </div>
                            <!-- Post Images -->
                            <div class="post-desc d-flex justify-content-center mt-2">
                                <input class="form-control w-75 mt-2" type="file" name="media[]" id="imgs" accept="image/*"
                                       multiple />
                            </div>
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
@endsection
