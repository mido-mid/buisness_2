<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ asset('css') }}/bootstrap.min.css">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css" rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"
    />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.css" integrity="sha512-vEia6TQGr3FqC6h55/NdU3QSM5XR6HSl5fW71QTKrgeER98LIMGwymBVM867C1XHIkYD9nMTfWK2A0xcodKHNA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Styles -->
    <link href="{{ asset('css/styles/style-mar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles/styleEn.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles/style.css') }}" rel="stylesheet">

    @if(App::getLocale() == 'ar')
        <link href="{{ asset('css/styles/style.css') }}" rel="stylesheet">
        <link href="{{ asset('css/styles/style-mar.css') }}" rel="stylesheet">
        <link href="{{ asset('css/styles/style-rtl.css') }}" rel="stylesheet">
    @endif
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark ez-navbar sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand p-2" href="{{route('welcome')}}">Business</a>
            <div class="right-side d-flex flex-row">
                <a class="d-none d-lg-block mr-4" href="#"><i class="fas fa-headset p"></i>{{__('user.support')}}</a>
                <a class="dropdown-toggle d-none d-lg-block" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-language"></i>{{__('user.language')}}
                </a>
                <ul class="dropdown-menu rounded-5" aria-labelledby="navbarDropdown">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <li>
                            <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"> {{ $properties['native'] }}
                                <span class="sr-only">(current)</span>
                            </a>
                        </li>
                    @endforeach
                    <li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a onclick="event.preventDefault();
                              document.getElementById('logout-form').submit();" href="{{ route('logout') }}"><span>{{__('logout')}}</span></a>
                    </li>
                </ul>
                <button class="navbar-toggler" type="button" onclick="openSidenav()">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>
    </nav>
    <div class="w3-sidebar w3-bar-block" id="sideNavbar">
        <div class="d-flex flex-row-reverse justify-content-between">
            <div class="w-25 d-flex flex-row-reverse">
                <button class="close-sidebar btn btn-danger p-3 m-3" onclick="closeSidenav()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <ul class="w-75 navbar-nav me-auto mb-2 mb-lg-0 p-3">
                <li>
                    <a href="{{route('user.view.profile',['user_id'=>auth()->user()->id])}}"><img class="profile-figure"
                                                src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                                alt="My Profile Pic" />
                        <span>{{ \Str::limit(auth()->user()->name, 10) }}</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="{{route('home')}}"><i class="fas fa-home"></i>{{__('user.home')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('service_categories')}}"><i class="fas fa-hand-holding-usd"></i>{{__('user.services')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('all-group')}}"><i class="fas fa-users"></i>{{__('user.groups')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('companies.index')}}"><i class="fas fa-truck"></i>{{__('user.delivery')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('all-page')}}"><i class="far fa-copy"></i>{{__('user.pages')}}</a>
                </li>
                <li class="nav-item" style="display: flex; align-items: center">
                    <a class="nav-link active" href="#"><i class="fas fa-bell"></i>{{__('user.notifications')}}</a>
                    <span class="badge bg-warning text-dark">2</span>
                </li>
                <li class="nav-item" style="display: flex; align-items: center">
                    <a class="nav-link active" href="#"><i class="fas fa-comment-dots"></i>{{__('user.messages')}}</a>
                    <span class="badge bg-warning text-dark">3</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('saved_posts')}}}"><i class="fas fa-bookmark"></i>{{__('user.saved_posts')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#"><i class="fas fa-headset"></i>{{__('user.support')}}</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-language"></i> Language
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <li>
                                <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"> {{ $properties['native'] }}
                                    <span class="sr-only">(current)</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                @if(auth()->user()->type == 1)
                    <li class="nav-item">
                        <a class="nav-link active" href="{{url('admin/dashboard')}}"><i class="fas fa-users-cog"></i>{{__('user.dashboard')}}</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    <section class="container-fluid ez-main">
        <section class="ez-body row">
            <section id="ez-body__left-sidebar" class="col-lg-2 ez-sidebar">
                <ul id="left-sidebar__items">
                    <li class="mt-2">
                        <a href="{{route('user.view.profile',['user_id'=>auth()->user()->id])}}"><img class="profile-figure"
                                                    src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                                    alt="My Profile Pic" />
                            <span>{{ \Str::limit(auth()->user()->name, 10) }}</span></a>
                    </li>
                    <li>
                        <a aria-current="page" href="{{route('home')}}"><i class="fas fa-home p"></i>{{__('user.home')}}</a>
                    </li>
                    <li>
                        <a href="{{route('service_categories')}}"><i class="fas fa-hand-holding-usd p"></i>{{__('user.services')}}</a>
                    </li>
                    <li>
                        <a href="{{route('all-group')}}"><i class="fas fa-users p"></i>{{__('user.groups')}}</a>
                    </li>
                    <li>
                        <a href="{{route('companies.index')}}"><i class="fas fa-truck p"></i>{{__('user.delivery')}}</a>
                    </li>
                    <li>
                        <a href="{{route('all-page')}}"><i class="far fa-copy p"></i>{{__('user.pages')}}</a>
                    </li>
                    <li style="
                display: flex;
                justify-content: space-between;
                align-items: center;
              ">
                        <a href="#"><i class="fas fa-bell p"></i>{{__('user.notifications')}}</a><span class="badge bg-warning text-dark">2</span>
                    </li>
                    <li style="
                display: flex;
                justify-content: space-between;
                align-items: center;
              ">
                        <a href="#"><i class="fas fa-comment-dots p"></i>{{__('user.messages')}}</a>
                        <span class="badge bg-warning text-dark">3</span>
                    </li>
                    <li>
                        <a href="{{route('saved_posts')}}"><i class="fas fa-bookmark p"></i>{{__('user.saved_posts')}}</a>
                    </li>
                    @if(auth()->user()->type == 1)
                        <li class="nav-item">
                            <a href="{{url('admin/dashboard')}}"><i class="fas fa-users-cog"></i>{{__('user.dashboard')}}</a>
                        </li>
                    @endif
                </ul>
            </section>

            @yield('content')

        </section>
    </section>

    <script src="{{ asset('assets') }}/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets') }}/libs/jquery/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/index.js') }}"></script>
    <script src="{{ asset('js/vue.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@3.0.3/dist/index.min.js"></script>
    <script>
        const button = document.querySelector('#emoji-button');

        const picker = new EmojiButton();

        button.addEventListener('click', () => {
            picker.pickerVisible ? picker.hidePicker : picker.showPicker(button);

        });

        picker.on('emoji', emoji => {
            document.querySelector('textarea').value += emoji;
        });
    </script>
    <script>
        window.users = @json(['user' => $friends_mention]);
    </script>
    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function(){
            var limit = 5;
            var start = 5;
            var action = 'inactive';
            function loadData(limit, start)
            {
                $.ajax({
                    url:"loadmore/"+limit+'/'+start,
                    type: 'GET',
                    cache: false,
                    processData: false,
                    contentType: false,
                    success:function(data)
                    {
                        $('#load_data').append(data);
                        if(data.msg == "end")
                        {
                            $('#load_data_message').html("<div style='width: 100%;background:#fff;border-radius: 8px;padding:1px;margin-top: 10px;'><p style='text-align: center;font-weight: bold;'>End</p></div>'");
                            action = 'active';
                        }
                        else
                        {
                            $('#load_data_message').html("<div style='width: 100%;background:#fff;border-radius: 8px;padding:1px;margin-top: 10px;'><p style='text-align: center;font-weight: bold;'>Loading</p></div>'");
                            action = "inactive";
                        }

                    },
                    error: function (data) {
                        console.log(data.responseText);
                    }
                });
            }

            if(action == 'inactive')
            {
                action = 'active';
                loadData(limit, start);
            }
            $(window).scroll(function(){
                if($(window).scrollTop() + $(window).height() > $("#load_data").height() && action == 'inactive')
                {
                    action = 'active';
                    start = start + limit;
                    setTimeout(function(){
                        loadData(limit, start);
                    }, 1000);
                }
            });
        });
    </script>
    <script>
        $(document).ready(function(){
            var limit = 5;
            var start = 0;
            var action = 'active';
            function loadData(limit, start)
            {
                $.ajax({
                    url:"loadstories/"+limit+'/'+start,
                    type: 'GET',
                    cache: false,
                    processData: false,
                    contentType: false,
                    success:function(data)
                    {
                        $('#stories-scroll').append(data);

                        if(data.msg == "end"){
                            action = "inactive";
                        }
                        else{
                            action = "active";
                        }
                    },
                    error: function (data) {
                        console.log(data.responseText);
                    }
                });
            }
            $('#stories-scroll').scroll( function() {
                var $width = $('#stories-scroll').outerWidth()
                var $scrollWidth = $('#stories-scroll')[0].scrollWidth;
                var $scrollLeft = $('#stories-scroll').scrollLeft();

                if (parseInt($scrollWidth - $width) <= parseInt($scrollLeft)) {
                    action = 'active';
                    start = start + limit;
                    setTimeout(function(){
                        loadData(limit, start);
                    }, 1000);
                }
            });
        });
    </script>
    <script>
        $( document ).ready(function() {
            // $('.carousel').carousel({ interval: 4000 });
            $('.carousel').on('slid.bs.carousel', function() {
                var active_id = $('.carousel div.carousel-item.active.current').attr('id');
                var story_id = active_id.split('-')[2];

                // if($('.carousel div.carousel-item.active.current').attr('data-type') == 'video') {

                    var myEle = document.getElementById("story-video-" + story_id);
                    if(myEle){
                        $("#story-video-" + story_id)[0].play();
                    }
                    else{
                        $("video")[0].pause();
                    }
                // }

                $.ajax({
                    url: $('#view-story-form-' + story_id).attr('action'),
                    type: 'POST',
                    data: new FormData(document.getElementById("view-story-form-" + story_id)),
                    dataType: 'JSON',
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        console.log(data);
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function(){
            $('.totyAllgroups').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Group_id = splittable[1];
                var User_id = {{auth::user()->id}};
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/join-group',
                    method:"get",
                    data:{requestType:RequestType,group_id:Group_id, user_id:User_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        var str = data.split('|');
                        var relatedId = document.getElementById(RequestType+'|'+Group_id+'|1');
                        var grooupId = document.getElementById(RequestType+'|'+Group_id+'|0');

                        if(str[0] == 1)
                        {
                            if(grooupId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|0').textContent = "{{__('groups.left')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|0').id = 'leave|'+str[1]+'|0';
                                document.getElementById(str[1]+'|0').textContent = str[2];
                            }

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|1').textContent = "{{__('groups.left')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|1').id = 'leave|'+str[1]+'|1';
                                document.getElementById(str[1]+'|1').textContent = str[2];
                            }
                        }
                        if(str[0] == 2)
                        {
                            if(grooupId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|0').textContent = "{{__('groups.left_request')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|0').id = 'leave|'+str[1]+'|0';
                                document.getElementById(str[1]+'|0').textContent = str[2];
                            }

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|1').textContent = "{{__('groups.left_request')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|1').id = 'leave|'+str[1]+'|1';
                                document.getElementById(str[1]+'|1').textContent = str[2];
                            }
                        }
                        if(str[0] == 0)
                        {
                            if(grooupId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|0').textContent = "{{__('groups.join')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.remove("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.add("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|0').id = 'join|'+str[1]+'|0';
                                document.getElementById(str[1]+'|0').textContent = str[2];
                            }

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|1').textContent = "{{__('groups.join')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.remove("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.add("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|1').id = 'join|'+str[1]+'|1';
                                document.getElementById(str[1]+'|1').textContent = str[2];
                            }
                        }

                        // alert(data);
                    }
                });

            });
        });
    </script>
    <script>
        $(document).ready(function(){
            $('.totyMygroups').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Group_id = splittable[1];
                var User_id = {{auth::user()->id}};
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/join-group',
                    method:"get",
                    data:{requestType:RequestType,group_id:Group_id, user_id:User_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        var str = data.split('|');
                        var relatedId = document.getElementById(RequestType+'|'+Group_id+'|1');
                        var grooupId = document.getElementById(RequestType+'|'+Group_id+'|0');

                        if(str[0] == 1)
                        {
                            if(grooupId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|0').textContent = "{{__('groups.left')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|0').id = 'leave|'+str[1]+'|0';
                                document.getElementById(str[1]+'|0').textContent = str[2];
                            }

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|1').textContent = "{{__('groups.left')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|1').id = 'leave|'+str[1]+'|1';
                                document.getElementById(str[1]+'|1').textContent = str[2];
                            }
                        }
                        if(str[0] == 2)
                        {
                            if(grooupId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|0').textContent = "{{__('groups.left_request')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|0').id = 'leave|'+str[1]+'|0';
                                document.getElementById(str[1]+'|0').textContent = str[2];
                            }

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|1').textContent = "{{__('groups.left_request')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|1').id = 'leave|'+str[1]+'|1';
                                document.getElementById(str[1]+'|1').textContent = str[2];
                            }
                        }
                        if(str[0] == 0)
                        {
                            if(grooupId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|0').textContent = "{{__('groups.join')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.remove("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.add("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|0').id = 'join|'+str[1]+'|0';
                                document.getElementById(str[1]+'|0').style.display = 'none';
                            }

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|1').textContent = "{{__('groups.join')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.remove("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.add("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|1').id = 'join|'+str[1]+'|1';
                                document.getElementById(str[1]+'|1').style.display = 'none';
                            }
                        }

                        // alert(data);
                    }
                });

            });
        });
    </script>
    <script>
        $(document).ready(function(){
            $('.totyAllpages').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Page_id = splittable[1];
                var User_id = {{auth::user()->id}};
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/join-page',
                    method:"get",
                    data:{requestType:RequestType,page_id:Page_id, user_id:User_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        var str = data.split('|');
                        var relatedId = document.getElementById(RequestType+'|'+Page_id);
                        if(str[0] == 1)
                        {
                            document.getElementById(RequestType+'|'+Page_id+'|0').textContent = "{{__('pages.dislike')}}";
                            document.getElementById(RequestType+'|'+Page_id+'|0').classList.remove("button-4");
                            document.getElementById(RequestType+'|'+Page_id+'|0').classList.add("button-2");
                            document.getElementById(RequestType+'|'+Page_id+'|0').id = 'leave|'+str[1]+'|0';
                            document.getElementById(str[1]+'|0').textContent = str[2];

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Page_id).textContent = "{{__('pages.dislike')}}";
                                document.getElementById(RequestType+'|'+Page_id).classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Page_id).classList.add("button-2");
                                document.getElementById(RequestType+'|'+Page_id).id = 'leave|'+str[1];
                                document.getElementById(str[1]).textContent = str[2];
                            }
                        }
                        if(str[0] == 2)
                        {
                            document.getElementById(RequestType+'|'+Page_id+'|0').textContent = "{{__('pages.dislike_request')}}";
                            document.getElementById(RequestType+'|'+Page_id+'|0').classList.remove("button-4");
                            document.getElementById(RequestType+'|'+Page_id+'|0').classList.add("button-2");
                            document.getElementById(RequestType+'|'+Page_id+'|0').id = 'leave|'+str[1]+'|0';
                            document.getElementById(str[1]+'|0').textContent = str[2];

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Page_id).textContent = "{{__('pages.dislike_request')}}";
                                document.getElementById(RequestType+'|'+Page_id).classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Page_id).classList.add("button-2");
                                document.getElementById(RequestType+'|'+Page_id).id = 'leave|'+str[1];
                                document.getElementById(str[1]).textContent = str[2];
                            }
                        }
                        if(str[0] == 0)
                        {
                            document.getElementById(RequestType+'|'+Page_id+'|0').textContent = "{{__('pages.like')}}";
                            document.getElementById(RequestType+'|'+Page_id+'|0').classList.remove("button-2");
                            document.getElementById(RequestType+'|'+Page_id+'|0').classList.add("button-4");
                            document.getElementById(RequestType+'|'+Page_id+'|0').id = 'join|'+str[1]+'|0';
                            document.getElementById(str[1]+'|0').textContent = str[2];

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Page_id).textContent = "{{__('pages.like')}}";
                                document.getElementById(RequestType+'|'+Page_id).classList.remove("button-2");
                                document.getElementById(RequestType+'|'+Page_id).classList.add("button-4");
                                document.getElementById(RequestType+'|'+Page_id).id = 'join|'+str[1];
                                document.getElementById(str[1]).textContent = str[2];
                            }
                        }


                        // alert(data);
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('.totyMypages').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Page_id = splittable[1];
                var User_id = {{auth::user()->id}};
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/join-page',
                    method:"get",
                    data:{requestType:RequestType,page_id:Page_id, user_id:User_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        var str = data.split('|');
                        var relatedId = document.getElementById(RequestType+'|'+Page_id);
                        var grooupId = document.getElementById(RequestType+'|'+Page_id+'|0');

                        if(str[0] == 1)
                        {
                            if(grooupId)
                            {
                                document.getElementById(RequestType+'|'+Page_id+'|0').textContent = "{{__('pages.dislike')}}";
                                document.getElementById(RequestType+'|'+Page_id+'|0').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Page_id+'|0').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Page_id+'|0').id = 'leave|'+str[1]+'|0';
                                document.getElementById(str[1]+'|0').textContent = str[2];
                            }

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Page_id).textContent = "{{__('pages.dislike')}}";
                                document.getElementById(RequestType+'|'+Page_id).classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Page_id).classList.add("button-2");
                                document.getElementById(RequestType+'|'+Page_id).id = 'leave|'+str[1];
                                document.getElementById(str[1]).textContent = str[2];
                            }
                        }
                        if(str[0] == 2)
                        {
                            if(grooupId)
                            {
                                document.getElementById(RequestType+'|'+Page_id+'|0').textContent = "{{__('pages.dislike_request')}}";
                                document.getElementById(RequestType+'|'+Page_id+'|0').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Page_id+'|0').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Page_id+'|0').id = 'leave|'+str[1]+'|0';
                                document.getElementById(str[1]+'|0').textContent = str[2];
                            }

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Page_id).textContent = "{{__('pages.dislike_request')}}";
                                document.getElementById(RequestType+'|'+Page_id).classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Page_id).classList.add("button-2");
                                document.getElementById(RequestType+'|'+Page_id).id = 'leave|'+str[1];
                                document.getElementById(str[1]).textContent = str[2];
                            }
                        }
                        if(str[0] == 0)
                        {
                            if(grooupId)
                            {
                                document.getElementById(RequestType+'|'+Page_id+'|0').textContent = "{{__('pages.like')}}";
                                document.getElementById(RequestType+'|'+Page_id+'|0').classList.remove("button-2");
                                document.getElementById(RequestType+'|'+Page_id+'|0').classList.add("button-4");
                                document.getElementById(RequestType+'|'+Page_id+'|0').id = 'join|'+str[1]+'|0';
                                document.getElementById(str[1]+'|0').style.display = 'none';
                            }

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Page_id).textContent = "{{__('pages.like')}}";
                                document.getElementById(RequestType+'|'+Page_id).classList.remove("button-2");
                                document.getElementById(RequestType+'|'+Page_id).classList.add("button-4");
                                document.getElementById(RequestType+'|'+Page_id).id = 'join|'+str[1];
                                document.getElementById(str[1]).style.display = 'none';
                            }
                        }

                        // alert(data);
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('.totyPage').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Page_id = splittable[1];
                var User_id = {{auth::user()->id}};
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/join-page',
                    method:"get",
                    data:{requestType:RequestType,page_id:Page_id, user_id:User_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        var str = data.split('|');
                        if(str[0] == 1)
                        {
                            document.getElementById(id).textContent = "{{__('pages.dislike')}}";
                            document.getElementById(id).classList.remove("button-4");
                            document.getElementById(id).classList.add("button-2");
                            document.getElementById(id).id = 'leave|'+str[1]+'|2';
                            document.getElementById(str[1]).textContent = str[2];
                        }
                        if(str[0] == 2)
                        {
                            document.getElementById(id).textContent = "{{__('pages.dislike_request')}}";
                            document.getElementById(id).classList.remove("button-4");
                            document.getElementById(id).classList.add("button-2");
                            document.getElementById(id).id = 'leave|'+str[1]+'|2';
                            document.getElementById(str[1]).textContent = str[2];
                        }
                        if(str[0] == 0)
                        {
                            document.getElementById(id).textContent = "{{__('pages.like')}}";
                            document.getElementById(id).classList.remove("button-2");
                            document.getElementById(id).classList.add("button-4");
                            document.getElementById(id).id = 'join|'+str[1]+'|2';
                            document.getElementById(str[1]).textContent = str[2];
                        }

                        // alert(data);
                    }
                });

            });
        });
    </script>
    <script>
        $(document).ready(function(){
            $('.toty2').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Group_id = splittable[1];
                var User_id = {{auth::user()->id}};
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/join-group',
                    method:"get",
                    data:{requestType:RequestType,group_id:Group_id, user_id:User_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        var str = data.split('|');
                        if(str[0] == 1)
                        {
                            document.getElementById(id).textContent = "{{__('groups.left')}}";
                            document.getElementById(id).classList.remove("button-4");
                            document.getElementById(id).classList.add("button-2");
                            document.getElementById(id).id = 'leave|'+str[1]+'|2';
                            document.getElementById(str[1]).textContent = str[2];
                        }
                        if(str[0] == 2)
                        {
                            document.getElementById(id).textContent = "{{__('groups.left_request')}}";
                            document.getElementById(id).classList.remove("button-4");
                            document.getElementById(id).classList.add("button-2");
                            document.getElementById(id).id = 'leave|'+str[1]+'|2';
                            document.getElementById(str[1]).textContent = str[2];
                        }
                        if(str[0] == 0)
                        {
                            document.getElementById(id).textContent = "{{__('groups.join')}}";
                            document.getElementById(id).classList.remove("button-2");
                            document.getElementById(id).classList.add("button-4");
                            document.getElementById(id).id = 'join|'+str[1]+'|2';
                            document.getElementById(str[1]).textContent = str[2];
                        }

                        // alert(data);
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('.toty').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Group_id = splittable[1];
                var User_id = {{auth::user()->id}};
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/join-group',
                    method:"get",
                    data:{requestType:RequestType,group_id:Group_id, user_id:User_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        var str = data.split('|');
                        var relatedId = document.getElementById(RequestType+'|'+Group_id+'|1');
                        var grooupId = document.getElementById(RequestType+'|'+Group_id+'|0');

                        if(str[0] == 1)
                        {
                            if(grooupId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|0').textContent = "{{__('groups.left')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|0').id = 'leave|'+str[1]+'|0';
                                document.getElementById(str[1]+'|0').textContent = str[2];
                            }

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|1').textContent = "{{__('groups.left')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|1').id = 'leave|'+str[1]+'|1';
                                document.getElementById(str[1]+'|1').textContent = str[2];
                            }
                        }
                        if(str[0] == 2)
                        {
                            if(grooupId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|0').textContent = "{{__('groups.left_request')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|0').id = 'leave|'+str[1]+'|0';
                                document.getElementById(str[1]+'|0').textContent = str[2];
                            }

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|1').textContent = "{{__('groups.left_request')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.remove("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.add("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|1').id = 'leave|'+str[1]+'|1';
                                document.getElementById(str[1]+'|1').textContent = str[2];
                            }
                        }
                        if(str[0] == 0)
                        {
                            if(grooupId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|0').textContent = "{{__('groups.join')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.remove("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|0').classList.add("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|0').id = 'join|'+str[1]+'|0';
                                document.getElementById(str[1]+'|0').textContent = str[2];
                            }

                            if(relatedId)
                            {
                                document.getElementById(RequestType+'|'+Group_id+'|1').textContent = "{{__('groups.join')}}";
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.remove("button-2");
                                document.getElementById(RequestType+'|'+Group_id+'|1').classList.add("button-4");
                                document.getElementById(RequestType+'|'+Group_id+'|1').id = 'join|'+str[1]+'|1';
                                document.getElementById(str[1]+'|1').textContent = str[2];
                            }
                        }

                        // alert(data);
                    }
                });

            });
        });
    </script>
    <script>
        $(document).ready(function(){
            $('.totyFrientshep').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Enemy_id = splittable[1];
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/frientshep-group',
                    method:"get",
                    data:{requestType:RequestType,enemy_id:Enemy_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        var str = data.split('|');
                        var x = 'addFollowing|'+str[1];
                        console.log(x);
                        if(str[0] == 2)
                        {
                            document.getElementById(id).textContent = "{{__('groups.un_friend')}}";
                            document.getElementById(id).classList.remove("button-4");
                            document.getElementById(id).classList.add("button-2");
                            document.getElementById(id).id = 'remove|'+str[1];
                            document.getElementById(str[1]).textContent = str[2] + " {{__('groups.follower')}} ";

                            document.getElementById(x).textContent = "{{__('groups.un_following')}}";
                            document.getElementById(x).classList.remove("button-4");
                            document.getElementById(x).classList.add("button-2");
                            document.getElementById(x).id = 'removeFollowing|'+str[1];
                        }
                        if(str[0] == 3)
                        {
                            document.getElementById(id).textContent = "{{__('groups.un_friend_request')}}";
                            document.getElementById(id).classList.remove("button-4");
                            document.getElementById(id).classList.add("button-2");
                            document.getElementById(id).id = 'remove|'+str[1];
                            document.getElementById(str[1]).textContent = str[2] + " {{__('groups.follower')}} ";

                            document.getElementById(x).textContent = "{{__('groups.un_following')}}";
                            document.getElementById(x).classList.remove("button-4");
                            document.getElementById(x).classList.add("button-2");
                            document.getElementById(x).id = 'removeFollowing|'+str[1];
                        }
                        if(str[0] == 0)
                        {
                            document.getElementById(id).textContent = "{{__('groups.add_friend')}}";
                            document.getElementById(id).classList.remove("button-2");
                            document.getElementById(id).classList.add("button-4");
                            document.getElementById(id).id = 'add|'+str[1];
                            document.getElementById(str[1]).textContent = str[2] + " {{__('groups.follower')}} ";

                            document.getElementById('removeFollowing|'+str[1]).textContent = "{{__('groups.add_following')}}";
                            document.getElementById('removeFollowing|'+str[1]).classList.remove("button-2");
                            document.getElementById('removeFollowing|'+str[1]).classList.add("button-4");
                            document.getElementById('removeFollowing|'+str[1]).id = 'addFollowing|'+str[1];
                        }
                    }
                });

            });

            $('.totyFollowing').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Enemy_id = splittable[1];
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/following-group',
                    method:"get",
                    data:{requestType:RequestType,enemy_id:Enemy_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        var str = data.split('|');
                        if(str[0] == 1)
                        {
                            document.getElementById(id).textContent = "{{__('groups.un_following')}}";
                            document.getElementById(id).classList.remove("button-4");
                            document.getElementById(id).classList.add("button-2");
                            document.getElementById(id).id = 'removeFollowing|'+str[1];
                            document.getElementById(str[1]).textContent = str[2] + "{{__('groups.follower')}}";
                        }

                        if(str[0] == 0)
                        {
                            document.getElementById(id).textContent =  "{{__('groups.add_following')}}";
                            document.getElementById(id).classList.remove("button-2");
                            document.getElementById(id).classList.add("button-4");
                            document.getElementById(id).id = 'addFollowing|'+str[1];
                            document.getElementById(str[1]).textContent = str[2] + "{{__('groups.follower')}}";
                        }
                        //  alert(data);
                    }
                });

            });

            $('.totyAdmin').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Enemy_id = splittable[1];
                var Group_id = splittable[2];
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/asignAdmin-group',
                    method:"get",
                    data:{requestType:RequestType,enemy_id:Enemy_id,group_id:Group_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        var str = data.split('|');
                        if(str[0] == 1)
                        {
                            // var sad =  `<li class='members-item'>` + document.getElementById(Enemy_id+'|'+Group_id).innerHTML + `</li>`;
                            // console.log(sad);
                            // document.getElementById('adddmin').innerHTML +=  document.getElementById(Enemy_id+'|'+Group_id).innerHTML ;
                            // document.getElementById(Enemy_id+'|'+Group_id).style.display = "none";
                            document.getElementById(id).textContent =  "{{__('groups.admin')}}";
                            document.getElementById(id).classList.remove("button-4");
                            document.getElementById(id).classList.add("button-2");
                            // document.getElementById('addAdmin|'+Enemy_id+'|'+Group_id).style.display = "none";
                            document.getElementById('removeMember|'+Enemy_id+'|'+Group_id).style.display = "none";

                        }

                        if(str[0] == 0)
                        {
                            document.getElementById(Enemy_id+'|'+Group_id).style.display = "none";
                        }

                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('.totyRequestgroup').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Request_id = splittable[1];
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/changeRequest-group',
                    method:"get",
                    data:{requestType:RequestType,request_id:Request_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        document.getElementById(data).style.display = "none";
                    }
                });

            });
        });
    </script>
    {{-- page member --}}
    <script>
        $(document).ready(function(){
            $('.totyFrientshepPage').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Enemy_id = splittable[1];
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/frientshep-page',
                    method:"get",
                    data:{requestType:RequestType,enemy_id:Enemy_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        var str = data.split('|');
                        var x = 'addFollowing|'+str[1];
                        console.log(x);
                        if(str[0] == 2)
                        {
                            document.getElementById(id).textContent = "{{__('pages.un_friend')}}";
                            document.getElementById(id).classList.remove("button-4");
                            document.getElementById(id).classList.add("button-2");
                            document.getElementById(id).id = 'remove|'+str[1];
                            document.getElementById(str[1]).textContent = str[2] + " {{__('pages.follower')}} ";

                            document.getElementById(x).textContent = "{{__('pages.un_following')}}";
                            document.getElementById(x).classList.remove("button-4");
                            document.getElementById(x).classList.add("button-2");
                            document.getElementById(x).id = 'removeFollowing|'+str[1];
                        }
                        if(str[0] == 3)
                        {
                            document.getElementById(id).textContent = "{{__('pages.un_friend_request')}}";
                            document.getElementById(id).classList.remove("button-4");
                            document.getElementById(id).classList.add("button-2");
                            document.getElementById(id).id = 'remove|'+str[1];
                            document.getElementById(str[1]).textContent = str[2] + " {{__('pages.follower')}} ";

                            document.getElementById(x).textContent = "{{__('pages.un_following')}}";
                            document.getElementById(x).classList.remove("button-4");
                            document.getElementById(x).classList.add("button-2");
                            document.getElementById(x).id = 'removeFollowing|'+str[1];
                        }
                        if(str[0] == 0)
                        {
                            document.getElementById(id).textContent = "{{__('pages.add_friend')}}";
                            document.getElementById(id).classList.remove("button-2");
                            document.getElementById(id).classList.add("button-4");
                            document.getElementById(id).id = 'add|'+str[1];
                            document.getElementById(str[1]).textContent = str[2] + "{{__('pages.follower')}}";

                            document.getElementById('removeFollowing|'+str[1]).textContent = "{{__('pages.add_following')}}";
                            document.getElementById('removeFollowing|'+str[1]).classList.remove("button-2");
                            document.getElementById('removeFollowing|'+str[1]).classList.add("button-4");
                            document.getElementById('removeFollowing|'+str[1]).id = 'addFollowing|'+str[1];
                        }
                    }
                });

            });

            $('.totyFollowingPage').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Enemy_id = splittable[1];
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/following-page',
                    method:"get",
                    data:{requestType:RequestType,enemy_id:Enemy_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        var str = data.split('|');
                        if(str[0] == 1)
                        {
                            document.getElementById(id).textContent = "{{__('pages.un_following')}}";
                            document.getElementById(id).classList.remove("button-4");
                            document.getElementById(id).classList.add("button-2");
                            document.getElementById(id).id = 'removeFollowing|'+str[1];
                            document.getElementById(str[1]).textContent = str[2] + "{{__('pages.follower')}}";
                        }

                        if(str[0] == 0)
                        {
                            document.getElementById(id).textContent = "{{__('pages.add_following')}}";
                            document.getElementById(id).classList.remove("button-2");
                            document.getElementById(id).classList.add("button-4");
                            document.getElementById(id).id = 'addFollowing|'+str[1];
                            document.getElementById(str[1]).textContent = str[2] + "{{__('pages.follower')}}";
                        }
                        //  alert(data);
                    }
                });

            });

            $('.totyAdminPage').click(function(event){
                event.preventDefault();
                var id = $(this).attr('id');
                var splittable = id.split('|');
                var RequestType = splittable[0];
                var Enemy_id = splittable[1];
                var Page_id = splittable[2];
                console.log(RequestType);
                $.ajax({
                    url:'http://127.0.0.1:8000/asignAdmin-page',
                    method:"get",
                    data:{requestType:RequestType,enemy_id:Enemy_id,page_id:Page_id},
                    dataType:"text",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(data){
                        var str = data.split('|');
                        if(str[0] == 1)
                        {
                            // var sad =  `<li class='members-item'>` + document.getElementById(Enemy_id+'|'+Group_id).innerHTML + `</li>`;
                            // console.log(sad);
                            // document.getElementById('adddmin').innerHTML +=  document.getElementById(Enemy_id+'|'+Group_id).innerHTML ;
                            // document.getElementById(Enemy_id+'|'+Group_id).style.display = "none";
                            document.getElementById(id).textContent = "{{__('pages.admin')}}";
                            document.getElementById(id).classList.remove("button-4");
                            document.getElementById(id).classList.add("button-2");
                            // document.getElementById('addAdmin|'+Enemy_id+'|'+Group_id).style.display = "none";
                            document.getElementById('removeMember|'+Enemy_id+'|'+Page_id).style.display = "none";

                        }

                        if(str[0] == 0)
                        {
                            document.getElementById(Enemy_id+'|'+Page_id).style.display = "none";
                        }

                    }
                });

            });
        });
    </script>

    <script>
        function mysearchtoty() {
            var input = document.getElementById("search");
            var filter = input.value.toLowerCase();
            var nodes = document.getElementsByClassName('target');

            for (i = 0; i < nodes.length; i++) {
                if (nodes[i].innerText.toLowerCase().includes(filter)) {
                    nodes[i].style.display = "block";
                } else {
                    nodes[i].style.display = "none";
                }
            }
        }

    </script>
    <script src="https://www.dukelearntoprogram.com/course1/common/js/image/SimpleImage.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
        });
    </script>
    <!-- FONT AWESOME -->
    <script src="https://kit.fontawesome.com/5d2df7d4f7.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('js/script_mar.js') }}"></script>

</body>
</html>
