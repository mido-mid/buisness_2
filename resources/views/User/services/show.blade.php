@extends('layouts.app')

@section('content')
    <section id="ez-body__center-content" class="col-lg-10 mt-3">
        <div class="search-bar d-flex justify-content-center">
            <input class="w-75" type="text" placeholder="Search" />
        </div>
        <div class="services-container d-flex flex-wrap mt-3">
            @if(count($services) > 0)
                @foreach($services as $service)
                    <div class="service card m-2">
                        <img
                            src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                            class="card-img-top"
                            alt="..."
                        />
                        <div class="card-body">
                            <h5 class="card-title">{{$service->body}}</h5>
                            <p class="card-text">{{$service->price}} $</p>
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
