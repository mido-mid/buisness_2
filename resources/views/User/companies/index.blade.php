@extends('layouts.app')

@section('content')
        <section id="ez-body__center-content" class="col-lg-10 mt-3">
            <div class="search-bar d-flex justify-content-center">
                <input class="w-75" type="text" placeholder="Search" />
            </div>
            <div class="services-container d-flex flex-wrap mt-3">
                @foreach($companies as $company)
                    <div class="service card m-2">
                        <img
                            src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                            class="card-img-top"
                            alt="..."
                        />
                        <div class="card-body">
                            <p class="card-text">
                                @if(App::getLocale() == 'ar')
                                    {{$company->name_ar}}
                                @else
                                    {{$company->name_en}}
                                @endif
                            </p>

                            <ul>
                                @foreach($company->phones as $phone)
                                    <li>{{$phone->phoneNumber}}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
@endsection
