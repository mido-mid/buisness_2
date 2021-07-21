@extends('layouts.admin_layout')

@section('content')


    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Reported Model Info</h4>
                                <p class="card-title-desc"> publisher : {{$model->publisher->name}}</p>
                                <textarea id="elm1" name="area">
                                    {{$model->body}}
                                </textarea>
                                @if($model->publisher->state != "banned")
                                    <form action="{{ route('reports.ban', $model->publisher->id) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="ban" value="banned">
                                        <button type="button" class="btn btn-purple" onclick="confirm('{{ __("Are you sure you want to ban that user?") }}') ? this.parentElement.submit() : ''" style="margin-top: 20px" class="btn btn-purple waves-effect waves-light">Ban publisher</button>
                                    </form>
                                @else
                                    <form action="{{ route('reports.ban', $model->publisher->id) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="ban" value="allowed">
                                        <button type="button" class="btn btn-purple" onclick="confirm('{{ __("Are you sure you want to remove the ban?") }}') ? this.parentElement.submit() : ''" style="margin-top: 20px" class="btn btn-purple waves-effect waves-light">Remove Ban</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->

                @if(count($model->media) > 0)

                    @foreach($model->media as $media)

                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <a href="assets/images/gallery/work-1.jpg" class="gallery-popup" title="Open Imagination">
                                    <div class="project-item">
                                        <div class="overlay-container">
                                            <img src="{{asset('media')}}/{{$media->filename}}" alt="img" class="gallery-thumb-img">
                                            <div class="project-item-overlay">
                                                <h4>Open Imagination</h4>
                                                <p>
                                                    <img src="assets/images/users/avatar-1.jpg" alt="user" class="avatar-xs rounded-circle" />
                                                    <span class="ml-2">Curtis Marion</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif

            </div> <!-- container-fluid -->
        </div>

@endsection
