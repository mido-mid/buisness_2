@extends('layouts.app')

@section('content')
     <section id="ez-body__center-content" class="col-lg-8 mt-3">
        <div class="search-bar">
            <input type="text" placeholder="Search" id="search-companies" onkeyup="searchCompaniesSubmit();"/>
        </div>
         <div id="load-companies">
            @foreach($companies as $company)
                <div class="trade-div">
                    <div class="row">
                        <div class="col-md-3 col-12">
                            @if($company->image == null)
                                <img src="{{asset('media')}}/images.png">
                            @else
                                <img src="{{asset('media')}}/{{$company->image}}">
                            @endif
                        </div>
                        <div class="col-md-9 col-12 row">
                            <h2 class="col-12">
                                @if(App::getlocale() == 'en')
                                    {{$company->name_en}}
                                @else
                                    {{$company->name_ar}}
                                @endif
                            </h2>
                            @foreach($company->phones as $phone)
                                <p class="col-sm-3 col-6">{{$phone->phoneNumber}}</p>
                                <span  class="col-sm-3 col-6 ">
                                  <p class="call-icon">
                                      <i class="fas fa-mobile-alt"></i> Call
                                  </p>
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
         </div>
    </section>
@endsection
