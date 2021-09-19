
@section('groups')

    @if(count($groups) > 0)

        @foreach($groups as $group)
            <div class="post-container bg-white mt-3 p-3" id="searched-groups-{{$group->id}}">
                <div class="post-owner d-flex align-items-center">
                        @if($group->profile_image)
                            <div class="owner-img">
                                <a style="display: inline" href="{{route('main-group',$group->id)}}"><img src="{{asset('media')}}/{{$group->profile_image}}" class="rounded-circle" /></a>
                            </div>
                        @else
                            <div class="owner-img">
                                <a style="display: inline" href="{{route('main-group',$group->id)}}"><img src="{{asset('media')}}/img.jpg" class="rounded-circle" /></a>
                            </div>
                        @endif
                    <div class="owner-name pl-3">
                        <a href="{{route('main-group',$group->id)}}"><b>
                                {{$group->name}}
                            </b></a>

                        <span style="display: block" id="members-{{$group->id}}">{{$group->members}} {{__('groups.members')}}</span>
                    </div>
                    <div class="post-option ml-auto pr-3">
                        @if($group->state == 'delete group')
                            <a id="delete-group-btn-{{$group->id}}" onclick="deleteGroupSubmit({{$group->id}})" class="btn btn-warning text-white">{{$group->joined}}</a>
                            <form id="delete-group-form-{{$group->id}}" action="{{ route('groups.destroy',$group->id) }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <a id="join-btn-{{$group->id}}" onclick="joinGroupSubmit({{$group->id}})" class="btn btn-warning text-white">{{$group->joined}}</a>
                            <form id="join-group-form-{{$group->id}}" action="{{ route('join_group') }}" method="POST" style="display: none;">
                                @csrf
                                <input type="hidden" name="group_id" value="{{$group->id}}">
                                <input type="hidden" id="join-flag-{{$group->id}}" name="flag" value="{{$group->flag}}">
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection
