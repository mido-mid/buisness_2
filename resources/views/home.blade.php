@extends('layouts.app')

@section('content')
    <section id="ez-body__center-content" class="col-lg-8 mt-3">
        <div class="search-bar">
            <input type="text" placeholder="Search" class="search-input"/>
        </div>
        <div class="stories d-flex mt-2" id="story">
            <div
                class="my-story story"
                data-toggle="modal"
                data-target="#storyModal"
            >
                <img
                    src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                    alt="Story Pic"
                />
                <i class="far fa-plus-square"></i>
            </div>
            <div class="add-story-modal">
                <div
                    class="modal fade"
                    id="storyModal"
                    tabindex="-1"
                    aria-hidden="true"
                >
                    <div class="modal-dialog" style="margin-top: 22vh">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between">
                                <span></span>
                                <h5 class="modal-title" id="exampleModalLabel">
                                    Add Story
                                </h5>
                                <button
                                    type="button"
                                    class="close ml-0"
                                    data-dismiss="modal"
                                    aria-label="Close"
                                >
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form
                                    action=""
                                    class="container"
                                    enctype="multipart/form-data"
                                >
                                    <!-- Story Text -->
                                    <div
                                        class="post-desc d-flex justify-content-center mt-2"
                                    >
                          <textarea
                              name="story-text"
                              id="story-text"
                              cols="200"
                              rows="4"
                              placeholder="Start Typing..."
                          ></textarea>
                                    </div>
                                    <!-- Story Images -->
                                    <div
                                        class="post-desc d-flex justify-content-center mt-2"
                                    >
                                        <input
                                            class="form-control w-100 mt-2"
                                            type="file"
                                            name="imgs"
                                            id="story-img"
                                            accept="image/*"
                                        />
                                    </div>
                                    <!-- Add Story Btn -->
                                    <div
                                        class="post-add-btn d-flex justify-content-center mt-4"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-secondary btn-block w-75"
                                            data-dismiss="modal"
                                        >
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="app">
                <stories
                    v-for="story in stories"
                    :key="story.by"
                    :media="story.media"
                    :by="story.by"
                    :text="story.text"
                ></stories>
            </div>
        </div>
        <div class="add-post mt-2">
            <!-- Add Post Modal -->
            <div
                class="modal fade"
                id="add-post-modal"
                tabindex="-1"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog" style="margin-top: 22vh">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <span></span>
                            <h5 class="modal-title" id="exampleModalLabel">Add Post</h5>
                            <button
                                type="button"
                                class="close ml-0"
                                data-dismiss="modal"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form
                                action=""
                                class="container"
                                enctype="multipart/form-data"
                            >
                                <!-- Select Post Type -->
                                <div
                                    class="post-type d-flex justify-content-between align-items-center m-auto w-75"
                                >
                                    <div>Post As:</div>
                                    <div class="d-flex align-items-center">
                                        <input
                                            type="radio"
                                            name="post-type"
                                            value="post"
                                            id="post-type-post"
                                            checked
                                        />
                                        <span class="pl-2">Post</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input
                                            class="m-0"
                                            type="radio"
                                            name="post-type"
                                            value="service"
                                            id="post-type-service"
                                        />
                                        <span class="pl-2">Service</span>
                                    </div>
                                </div>
                                <!-- Select post Privacy -->
                                <div
                                    class="post-privacy d-flex justify-content-between align-items-center m-auto w-75"
                                >
                                    <label for="cars">Choose Post Privacy:</label>
                                    <select id="post-privacy" name="privacy">
                                        <option value="volvo">Volvo</option>
                                        <option value="saab">Saab</option>
                                        <option value="fiat">Fiat</option>
                                        <option value="audi">Audi</option>
                                    </select>
                                </div>
                                <div id="post-type-service-content" class="d-none">
                                    <!-- Select Service Category -->
                                    <div
                                        class="post-category d-flex justify-content-between align-items-center m-auto w-75"
                                    >
                                        <label for="cars">Choose A Category:</label>
                                        <select id="post-category" name="category">
                                            <option value="volvo">Volvo</option>
                                            <option value="saab">Saab</option>
                                            <option value="fiat">Fiat</option>
                                            <option value="audi">Audi</option>
                                        </select>
                                    </div>
                                    <!-- Select Service Price -->
                                    <div
                                        class="post-category d-flex justify-content-between align-items-center m-auto w-75"
                                    >
                                        <input
                                            class="w-100 border"
                                            type="number"
                                            placeholder="Service Price $"
                                        />
                                    </div>
                                </div>
                                <!-- Post Desc -->
                                <div class="post-desc d-flex justify-content-center mt-2">
                        <textarea
                            class="w-75"
                            name="post-text"
                            id="post-text"
                            cols="200"
                            rows="4"
                            placeholder="Post Description..."
                        ></textarea>
                                </div>
                                <!-- Post Images -->
                                <div class="post-desc d-flex justify-content-center mt-2">
                                    <input
                                        class="form-control w-75 mt-2"
                                        type="file"
                                        name="imgs"
                                        id="imgs"
                                        accept="image/*"
                                        multiple
                                    />
                                </div>
                                <!-- Add Post Btn -->
                                <div
                                    class="post-add-btn d-flex justify-content-center mt-4"
                                >
                                    <button
                                        type="button"
                                        class="btn btn-warning btn-block w-75"
                                        data-dismiss="modal"
                                    >
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <input
                type="text"
                placeholder="Add New Post"
                class="w-100"
                data-toggle="modal"
                data-target="#add-post-modal"
            />
        </div>
        <div id="app">
            <posts v-for="post in posts" :data="post"></posts>
        </div>
    </section>
    <section id="ez-body__right-sidebar" class="col-lg-2 ez-sidebar">
        <ul class="pt-4" id="right-sidebar__items">
            <li>
                <h6 class="pb-2">Posts You May Like</h6>
                <div class="suggested-posts mb-1">
                    @foreach($expected_posts as $post)
                        <div class="post">
                            <section class="posted-by">
                                @if($post->publisher->personal_image)
                                    <img
                                        class="profile-figure"
                                        src="{{asset('media')}}/{{$post->publisher->personal_image}}"
                                        alt="User Profile Pic"
                                    />
                                @else
                                    <img
                                        class="profile-figure"
                                        src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                        alt="User Profile Pic"
                                    />
                                @endif
                                <span>{{$post->publisher->name}}</span>
                            </section>
                            <section class="post-desc">
                                <p>{{$post->body}}</p>
                            </section>
                            <section class="post-img">
                                @if(count($post->media) > 0)
                                    @foreach($post->media as $media)
                                        <img
                                            class="profile-figure"
                                            src="{{asset('media')}}/{{$media->filename}}"
                                            alt="User Profile Pic"
                                        />
                                    @endforeach
                                @endif
                            </section>
                        </div>
                    @endforeach
                </div>
            </li>
            <li class="mt-3">
                <h6 class="pb-2">People You May Wanna Follow</h6>
                <div class="suggested-peoples">
                    @foreach($expected_friends as $friend)
                        <div class="people mt-2">
                            <div class="people-info d-flex">
                                @if($friend->personal_image)
                                    <img
                                        class="profile-figure"
                                        src="{{asset('media')}}/{{$friend->personal_image}}"
                                        alt="User Profile Pic"
                                    />
                                @else
                                    <img
                                        class="profile-figure"
                                        src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                        alt="User Profile Pic"
                                    />
                                @endif
                                    <div class="d-flex flex-column align-items-center">
                                        <p><b>{{$friend->name}}</b></p>
                                    </div>
                            </div>
                            <button class="btn btn-warning text-white">add friend</button>
                        </div>
                    @endforeach
                </div>
            </li>
            <li class="mt-3">
                <h6 class="pb-2">Groups You May Like</h6>
                <div class="suggested-groups">
                    @foreach($expected_groups as $group)
                        <div class="group">
                            <div class="group-banner">
                                @if($group->cover_image)
                                    <img
                                        width="100%"
                                        src="{{asset('media')}}/{{$group->cover_image}}"
                                        alt="User Profile Pic"
                                    />
                                @else
                                    <img
                                        width="100%"
                                        src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                        alt="User Profile Pic"
                                    />
                                @endif
                            </div>
                            <div class="mt-2 group-info">
                                <div>
                                    <p><b>{{$group->name}}</b></p>
                                    <p>{{$group->members}} members</p>
                                </div>
                                <button class="btn btn-warning text-white">Join</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </li>
            <li class="mt-3">
                <h6 class="pb-2">Pages You May Like</h6>
                <div class="suggested-groups">
                    @foreach($expected_pages as $page)
                        <div class="group">
                            <div class="group-banner">
                                @if($page->cover_image)
                                    <img
                                        width="100%"
                                        src="{{asset('media')}}/{{$page->cover_image}}"
                                        alt="User Profile Pic"
                                    />
                                @else
                                    <img
                                        width="100%"
                                        src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                        alt="User Profile Pic"
                                    />
                                @endif
                            </div>
                            <div class="mt-2 group-info">
                                <div>
                                    <p><b>{{$page->name}}</b></p>
                                    <p>{{$page->members}} likes</p>
                                </div>
                                <button class="btn btn-warning text-white">Like</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </li>
        </ul>
    </section>
@endsection

