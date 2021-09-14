
@section('categories')

    @if(count($categories) > 0)

        @foreach($categories as $category)
            <div class="service card m-2">
                <a href="{{route('services',$category->id)}}" style="text-decoration: none">
                    <img
                        src="{{asset('category_images')}}/{{$category->image}}"
                        style="height: 220px"
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
