
@section('pages')

    @if(count($pages) > 0)

        @foreach($pages as $page)
            <div class="post-container bg-white mt-3 p-3" id="searched-pages-{{$page->id}}">
                <div class="post-owner d-flex align-items-center">
                    @if($page->profile_image)
                        <div class="owner-img">
                            <a style="display: inline" href="{{route('main-page',$page->id)}}"><img src="{{asset('media')}}/{{$page->profile_image}}" class="rounded-circle" /></a>
                        </div>
                    @else
                        <div class="owner-img">
                            <a style="display: inline" href="{{route('main-page',$page->id)}}"><img src="{{asset('media')}}/img.jpg" class="rounded-circle" /></a>
                        </div>
                    @endif
                    <div class="owner-name pl-3">
                        <a href="{{route('main-page',$page->id)}}"><b>
                                {{$page->name}}
                            </b></a>

                        <span style="display: block" id="page-members-{{$page->id}}">{{$page->members}} members</span>
                    </div>
                    <div class="post-option ml-auto pr-3">
                        @if($page->liked == 'delete page')
                            <a id="delete-page-btn-{{$page->id}}" onclick="deletePageSubmit({{$page->id}})" class="btn btn-warning text-white">{{$page->liked}}</a>
                            <form id="delete-page-form-{{$page->id}}" action="{{ route('pages.destroy',$page->id) }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <a id="like-page-btn-{{$page->id}}" onclick="likePageSubmit({{$page->id}})" class="btn btn-warning text-white">{{$page->liked}}</a>
                            <form id="like-page-form-{{$page->id}}" action="{{ route('like_page') }}" method="POST" style="display: none;">
                                @csrf
                                <input type="hidden" name="page_id" value="{{$page->id}}">
                                <input type="hidden" id="like-page-flag-{{$page->id}}" name="flag" value="{{$page->flag}}">
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection
