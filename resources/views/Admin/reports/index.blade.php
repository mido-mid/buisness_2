@extends('layouts.admin_layout')

@section('content')


    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">
                <div class="col-12">
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{__('admin.reports')}}</h3>
                            </div>
                            <div class="card-body">
                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                    @if(count($reports) > 0)
                                        <thead>
                                            <tr>
                                                <th>{{__('admin.body')}}</th>
                                                <th>{{__('admin.status')}}</th>
                                                <th>{{__('admin.source')}}</th>
                                                <th>{{__('admin.user')}}</th>
                                                <th>{{__('admin.details')}}</th>
                                                <th>{{__('admin.controls')}}</th>
                                            </tr>
                                        </thead>
                                        @foreach($reports as $report)
                                            <tbody>
                                                <tr>
                                                    <td>{{$report->body}}</td>
                                                    <th>
                                                        {{$report->state}}
                                                    </th>
                                                    <th>
                                                        {{ $report->model_type }}
                                                    </th>
                                                    <th>
                                                        {{ $report->user_name }}
                                                    </th>
                                                    <td>
                                                        <a type="button" class="btn btn-info"href="{{route('reports.showinfo',$report->id)}}">{{ __('admin.show') }}</a>
                                                    </td>
                                                    <td>

                                                        <form action="{{ route('reports.update', $report->id) }}" method="post" style="display: inline; margin-right: 5px">
                                                            @csrf
                                                            @method('put')

                                                            <input type="hidden" name="state" value="accepted">
                                                            <button type="button" class="btn btn-success" onclick="confirm('{{ __("home.confirm") }}') ? this.parentElement.submit() : ''">{{ __('admin.accept') }}</button>

                                                        </form>

                                                        <form action="{{ route('reports.update', $report->id) }}" method="post" style="display: inline; margin-right: 5px">
                                                            @csrf
                                                            @method('put')

                                                            <input type="hidden" name="state" value="refused">
                                                            <button type="button" class="btn btn-primary" onclick="confirm('{{ __("home.confirm") }}') ? this.parentElement.submit() : ''">{{ __('admin.refuse') }}</button>

                                                        </form>
                                                    </td>
                                                </tr>
                                                </tbody>
                                        @endforeach
                                    @else
                                        <tbody>
                                            <tr>
                                                <td colspan="3">
                                                    <center>
                                                        <h3>{{ __('admin.no_reports') }}</h3>
                                                    </center>
                                                </td>
                                            </tr>
                                        </tbody>
                                    @endif
                                </table>

                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->



            </div> <!-- container-fluid -->
        </div>

@endsection
