
@section('categories')

    @if(count($categories) > 0)

        @foreach($categories as $category)
            <div class="service card m-2">
                <a href="{{route('services',$category->id)}}" style="text-decoration: none">
                    <img
                        src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                        class="card-img-top"
                        alt="..."
                    />
                    <div class="card-body">
                        <p class="card-text">
                            @if(App::getLocale() == 'ar')
                                {{$category->name_ar}}
                            @else
                                {{$category->name_en}}
                            @endif
                        </p>
                    </div>
                </a>
            </div>
        @endforeach
    @endif
@endsection
