<section id="ez-body__right-sidebar" class="col-lg-3 ez-sidebar">
    <ul class="pt-4" id="right-sidebar__items">
        @if(count($expected_groups) > 0)
            <li class="mt-3">
                <h6 class="pb-2" style="font-weight: bold;font-size: 15px">{{__('home.expected_groups')}}</h6>
                <div class="suggested-groups">
                    @foreach($expected_groups as $group)
                        <div class="group">
                            <a href="{{route('main-group',$group->id)}}">
                                <div class="group-banner">
                                    @if($group->cover_image)
                                        <img
                                            width="100%"
                                            src="{{asset('media')}}/{{$group->cover_image}}"
                                            alt="User Profile Pic"
                                        />
                                    @else
                                        <img
                                            width="100%"
                                            src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                            alt="User Profile Pic"
                                        />
                                    @endif
                                </div>
                                <div class="mt-2 group-info">
                                    <div>
                                        <p><b>{{$group->name}}</b></p>
                                        <p id="members-{{$group->id}}">{{$group->members}} {{__('groups.members')}}</p>
                                    </div>
                                    <a id="join-btn-{{$group->id}}" onclick="joinGroupSubmit({{$group->id}},'{{App::getlocale()}}')" class="btn btn-warning text-white">{{__('groups.join')}}</a>
                                    <form id="join-group-form-{{$group->id}}" action="{{ route('join_group') }}" method="POST" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="group_id" value="{{$group->id}}">
                                        <input type="hidden" id="join-flag-{{$group->id}}" name="flag" value="0">
                                    </form>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </li>
        @endif
    </ul>
</section>



