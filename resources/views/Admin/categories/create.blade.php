@extends('layouts.admin_layout')

@section('content')

    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-header">
                                <h3 class="card-title">


                                    @if(isset($categories))
                                        {{ __('edit_category') }}
                                    @else
                                        {{ __('add_category') }}

                                    @endif
                                </h3>
                            </div>


                            <form role="form" action="@if(isset($category)){{route('categories.update',$category->id) }} @else {{route('categories.store') }} @endif" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if(isset($category))
                                    @method('PUT')
                                @endif
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Name_Ar</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" type="text" @if(isset($category)) value="{{old('name_ar',$category->name_ar)}}" @endif name="name_ar" id="example-text-input" required>
                                        </div>
                                        @error('name_ar')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Name_En</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" type="text" @if(isset($category)) value="{{old('name_ar',$category->name_en)}}" @endif name="name_en" id="example-text-input" required>
                                        </div>
                                        @error('name_en')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Type</label>
                                        <div class="col-sm-10">
                                            <select class="form-control select2" required>
                                                <option @if(isset($category))  @if($category->id == $service->categoryId) selected @endif  @endif value="post">post</option>
                                                <option @if(isset($category))  @if($category->id == $service->categoryId) selected @endif  @endif value="service">service</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2">Image</label>
                                        <div class="col-sm-10">
                                            <input type="file" name="image" id="example-text-input">
                                        </div>
                                        @error('image')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                            <input class="btn btn-purple" type="submit" @if(isset($category)) value="edit" @else value="add" @endif>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->



            </div> <!-- container-fluid -->
        </div>

@endsection
