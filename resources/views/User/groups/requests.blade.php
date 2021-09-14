@extends('User.groups.layout')

@section('sectionGroups')
@if($myggroup->privacy == 2 && $isAdmin == 0 && $myState == 0)
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">{{__('groups.privacy')}}</h3>
    </div>
</div>
@else
<div class="group-about my-3">
    <table class="table table-hover">
        <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">{{__('groups.image_word')}}</th>
          <th scope="col">{{__('groups.name_word')}}</th>
          <th scope="col">{{__('groups.stutes_word')}}</th>
        </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?>
            @foreach($group_requests as $group_request)
            <?php $i = $i+1; ?>
                <tr id="{{$group_request->id}}">
                    <th scope="row">{{$i}}</th>
                    <td>
                    <img class="circle" src="{{asset('assets/images/users')}}/{{$group_request->member->personal_image}}" width="60" height="60">
                    </td>
                    <td>
                    <a href="#" class="link-cust" >{{$group_request->member->name}}</a>
                    </td>
                    <td>
                    <button class="button-4 totyRequestgroup" id="conferm|{{$group_request->id}}" >{{__('groups.confirm_request')}}</button>
                    <button class="button-2 totyRequestgroup" id="delete|{{$group_request->id}}">{{__('groups.refuse_request')}}</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
      </table>
</div>
@endif


@endsection
