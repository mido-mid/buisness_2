<section id="ez-body__right-sidebar" class="col-lg-3 ez-sidebar">
    <ul class="pt-4" id="right-sidebar__items">
        @if(count($expected_groups) > 0)
            <li class="mt-3">
                <h6 class="pb-2" style="font-weight: bold;font-size: 15px">Groups You May Like</h6>
                <div class="suggested-groups">
                    @foreach($expected_groups as $group)
                    <div class="card">
                        <a href="/groups/{{$group->id}}">
                            <img src="{{asset('media')}}/{{$group->profile_image}}" class="card-img-top" alt="...">
                        </a>
                        <div class="d-flex justify-content-between">
                            <div class="card-body">
                                <a href="groups/{{$group->id}}" style="color:black !important">
                                    <h3 class="card-title">{{$group->name}}</h3>
                                </a>
                                <p class="card-text"><small class="text-muted" id="{{$group->id}}|1">
                                    <?php
                                        $member = App\models\GroupMember::where('group_id',$group->id)->count();
                                        echo $member;
                                    ?>
                                    </small>
                                    {{__('groups.member')}}
                                </p>
                            </div>
                            <div class="p-2">
                                @if(Auth::guard('web')->user())
                                    <?php
                                        $checkState = App\models\GroupMember::where('group_id',$group->id)->where('user_id',auth::user()->id)->get();
                                    ?>
                                    @if (count($checkState)==0)
                                    <div class="p-2">
                                            <button class="button-4 toty" id="join|{{$group->id}}|1" >{{__('groups.join')}} </button>
                                    </div>

                                    @elseif (count($checkState)>0)
                                        @if ($checkState[0]->state == 1)
                                            <div class="p-2">
                                                    <button class="button-2 toty" id="leave|{{$group->id}}|1">{{__('groups.left')}}</button>
                                            </div>

                                        @elseif ($checkState[0]->state == 2)
                                            <div class="p-2">
                                                <button class="button-2 toty" id="leave|{{$group->id}}|1">{{__('groups.left_request')}}</button>
                                            </div>

                                        @elseif ($checkState[0]->isAdmin == 1)
                                            <div class="p-2">
                                                <button class="button-2">{{__('groups.admin')}}</button>
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <form action="/login" method="post">
                                        @csrf
                                        <button class="button-4">{{__('groups.join')}}</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </li>
        @endif
    </ul>
</section>



