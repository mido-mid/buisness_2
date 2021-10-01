@extends('layouts.admin_layout')

@section('content')


    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">{{ __('admin.report_details') }}</h4>
                                <p class="card-title-desc"> {{ __('admin.publisher') }} : {{$model->publisher->name}}</p>
                                <form method="post">
                                    <textarea id="elm1" name="area">{{$model->body}}</textarea>
                                </form>
                                @if($model->publisher->stateId != "banned")
                                    <form action="{{ route('reports.ban', $report) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="ban" value="banned">
                                        <button type="button" class="btn btn-purple" onclick="confirm('{{ __("home.confirm") }}') ? this.parentElement.submit() : ''" style="margin-top: 20px" class="btn btn-purple waves-effect waves-light">{{ __('admin.ban_publisher') }}</button>
                                    </form>
                                @else
                                    <form action="{{ route('reports.ban', $report)}}" method="post">
                                        @csrf
                                        <input type="hidden" name="ban" value="allowed">
                                        <button type="button" class="btn btn-purple" onclick="confirm('{{ __("home.confirm") }}') ? this.parentElement.submit() : ''" style="margin-top: 20px" class="btn btn-purple waves-effect waves-light">{{ __('admin.remove_ban') }}</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->

                @if($model->media != null)
                    <div class="row">

                        @foreach($model->media as $media)
                            <div class="col-lg-3 col-md-6">
                                @if($media->mediaType == "image")
                                    <img src="{{asset('media')}}/{{$media->filename}}" alt="img" class="gallery-thumb-img" style="height: 300px; width: 200px">
                                @else
                                    <video class="p-1" controls class="gallery-thumb-img" style="height: 300px; width: 200px">
                                        <source src="{{asset('media')}}/{{$media->filename}}" type="video/mp4">
                                        {{ __('home.no_browser') }}
                                    </video>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

            </div> <!-- container-fluid -->
        </div>

@endsection
