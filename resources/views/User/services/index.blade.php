@extends('layouts.app')

@section('content')
    <section id="ez-body__center-content" class="col-lg-10 mt-3">
        <div class="search-bar d-flex justify-content-center">
            <input type="text" placeholder="{{__('user.search')}}" class="search-input w-75" id="search-categories" onkeyup="searchCategoriesSubmit();"/>
        </div>
        <div class="services-container d-flex flex-wrap mt-3" id="load-categories">
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
            @else
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            {{ __('no categories found!') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

