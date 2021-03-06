<?php
use App\Models\Following;
use App\models\Friendship;
use App\Models\Inspiration;use App\Models\UserMusic;use App\Models\UserSport;
function CheckUserFriendshipState($user, $enemy){
    //Different users
    //1. User => From token
    //2. Enemy=> The person i want to check my friendship with
    /*
     *
     */
    #region different States
    //Friend
    //pending login => request  cancel request
    //cancel
    //accepted => cancel request
    //guest  => add request
    $friendship = Friendship::where('senderId',$user)->where('receiverId',$enemy)->get();
    if(count($friendship) > 0){
        switch($friendship[0]->stateId){
            case '2':
                return 'pending';
            case '1':
                return 'accepted';
        }
    }else {
        $friendship = Friendship::where('senderId', $enemy)->where('receiverId', $user)->get();
        if (count($friendship) > 0) {
            switch($friendship[0]->stateId){
                case '2':
                    return 'cancel';
                case '1':
                    return 'accepted';
            }
        }
    }
    return 'guest';
    //Guest
    //Didn't accept my request
    //i didn't accept his request
    #endregion
}
function CheckUserFollowingState($user, $enemy)
{
    ////Following => Enemy
    ////Follower  => User
    $following = Following::where('followerId', $user)->where('followingId', $enemy)->get();
    if (count($following) > 0) {
        return 1;
    } else {
        return 0;
    }
}
function CheckInspiration($user,$enemy){
    $records = Inspiration::where('user_id',$user)->where('inspirerende_id',$enemy)->get();
    if(count($records) > 0 ){
        return 1;
    }else{
        return 0;
    }
}
function friendshipId($user, $enemy){
    $friendship = Friendship::where('senderId',$user)->where('receiverId',$enemy)->get();
    if(count($friendship)>0){
        return $friendship[0]->id;
    }else{
        $friendship = Friendship::where('senderId',$enemy)->where('receiverId',$user)->get();
        if(count($friendship)>0){
            return $friendship[0]->id;
        }
    }
    return 99;
}
function followingId($user, $enemy){
    $friendship = Following::where('followerId',$user)->where('followingId',$enemy)->get();
    if(count($friendship)>0){
        return $friendship[0]->id;
    }else{
        $friendship = Following::where('followingId',$enemy)->where('followerId',$user)->get();
        if(count($friendship)>0){
            return $friendship[0]->id;
        }else{
            return  99;
        }
    }
}
function inspirationId($user, $enemy){
    $records =  Inspiration::where('user_id',$user)->where('inspirerende_id',$enemy)->get();
    if(count($records)>0){
        return $records[0]->id;
    }else{
        return 99;
    }
}
?>
@extends('layouts.profile')
<style>
    ul {
        list-style-type: none;
    }
    a:link {
        color: black;
    }
    a:visited {
        color: black;
    }
    a:hover {
        color: black;
    }
    a:active {
        color: black;
    }
</style>
@section('content')


    {{--Musics--}}
    <section id="ez-body__center-content-music" class="col-lg-10 row profile-center-content">
        <div class="col-lg-12 profile-center-content">
            <br>
            <center>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </center>
            <div class="row">
                <div class="col-lg-12">
                    <input type="text" class="search-box" placeholder="Search about your sport" id="search" onkeyup="myFunction()">

                    <section class="group-section px-3" id="renderMusic">
                        @if($myProfile == 1)
                            <div class="group-mygroups py-3">

                                <div class="row text-center totyAdmin">
                                    <button onclick="showAllMusics()" class=" button-4 col-5 mx-auto tablinks" href="">All Sports</button>
                                    <button onclick="showMyMusics()" class="button-4 col-5  mx-auto tablinks" href="">My Sports</button>
                                </div><br>

                                <div id="all-musics" class=" tabcontent" >
                                    <div  class="row ">
                                        @foreach($sports['allSports'] as $oneAllMusic)
                                            <div class="music col-sm-3 target">
                                                <form action="{{route('redeny.user.list.sport')}}" method="post" style="display:inline;">
                                                    @csrf
                                                    <img src="{{asset('assets/images/sports/'.$oneAllMusic->image)}}" alt="" style="height:250px;width: 100%">
                                                    <input type="hidden" id="state" name="state" value="0">
                                                    <input type="hidden" name="musicId" id="musicId" value="{{$oneAllMusic->id}}">

                                                    <center>
                                                        <h2>
                                                            {{$oneAllMusic['name_'.app()->getLocale()] }}
                                                        </h2>
                                                        <button  class="button-4 col-5  mx-auto tablinks ">Add To  List </button>
                                                        <br><br>
                                                    </center>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div style="display:none;" id="my-musics" class=" tabcontent" >

                                    <div  class="row ">
                                        @foreach($sports['mySports'] as $oneAllMusic)
                                            <div class="music col-sm-3 target">
                                                <form action="{{route('redeny.user.list.sport')}}" method="post" style="display:inline;">
                                                    @csrf
                                                    <img src="{{asset('assets/images/sports/'.$oneAllMusic->music->image)}}" alt="" style="height:250px;width: 100%">
                                                    <input type="hidden" id="state" name="state" value="1">
                                                    <input type="hidden" name="relation" id="relation" value="{{$oneAllMusic->id}}">
                                                    <input type="hidden" name="musicId" id="musicId" value="{{$oneAllMusic->music->id}}">
                                                    <center>
                                                        <h2>
                                                            {{$oneAllMusic->music['name_'.app()->getLocale()]}}
                                                        </h2>
                                                        <button  class="button-4 col-5  mx-auto tablinks ">Remove </button>
                                                        <br><br>
                                                    </center>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>

                            </div>
                        @else
                            <div class="group-mygroups py-3">

                                <div  id="my-musics" class=" tabcontent" >
                                    <div  class="row ">
                                        @foreach($sports['mySports'] as $oneAllMusic)
                                            <div class="music col-sm-3 target">
                                                <form action="{{route('redeny.user.list.sport')}}" method="post" style="display:inline;">
                                                    @csrf
                                                    <img src="{{asset('assets/images/sports/'.$oneAllMusic->music->image)}}" alt="" style="height:250px;width: 100%">
                                                    <input type="hidden" name="relation" id="relation" value="{{$oneAllMusic->id}}">
                                                    <input type="hidden" name="musicId" id="musicId" value="{{$oneAllMusic->music->id}}">
                                                    <center>
                                                        <?php
                                                        $inMyCokkection = UserSport::where('userId',auth::user()->id)->where('sportId',$oneAllMusic->music->id)->get();
                                                        ?>
                                                        <h2>
                                                            {{$oneAllMusic->music->name}}

                                                        </h2>
                                                        <!-- Auth Id Music-->

                                                        @if(count($inMyCokkection) > 0)
                                                            <input type="hidden" id="state"  name="state"  value="1">
                                                            <button  class="button-4 col-5  mx-auto tablinks" type="submit">Remove </button>
                                                        @else
                                                            <input type="hidden" id="state"  name="state"  value="0">
                                                            <button  class="button-4 col-5  mx-auto tablinks" type="submit">Add To List </button>
                                                        @endif
                                                        <br><br>
                                                    </center>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        @endif
                    </section>
                </div>
                {{-- @include('User.groups.related') --}}
            </div>
        </div>
    </section>
    {{--End Of Musics--}}
@endsection
@include('User.profile.models.friends')
@include('User.profile.scripts.main')
@include('User.profile.scripts.friendFollowIns')
@include('User.profile.scripts.musics')
