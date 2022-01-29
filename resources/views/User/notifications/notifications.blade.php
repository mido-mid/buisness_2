@extends('layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css" integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />
<link href="{{ asset('css/styles/notifications.css') }}" rel="stylesheet">
@section('content')
<div class="container">
    <div class="row">
        
        <div class="col-lg-9 right">
            <div class="box shadow-sm rounded bg-white mb-3">
                <div class="box-title border-bottom p-3">
                    <h6 class="m-0 text-truncate font-weight-bold" ><strong>Notifications</strong></h6>
                </div>
                <a id="ClearNotificationsBtn" class="btn btn-danger btn-sm" href="deletenotifications/">
                              <i class="fas fa-trash">
                              </i>
                              Clear Notifications
                </a>
                @foreach($notifications as $notification)
                <div id = "notificationDiv" class="box-body p-0">
                    <div class="p-3 d-flex align-items-center bg-light border-bottom osahan-post-header">
                        <div class="dropdown-list-image mr-3">
                            <img class="rounded-circle" src="https://bootdey.com/img/Content/avatar/avatar3.png" alt="" />
                        </div>
                        <div class="font-weight-bold mr-3">
                            <div style="cursor: pointer;" onclick="window.location='{{ route('openNotification', ['model_id'=>$notification['targetId'] ]) }}'" class="text-truncate">{{$notification['title']}}</div>
                            <div style="cursor: pointer;" onclick="window.location='{{ route('openNotification', ['model_id'=>$notification['targetId'] ]) }}'" class="small">{{$notification['body']}}</div>
                        </div>
                        <span class="ml-auto mb-auto">
                            <br />
                            <div class="notificationCreateAt text-right text-muted pt-1">{{$notification['createAt']}}</div>
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/timeSet.js')}}"></script>
@endsection