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
        <div class="post-container bg-white mt-3 p-3">
            <div class="post-owner d-flex align-items-center">
                <div class="owner-img">
                    <img
                        src=""
                        class="rounded-circle"
                    />
                </div>
                <div class="owner-name pl-3">
                    <a href="/"><b>username</b></a><br>
                    <span>date</span>
                </div>
                <!-- Post options -->
                <div class="post-options">
                    <ul class="options">
                        <li data-toggle="modal" data-target="'#advertiseModal-'+post.id">Advertise</li>
                        <li @click="toggleSave(post.id)" v-if="post.saved == false">Save Post</li>
                        <li @click="toggleSave(post.id)" v-else>Saved</li>
                        <li data-toggle="modal" :data-target="'#edit-post-modal-'+post.id">Edit</li>
                        <li data-toggle="modal" :data-target="'#delete-post-modal-'+post.id" class="last-li">Delete</li>
                    </ul>
                </div>
                <div class="post-option ml-auto pr-3" @click="toggleOptions(post.id)">
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
            <div class="post-desc mt-3">
                <pre style="text-align:right;">text</pre>
                <pre style="text-align:left;">text</pre>
                <div class="media">
                    <div v-if="post.mediaType == 'img' && post.mediaCount == 1">
                        <img
                            :src="post.media"
                            alt="opel car"
                        />
                    </div>
                    <div v-if="post.mediaType == 'img' && post.mediaCount == 2" class="d-flex">
                        <img
                            v-for="img in post.media"
                            :src="post.media"
                            alt="opel car"
                            class="w-50 p-1"
                        />
                    </div>
                    <div v-if="post.mediaType == 'img' && post.mediaCount == 3">
                        <img
                            class="d-block w-100"
                            :src="post.media[0]"
                            alt="opel car"
                        />
                        <div class="d-flex">
                            <img
                                :src="post.media[1]"
                                alt="opel car"
                                class="w-50 pr-1 pt-2"
                            />
                            <img
                                :src="post.media[2]"
                                alt="opel car"
                                class="w-50 pl-1 pt-2"
                            />
                        </div>
                    </div>
                    <div v-if="post.mediaType == 'img' && post.mediaCount == 4">
                        <div class="d-flex">
                            <img
                                :src="post.media[0]"
                                alt="opel car"
                                class="w-50 pr-1"
                            />
                            <img
                                :src="post.media[1]"
                                alt="opel car"
                                class="w-50 pl-1"
                            />
                        </div>
                        <div class="d-flex">
                            <img
                                :src="post.media[2]"
                                alt="opel car"
                                class="w-50 pr-1 pt-2"
                            />
                            <img
                                :src="post.media[3]"
                                alt="opel car"
                                class="w-50 pl-1 pt-2"
                            />
                        </div>
                    </div>
                    <div v-if="post.mediaType == 'img' && post.mediaCount > 4" class="d-flex">
                        <img
                            v-if="post.mediaCount <= 4"
                            v-for="img in post.media"
                            :src="post.media"
                            alt="opel car"
                        />
                    </div>
                    <video v-if="post.mediaType == 'video' && post.mediaCount == 1" controls>
                        <source :src="post.media" type="video/mp4">
                        Your browser does not support HTML video.
                    </video>
                </div>
            </div>
            <div class="post-statistics mt-3 d-flex">
                <div class="likes">
                    <div v-if="post.liked">
                        <i @click="toggleLikePost(post.id)" class="fas fa-thumbs-up"></i> <span>likes</span>
                    </div>
                    <div v-else>
                        <i @click="toggleLikePost(post.id)" class="far fa-thumbs-up"></i> <span>likes</span>
                    </div>
                </div>
                <div @click="toggleComments" class="comments">
                    <i class="far fa-comment ml-3"></i> <span> comments</span>
                </div>
                <div class="shares">
                    <i class="fas fa-share ml-3"></i> <span> comments</span>
                </div>
            </div>
            <div v-if="post.viewComments == true" class="post-comment-list mt-2">
                <div class="hide-commnet-list d-flex flex-row-reverse">
                    <span @click="toggleComments"><i class="fas fa-chevron-up"></i> Hide</span>
                </div>
                <comment-component
                    v-for="comment in post.commentsContent"
                    :data="comment">
                </comment-component>
            </div>
            <form class="add-commnet mt-2 d-flex align-items-center">
                <input
                    class="w-100 pl-2"
                    type="text"
                    name="comment"
                    placeholder="Add Your Commnet"
                />
                <div class="d-flex align-items-center pr-3">
                    <i class="fas fa-paperclip" @click="commentAttach(post.id)"></i>
                    <input type="file" :id="'comment-attach-'+post.id" name="img" accept="image/*" />
                </div>
            </form>
            <div class="post-advertise-modal">
                <div
                    class="modal fade"
                    :id="'advertiseModal-'+post.id"
                    tabindex="-1"
                    aria-hidden="true"
                >
                    <div class="modal-dialog" style="margin-top: 10vh">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between">
                                <span></span>
                                <h5 class="modal-title" id="exampleModalLabel">
                                    Advertise Post
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
                            <div class="modal-body pl-5 pr-5">
                                <form action="" class="container">
                                    <p>Select Duration:</p>
                                    <div class="form-group form-check mb-1">
                                        <input
                                            type="radio"
                                            name="duration"
                                            class="form-check-input"
                                            id="exampleCheck1"
                                        />
                                        <label class="form-check-label" for="exampleCheck1"
                                        >3 days</label
                                        >
                                    </div>
                                    <hr class="m-1">
                                    <div class="form-group form-check mb-1">
                                        <input
                                            type="radio"
                                            name="duration"
                                            class="form-check-input"
                                            id="exampleCheck1"
                                        />
                                        <label class="form-check-label" for="exampleCheck1"
                                        >5 days</label
                                        >
                                    </div>
                                    <hr class="m-1">
                                    <div class="form-group form-check">
                                        <input
                                            type="radio"
                                            name="duration"
                                            class="form-check-input"
                                            id="exampleCheck1"
                                        />
                                        <label class="form-check-label" for="exampleCheck1"
                                        >7 days</label
                                        >
                                    </div>
                                    <p>Select Audience:</p>
                                    <div class="form-group form-check mb-1">
                                        <input
                                            type="radio"
                                            name="audience"
                                            class="form-check-input"
                                            id="exampleCheck1"
                                        />
                                        <label class="form-check-label" for="exampleCheck1"
                                        >From 100 To 1000</label
                                        >
                                    </div>
                                    <hr class="m-1">
                                    <div class="form-group form-check mb-1">
                                        <input
                                            type="radio"
                                            name="audience"
                                            class="form-check-input"
                                            id="exampleCheck1"
                                        />
                                        <label class="form-check-label" for="exampleCheck1"
                                        >From 1000 To 2000</label
                                        >
                                    </div>
                                    <hr class="m-1">
                                    <div class="form-group form-check">
                                        <input
                                            type="radio"
                                            name="audience"
                                            class="form-check-input"
                                            id="exampleCheck1"
                                        />
                                        <label class="form-check-label" for="exampleCheck1"
                                        >From 2000 To 5000</label
                                        >
                                    </div>
                                    <div
                                        class="form-group d-flex justify-content-between"
                                    >
                                        <label for="exampleInputEmail1"
                                        >Target Audience:</label
                                        >
                                        <select name="target-audience">
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                    <div
                                        class="form-group d-flex justify-content-between"
                                    >
                                        <label for="exampleInputEmail1">Target Age:</label>
                                        <select name="target-age">
                                            <option value="male">From 20 To 30</option>
                                            <option value="female">From 30 To 40</option>
                                        </select>
                                    </div>
                                    <div
                                        class="form-group d-flex justify-content-between"
                                    >
                                        <label for="exampleInputEmail1"
                                        >Target Countery:</label
                                        >
                                        <select name="target-countery">
                                            <option value="male">Egypt</option>
                                            <option value="female">Saudia</option>
                                        </select>
                                    </div>
                                    <div
                                        class="form-group d-flex justify-content-between"
                                    >
                                        <label for="exampleInputEmail1">Target City:</label>
                                        <select name="target-countery">
                                            <option value="male">Alex</option>
                                            <option value="female">Cairo</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="postId" :value="post.id">
                                    <button type="submit" class="btn btn-warning btn-block">
                                        Submit
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="post-edit-modal">
                <div
                    class="modal fade"
                    :id="'edit-post-modal-'+post.id"
                    tabindex="-1"
                    aria-hidden="true"
                >
                    <div class="modal-dialog" style="margin-top: 22vh">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between">
                                <span></span>
                                <h5 class="modal-title" id="exampleModalLabel">Edit Post</h5>
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
                                    <div v-if="post.postType == 'service'" id="post-type-service-content">
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
                        placeholder="Start Typing..."
                        :value="post.text"
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
            </div>
            <div class="post-delete-modal">
                <div
                    class="modal fade"
                    :id="'delete-post-modal-'+post.id"
                    tabindex="-1"
                    aria-hidden="true"
                >
                    <div class="modal-dialog" style="margin-top: 22vh">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between">
                                <h5 class="modal-title" id="exampleModalLabel">Confirm Delete Post</h5>
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
                                <button
                                    type="button"
                                    class="btn btn-warning btn-block w-100"
                                    data-dismiss="modal"
                                    @click="deletePost(post.id)">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

