<section id="ez-body__right-sidebar" class="col-lg-3 ez-sidebar">
    <ul class="pt-4" id="right-sidebar__items">
        @if(count($related_pages) > 0)
            <li class="mt-3">
                <h6 class="pb-2" style="font-weight: bold;font-size: 15px">{{__('home.expected_pages')}}</h6>
                <div class="suggested-groups">
                    @foreach($related_pages as $page)
                        <div class="group">
                            <a href="{{route('main-page',$page->id)}}">
                                <div class="group-banner">
                                    @if($page->cover_image)
                                        <img
                                            width="100%"
                                            src="{{asset('media')}}/{{$page->profile_image}}"
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
                                        <p id="page-members-{{$page->id}}">{{$page->members}} {{__('home.likes')}}</p>
                                    </div>
                                    <a id="like-page-btn-{{$page->id}}" onclick="likePageSubmit({{$page->id}},'{{App::getlocale()}}')" class="btn btn-warning text-white">{{__('pages.like')}}</a>
                                    <form id="like-page-form-{{$page->id}}" action="{{ route('like_page') }}" method="POST" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="page_id" value="{{$page->id}}">
                                        <input type="hidden" id="like-page-flag-{{$page->id}}" name="flag" value="0">
                                    </form>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </li>
        @endif
    </ul>
</section>
