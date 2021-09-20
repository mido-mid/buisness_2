<?php
namespace App\Http\Controllers\User;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use App\Models\Following;
use App\Models\Friendship;
use App\Models\Hoppy;
use App\Models\Inspiration;
use App\Models\Music;
use App\Models\Post;
use App\Models\Sport;
use App\Models\UserHoppy;
use App\Models\UserInspiration;
use App\Models\UserMusic;
use App\Models\UserSport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;

class ProfileController extends Controller
{
    protected $myProfile;

    public function index($user_id)
    {

        $user = \auth()->user();
        $MyId = Auth::user()->id;
        if ($MyId == $user_id) {
            $this->myProfile = 1;
        } else {
            $this->myProfile = 0;
        }
        $unRelatedUsersIds = $this->getUnRelatedUsersIds($user_id);

        $myProfile = $this->myProfile;
        $profileId = User::find($user_id);
        $this->profileId = $profileId;
        $friends = $this->GetFirends($user_id);
        $followers = $this->GetFollower($user_id);
        $followings = $this->GetFollowing($user_id);

        $Myfollowers = $this->GetFollower($MyId);
        $Myfollowings = $this->GetFollowing($MyId);
        $MyFriends = $this->GetFirends($MyId);

        $firendship_state = $this->CheckUserFriendshipState($MyId, $user_id);
        $following_state = $this->CheckUserFollowingState($MyId, $user_id);
        $pending_send = $this->GetPendingSend($user_id);
        $pending_receive = $this->GetPendingReceive($user_id);

        //Musics
        $musics = $this->musics($user_id);
        $sports = $this->sports($user_id);
        $hoppies = $this->hoppies($user_id);

        $posts = DB::table('posts')->where('publisherId', $user_id)->where('postTypeId', 2)->orderBy('created_at','desc')->get()->toArray();

        foreach ($posts as $post){
            $post = $this->getPost(auth()->user(),$post);
        }

        $privacy = DB::table('privacy_type')->get();

        $categories = DB::table('categories')->where('type', 'post')->get();

        $times = DB::table('sponsored_time')->get();

        $reaches = DB::table('sponsored_reach')->get();

        $ages = DB::table('sponsored_ages')->get();

        $reacts = DB::table('reacts')->get();

        $cities = DB::table('cities')->get();

        $countries = DB::table('countries')->get();

        $friends_info = [];

        // friends posts he follows and are public and in groups you are in and in pages you liked
        $friends = DB::table('friendships')->where(function ($q) use ($user){
            $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
        })->where('stateId',2)->get();

        foreach ($friends as $friend){
            $friend_id = $friend->receiverId == $user->id ? $friend->senderId : $friend->receiverId;

            $friend_info = DB::table('users')->select('id','name','cover_image','personal_image')->where('id',$friend_id)->first();

            array_push($friends_info,$friend_info);

        }
        foreach ($friends_info as $info){
            $info->name = explode(' ',$info->name)[0];
        }

        return view('User.profile.index_en',
            compact(
                'posts',
                'privacy', 'categories', 'times',
                'ages', 'reaches', 'reacts','cities',
                'countries','friends_info',
                'unRelatedUsersIds',
                'myProfile',
                'profileId',
                'friends',
                'followers', 'followings',
                'Myfollowers','Myfollowings','MyFriends',
                'pending_send', 'pending_receive',
                'firendship_state', 'following_state',
                'musics','sports','hoppies',
                'user_id'
            ));
    }
    public function viewComponent($user_id,$component){
        $images = [];
        $videos = [];
        $MyId = Auth::user()->id;
        if ($MyId == $user_id) {
            $this->myProfile = 1;
        } else {
            $this->myProfile = 0;
        }
        $user_posts = Post::where('publisherId',$user_id)->get();
        foreach($user_posts as $user_post){
            $AllMedia  = $user_post->media;
            foreach($AllMedia as $media){
                if($media->mediaType == 'image'){
                    $images [] = $media;
                }elseif($media->mediaType == 'video'){
                    $videos [] = $media;
                }
            }
        }

        $unRelatedUsersIds = $this->getUnRelatedUsersIds($user_id);

        $myProfile = $this->myProfile;
        $profileId = User::find($user_id);
        $this->profileId = $profileId;
        $friends = $this->GetFirends($user_id);
        $followers = $this->GetFollower($user_id);
        $followings = $this->GetFollowing($user_id);

        $Myfollowers = $this->GetFollower($MyId);
        $Myfollowings = $this->GetFollowing($MyId);
        $MyFriends = $this->GetFirends($MyId);

        $firendship_state = $this->CheckUserFriendshipState($MyId, $user_id);
        $following_state = $this->CheckUserFollowingState($MyId, $user_id);
        $pending_send = $this->GetPendingSend($user_id);
        $pending_receive = $this->GetPendingReceive($user_id);
        //Musics
        $musics = $this->musics($user_id);
        $sports = $this->sports($user_id);
        $hoppies = $this->hoppies($user_id);
        $inspirations = $this->inspirations($user_id);

        return view('User.profile.'.$component.'.index_'.app()->getLocale(),compact(
            'unRelatedUsersIds',
            'myProfile',
            'profileId',
            'friends',
            'followers', 'followings',
            'Myfollowers','Myfollowings','MyFriends',
            'pending_send', 'pending_receive',
            'firendship_state', 'following_state',
            'musics','sports','hoppies','inspirations',
            'user_id',
            'images','videos'
        ));
    }

    ///Get All Friends
    public function GetFirends($user_id)
    {
        $friend_list = Friendship::where('senderId', $user_id)->orWhere('receiverId', $user_id)->where('stateId', 1)->get();
        return $friend_list;
    }

    public function GetFollower($user_id)
    {
        $friend_list = Following::where('followingId', $user_id)->get();
        return $friend_list;
    }

    public function GetFollowing($user_id)
    {
        $friend_list = Following::where('followerId', $user_id)->get();
        return $friend_list;
    }

    public function GetAllRelatedUsersSender($user_id)
    {
        $friend_list = Friendship::where('senderId', $user_id)->get();
        return $friend_list;
    }

    public function GetAllRelatedUsersReceiver($user_id)
    {
        $friend_list = Friendship::where('receiverId', $user_id)->get();
        return $friend_list;
    }

    public function GetPendingSend($user_id)
    {
        $friend_list = Friendship::where('senderId', $user_id)->where('stateId', 2)->get();
        return $friend_list;
    }

    public function GetPendingReceive($user_id)
    {
        $friend_list = Friendship::where('receiverId', $user_id)->where('stateId', 2)->get();
        return $friend_list;
    }

    public function getUnRelatedUsersIds($user_id)
    {
        $system_users = User::get();
        $ids = [];
        $unrealed = [];
        //Sender => Receivers
        $GetAllRelatedUsersSender = $this->GetAllRelatedUsersSender($user_id);
        foreach ($GetAllRelatedUsersSender as $sender) {
            $ids [] = $sender->receiverId;
        }
        //Receivers => Senders
        $GetAllRelatedUsersReceiver = $this->GetAllRelatedUsersReceiver($user_id);
        foreach ($GetAllRelatedUsersReceiver as $receiver) {
            $ids [] = $receiver->senderId;
        }

        foreach ($system_users as $user) {
            if (!in_array($user->id, $ids)) {
                $unrealed [] = $user;
            }

        }
        return $unrealed;
    }

    public function CheckUserFriendshipState($user, $enemy)
    {
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
        $friendship = Friendship::where('senderId', $user)->where('receiverId', $enemy)->get();
        if (count($friendship) > 0) {
            switch ($friendship[0]->stateId) {
                case '2':
                    return 'pending';
                case '1':
                    return 'accepted';
            }
        } else {
            $friendship = Friendship::where('senderId', $enemy)->where('receiverId', $user)->get();
            if (count($friendship) > 0) {
                switch ($friendship[0]->stateId) {
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
    function CheckInspiration($user,$enemy){
        $records = Inspiration::where('user_id',$user)->where('inspirerende_id',$enemy)->get();
        if(count($records) > 0 ){
            return 1;
        }else{
            return 0;
        }
    }
    public function CheckUserFollowingState($user, $enemy)
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

    public function friendshipId($user, $enemy){
        $friendship = Friendship::where('senderId',$user)->where('receiverId',$enemy)->get();
        if(count($friendship)>0){
            return $friendship[0]->id;
        }else{
            $friendship = Friendship::where('senderId',$enemy)->where('receiverId',$user)->get();
            if(count($friendship) > 0) {
                return $friendship[0]->id;
            }else{
                return 99;
            }
        }

    }
    function followingId($user, $enemy){
        $friendship = Following::where('followerId',$user)->where('followingId',$enemy)->get();
        if(count($friendship)>0){
            return $friendship[0]->id;
        }else{
            $friendship = Following::where('followingId',$enemy)->where('followerId',$user)->get();
            if(count($friendship) > 0) {
                return $friendship[0]->id;
            }else{
                return 99;
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
    public function addFriendRedeny(Request $request)
    {
        //Iam the sender
        $senderId = $request->senderId;
        $receiverId = $request->receiverId;
        $state = 2;
        $friendRequest = Friendship::create([
            'senderId' => $senderId,
            'receiverId' => $receiverId,
            'stateId' => $state,
        ]);

        //Check if iam follower or not
        $me_following_him = $this->CheckUserFollowingState($senderId, $receiverId);
        if (!$me_following_him == 1) {
            $hime_following_me = $this->CheckUserFollowingState($senderId, $receiverId);
            if (!$hime_following_me == 1) {
                //Sender is the follower
                //Receiver is the following
                $follower = $senderId;
                $following = $receiverId;
                $followingRequest = Following::create([
                    'followerId' => $follower,
                    'followingId' => $following,
                ]);
            }
        }
        $html = $this->rednder($senderId);
        return $html;
    }
    public function followFriendRedeny(Request $request)
    {
        //Iam the sender
        $senderId = $request->senderId;
        $receiverId = $request->receiverId;

        //Receiver is the following
        $follower = $senderId;
        $following = $receiverId;
        $followingRequest = Following::create([
            'followerId' => $follower,
            'followingId' => $following,
        ]);


        $html = $this->rednder($senderId);
        return $html;
    }
    #region render
    /*Modal*/
    public function rednder($senderId){
        $profileId = User::find($senderId);
        $followings = $this->GetFollowing($senderId);
        $html = "
                <section class=\"group-section \" style=\"min-height:auto\">
                    <div class=\"group-members my-3\">
                        <ul class=\"members-list list-unstyled\" >";
        if (count($followings) > 0) {
            foreach ($followings as $following) {
                $html .= "                          <li class=\"members-item\" >
                                <div class=\"group-member d-flex justify-content-between\">
                                    <a href=\"#\" class=\"group-member-link d-flex align-items-center\">
                                        <img src=" . asset('assets/images/users/' . $following->following->personal_image) ." alt=\"#\" class=\"member-img img-fluid\">
                                        <span class=\"d-inline-block group-member-link_span\">
                                              <p class=\"user-name\">" . $following->following->name . "</p>
                                            </span>
                                    </a>
                                    <div>";
                if (count(\App\Models\Following::where('followerId', $profileId->id)->where('followingId', $following->following->id)->get()) > 0) {
                    $html .= "
                                        <button class='button-4 totyAdmin unfollow".$following->id."'  >Un Follow</button>
                                        <input type=\"hidden\" class=\"friendshipId".$following->id."\" value=".$this->followingId($profileId->id,$following->following->id).">
                                        ";

                }
                if ($this->CheckUserFriendshipState($profileId->id, $following->following->id) == 'guest') {
                    $html .= "
                                        <button  class='button-4 totyAdmin add".$following->id."'>Add Friend </button>
                                                <input type=\"hidden\" class='receiver".$following->id."' value='".$following->following->id."'>";
                } elseif ($this->CheckUserFriendshipState($profileId->id, $following->following->id) == 'pending') {
                    $html .= "
                                                <input type=\"hidden\" class='friendshipId".$following->id."' value=" . $this->friendshipId($profileId->id, $following->following->id) . ">
                                                <button  class='button-4 totyAdmin cancel".$following->id."'>Cancel Request </button>";
                } elseif ($this->CheckUserFriendshipState($profileId->id, $following->following->id) == 'cancel') {
                    $html .= "
                                                <input type=\"hidden\" class='friendshipId".$following->id."' value=" . $this->friendshipId($profileId->id, $following->following->id) . ">
                                                <button  class='button-4 totyAdmin refuse".$following->id."'>Refuse Request </button>
                                                <button  class='button-4 totyAdmin refuse".$following->id."'>Accept Request </button>
                                 ";

                } elseif ($this->CheckUserFriendshipState($profileId->id, $following->following->id) == 'accepted') {

                    $html .= "
                                                <input type=\"hidden\" class='friendshipId".$following->id."' value=".$this->friendshipId($profileId->id,$following->following->id). ">
                                                <button  class='button-4 totyAdmin refuse".$following->id."'>Remove Friend </button>";
                }
                $html .= "
                                            <button class=\"button-4 totyAdmin\" id=\"\">Add Friend</button>
</div></div>
                            </li>
                            ";
            }
        } else {
            $html .= "
                                <center>
                                    <h2>There is no followings yet!</h2>
                                </center>
                            ";
        }
        $html .= "          </ul>
                    </div>
                </section>
";
        return $html;
    }
    /*Profile*/
    public function rednderButtonsProfile($profileId){
        $html="<input type=\"hidden\" class='receiverFriend' value=".$profileId.">";

        if($this->CheckUserFollowingState(Auth::user()->id,$profileId) == 1){
            $html.="
        <input type=\"hidden\" class='followingId' value=".$this->followingId(Auth::user()->id,$profileId).">
                        <button style=\"width: 180px;\" class='button-4 totyAdmin unfollowfriend' >Un Follow</button>";
        }else{
            $html.="
                        <button style=\"width: 180px;\" class='button-4 totyAdmin followfriend' >Follow</button>";
        }
        if($this->CheckUserFriendshipState(Auth::user()->id,$profileId) == 'guest' ) {
            $html .= "<button style=\"width: 180px;\" class=\"button-4 totyAdmin addfirend\" >Add Friend </button>
                       ";
        }elseif($this->CheckUserFriendshipState(Auth::user()->id,$profileId) == 'pending' ){
            $html .="       <input type=\"hidden\" class=\"friendshipId\" value=".$this->friendshipId(Auth::user()->id,$profileId).">
                        <button style=\"width: 180px;\" class=\"button-4 totyAdmin cancelrequest\">Cancel Request </button>";
        }elseif($this->CheckUserFriendshipState(Auth::user()->id,$profileId) == 'cancel' ){
            $html .= "<input type=\"hidden\" class=\"friendshipId\" value=".$this->friendshipId(Auth::user()->id,$profileId).">
                        <button style=\"width: 150px;\" class='button-4 totyAdmin cancelrequest'>Refuse Request </button>
                        <button style=\"width: 150px;\" class='button-4 totyAdmin acceptfriend'>Accept Request </button>";
        }elseif($this->CheckUserFriendshipState(Auth::user()->id,$profileId) == 'accepted' ){
            $html .=  "     <input type=\"hidden\" class=\"friendshipId\" value=".$this->friendshipId(Auth::user()->id,$profileId).">
                        <button style=\"width: 180px;\" class=\"button-4 totyAdmin cancelrequest\">Remove Friend </button>";
        }
        if($this->CheckInspiration(Auth::user()->id,$profileId)== 1){
            $html .=   "<input type=\"hidden\" class='inspirationId' value=".$this->inspirationId(Auth::user()->id,$profileId).">
                        <button class='button-4 totyAdmin removeinspiration '  style=\"width: 180px;\">Remove inspiration</button>";
        }else{
            $html .="
                        <button class='button-4 totyAdmin addinspiration'  style=\"width: 180px;\">Add inspiration</button>";
        }
        $html.="
    ";
        return $html;
    }
    public function musicsSection($profileId,$musics){
        $html ="                    <section class=\"group-section px-3 renderMusic\">
                        <div class=\"group-mygroups py-3\">
                            <div class=\"row text-center totyAdmin\">
                                <button onclick=\"showAllMusics()\" class=\" button-4 col-5 mx-auto tablinks\" href=\"\">All Musics</button>
                                <button onclick=\"showMyMusics()\" class=\"button-4 col-5  mx-auto tablinks\" href=\"\">My Muscis</button>
                            </div><br>
                            <div id=\"all-musics\" class=\" tabcontent\" >
                                <div  class=\"row \">";
        foreach($musics['allMusics'] as $oneAllMusic){
            $html="
                                      <div class=\"music col-sm-3 target\">
                                        <img src=".asset('assets/images/musics/'.$oneAllMusic->image)." alt=\"\" style=\"height:250px;width: 100%\">
                                          <input type=\"hidden\" id='state.$oneAllMusic->id.' value=\"0\">
                                        <audio controls style=\"width: 100%\">
                                            <source src=".asset('assets/images/musics/'.$oneAllMusic->music)." type=\"audio/ogg\">
                                            <source src=".asset('assets/images/musics/'.$oneAllMusic->music)." type=\"audio/mpeg\">
                                            Your browser does not support the audio element.
                                        </audio>
                                        <center>
                                            <h2>
                                                .$oneAllMusic->name.
                                            </h2>
                                            <button  class=\"button-4 col-5  mx-auto tablinks \" id='addRemoveMusic.$oneAllMusic->id.'>Add To  List </button>
                                            <br><br>
                                        </center>
                                    </div>";

        }
        $html="
                                </div>
                            </div>
                            <div style=\"display:none;\" id=\"my-musics\" class=\" tabcontent\" >
                                <form enctype=\"multipart/form-data\" method=\"post\"  action=".route('redeny.user.add.music').">
                                    @csrf
                                    <div >
                                        <center>
                                            Add New Music
                                            <br><br>
                                            <div class=\"col-sm-8\">
                                                <div  class=\"row \">
                                                    <div class=\"col-3\">
                                                        Music Name
                                                    </div>
                                                    <div class=\"col-9\">
                                                        <input type=\"text\" class=\"form-control\" id=\"musicName\" name=\"musicName\" required>
                                                    </div>
                                                </div>
                                                <div  class=\"row \">
                                                    <div class=\"col-3\">
                                                        Music Cover
                                                    </div>
                                                    <div class=\"col-9\">
                                                        <input type=\"file\" class=\"form-control\" id=\"musicCover\" name=\"musicCover\" required>
                                                    </div>
                                                </div>
                                                <div  class=\"row \">
                                                    <div class=\"col-3\">
                                                        Music \"Mp4\"
                                                    </div>
                                                    <div class=\"col-9\">
                                                        <input type=\"file\" class=\"form-control\" id=\"music\" name=\"music\" required>
                                                    </div>
                                                </div>
                                                <div  class=\"row \">
                                                    <div class=\"col-12 text-center\">
                                                        <br>
                                                        <button  class=\"button-4 col-5  mx-auto tablinks \" type=\"submit\">Add Mucis</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <br><br>
                                        </center>
                                    </div>
                                </form>
                                <div  class=\"row \">
                                ";
        foreach($musics['myMusics'] as $oneAllMusic) {

            $html = "
                                        <div class=\"music col-sm-3 target\">
                                            <img src=\"{{asset('assets/images/musics/'.$oneAllMusic->music->image)}}\" alt=\"\" style=\"height:250px;width: 100%\">
                                            <audio controls style=\"width: 100%\" >
                                                <source src=\"{{asset('assets/images/musics/'.$oneAllMusic->music->music)}}\" type=\"audio/ogg\">
                                                <source src=\"{{asset('assets/images/musics/'.$oneAllMusic->music->music)}}\" type=\"audio/mpeg\">
                                                Your browser does not support the audio element.
                                            </audio>
                                            <center>
                                                <h2>
                                                    {{
            $oneAllMusic->music->name}}
                                                </h2>
                                                <button  class=\"button-4 col-5  mx-auto tablinks\" href=\"\">Add To  List </button>
                                                <br><br>
                                            </center>
                                        </div>
                                    ";

        }
        $html="
                                </div>
                            </div>
                        </div>
                    </section>
";
        return $html;
    }
    /*
     * unfollow
     * follow
     * addfriend
     * accept
     * refuse
     * addinsp
     * removeinsp
     */
    public function addFriendProfile(Request $request)
    {
        //Iam the sender
        $senderId = $request->senderId;
        $receiverId = $request->receiverId;
        $state = 2;
        $friendRequest = Friendship::create([
            'senderId' => $senderId,
            'receiverId' => $receiverId,
            'stateId' => $state,
        ]);

        //Check if iam follower or not
        $me_following_him = $this->CheckUserFollowingState($senderId, $receiverId);
        if (!$me_following_him == 1) {
            $hime_following_me = $this->CheckUserFollowingState($senderId, $receiverId);
            if (!$hime_following_me == 1) {
                //Sender is the follower
                //Receiver is the following
                $follower = $senderId;
                $following = $receiverId;
                $followingRequest = Following::create([
                    'followerId' => $follower,
                    'followingId' => $following,
                ]);
            }
        }
        $html = $this->rednderButtonsProfile($receiverId);
        return $html;
    }
    public function AcceptFriendProfile(Request $request)
    {
        // Iam the receiver
        $friendshipId = $request->friendshipId;
        $friendshipRecord = Friendship::find($friendshipId);
        $senderId = $friendshipRecord->senderId;
        $receiverId = $friendshipRecord->receiverId;
        //Receiver is the follower
        //Sender is the following
        $follower = $receiverId;
        $following = $senderId;
        $followingRequest = Following::create([
            'followerId' => $follower,
            'followingId' => $following,
        ]);
        $friendshipRecord->stateId = 1;
        $friendshipRecord->save();
        $html = $this->rednderButtonsProfile($senderId);
        return $html;
    }
    public function RefuseFriendProfile(Request $request)
    {
        $friendshipId = $request->friendshipId;
        $receiverFriend = $request->receiverFriend;

        $friendshipRecord = Friendship::find($friendshipId);
        $friendshipRecord->delete();
        /*$msg = 'You have refused the request successfully';
        return $this->returnSuccessMessage($msg, 200);*/
        $html = $this->rednderButtonsProfile($receiverFriend);
        return $html;
    }
    public function followFriendProfile(Request $request)
    {
        //Iam the sender
        $senderId = $request->senderId;
        $receiverId = $request->receiverId;

        //Receiver is the following
        $follower = $senderId;
        $following = $receiverId;
        $followingRequest = Following::create([
            'followerId' => $follower,
            'followingId' => $following,
        ]);
        $html = $this->rednderButtonsProfile($following);
        return $html;
    }
    public function unfollowFriendProfile(Request $request)
    {
        $friendshipId = $request->friendshipId;
        $receiverFriend = $request->receiverFriend;

        $following = Following::find($friendshipId);
        $followingId = $following->followingId;
        $followerId = $following->followerId;
        $following->delete();
        $trash = Following::where('followingId',$followingId)->where('followerId',$followerId)->get();
        foreach($trash as $t){
            $t->delete();
        }
        $html = $this->rednderButtonsProfile($receiverFriend);
        return $html;
    }

    public function addInspirationProfile(Request $request)
    {
        //Iam the sender
        $senderId = $request->senderId;
        $receiverId = $request->receiverId;
        Inspiration::create([
            'user_id' => $senderId,
            'inspirerende_id' => $receiverId
        ]);

        $html = $this->rednderButtonsProfile($receiverId);
        return $html;
    }
    public function removeInspirationProfile(Request $request)
    {
        $inspirationId = $request->friendshipId;
        $inspiration= Inspiration::find($inspirationId);
        $user_id = $inspiration->user_id;
        $inspirerende_id = $inspiration->inspirerende_id;
        $inspiration->delete();
        $old = Inspiration::where('user_id',$user_id)->where('inspirerende_id',$inspirerende_id)->get();
        foreach($old as $o){
            $o->delete();
        }
        $html = $this->rednderButtonsProfile($inspirerende_id);
        return $html;
    }//ok


    #endregion
    public function AcceptFriendRedeny(Request $request)
    {
        // Iam the receiver
        $friendshipId = $request->friendshipId;
        $friendshipRecord = Friendship::find($friendshipId);
        $senderId = $friendshipRecord->senderId;
        $receiverId = $friendshipRecord->receiverId;
        //Receiver is the follower
        //Sender is the following
        $follower = $receiverId;
        $following = $senderId;
        $followingRequest = Following::create([
            'followerId' => $follower,
            'followingId' => $following,
        ]);
        $friendshipRecord->stateId = 1;
        $friendshipRecord->save();
        $msg = 'You have accepted the request successfully';
        return $this->returnSuccessMessage($msg, 200);
    }
    public function RefuseFriendRedeny(Request $request)
    {
        $friendshipId = $request->friendshipId;
        $friendshipRecord = Friendship::destroy($friendshipId);
        /*$msg = 'You have refused the request successfully';
        return $this->returnSuccessMessage($msg, 200);*/
        $html = $this->rednder(auth::user()->id);
        return $html;
    }
    public function unfollowFriendRedeny(Request $request)
    {
        $friendshipId = $request->friendshipId;
        $friendshipRecord = Following::destroy($friendshipId);
        /*$msg = 'You have refused the request successfully';
        return $this->returnSuccessMessage($msg, 200);*/
        $html = $this->rednder(auth::user()->id);
        return $html;
    }

    public function addMusic(Request $request){
        $request->validate([
            'musicName' => 'required',
            'musicCover' => 'required|mimes:jpeg,jpg,png,gif|max:10000',
            'music' => 'required|mimes:mp3',
        ]);

        $Name = $request->musicName;
        if($request->hasFile('musicCover')){
            $musicCover = $request->file('musicCover');
            $musicCoverName = $musicCover->getClientOriginalName();
            $musicCoverExt = $musicCover->getClientOriginalExtension();
            $file_to_store = time() . '-' . $musicCoverName ;
            $musicCover->move(public_path('assets/'.'images'.'/musics/'), $file_to_store);
        }
        if($request->hasFile('music')){
            $music = $request->file('music');
            $musicName = $music->getClientOriginalName();
            $musicExt = $music->getClientOriginalExtension();
            $file_to_store1 = time() . '-' . $musicName ;
            $music->move(public_path('assets/'.'images'.'/musics/'), $file_to_store1);
        }

        Music::create([
            'name'=>$Name,
            'image'=>$file_to_store,
            'music' => $file_to_store1
        ]);
        return redirect()->back();
    }

    public function musics($userId){
        $ids =[];
        $myMusics = UserMusic::where('userId',$userId)->get();
        foreach($myMusics as $music){
            $ids [] =  $music->musicId;
        }
        $allMusics = Music::whereNotIn('id',$ids)->get();

        return [
            'myMusics' => $myMusics,
            'allMusics'=>$allMusics
        ];
    }


    public function sports($userId){
        $ids =[];
        $myMusics = UserSport::where('userId',$userId)->get();
        foreach($myMusics as $music){
            $ids [] =  $music->musicId;
        }
        $allMusics = Sport::whereNotIn('id',$ids)->get();

        return [
            'mySports' => $myMusics,
            'allSports'=>$allMusics
        ];
    }
    public function hoppies($userId){
        $ids =[];
        $myMusics = UserHoppy::where('userId',$userId)->get();
        foreach($myMusics as $music){
            $ids [] =  $music->musicId;
        }
        $allMusics = Hoppy::whereNotIn('id',$ids)->get();

        return [
            'myHoppies' => $myMusics,
            'allHoppies'=>$allMusics
        ];
    }
    public function inspirations($userId){
        $ids =[];
        $myMusics = UserInspiration::where('user_id',$userId)->get();

        return [
            'myInspiration' => $myMusics,
            'allInspiration'=>$myMusics
        ];
    }


    public function MusicList(Request $request){
        if($request->state == 0){
            UserMusic::create([
                'userId'=>auth::user()->id,
                'musicId'=>$request->musicId
            ]);
        }else {
            $relation = $request->relation;
            $user_musics = UserMusic::find($relation);
            $userId = auth::user()->id;
            $musicId = $user_musics->music->id;
            $trash = UserMusic::where('userId',$userId)->where('musicId',$musicId)->get();
            foreach($trash as $mus){
                $mus->delete();
            }
        }
        return redirect()->back();
    }
    public function HobbyList(Request $request){
        if($request->state == 0){
            UserHoppy::create([
                'userId'=>auth::user()->id,
                'hoppieId'=>$request->musicId
            ]);
        }else {
            $relation = $request->relation;
            $user_musics = UserHoppy::find($relation);
            $userId = auth::user()->id;
            $musicId = $user_musics->music->id;
            $trash = UserHoppy::where('userId',$userId)->where('hoppieId',$musicId)->get();
            foreach($trash as $mus){
                $mus->delete();
            }
        }
        return redirect()->back();
    }

    public function inspirationList(Request $request){
        if($request->state == 0){
            UserInspiration::create([
                'user_id'=>auth::user()->id,
                'inspirerende_id'=>$request->musicId
            ]);

        }else {
            $relation = $request->relation;
            $user_musics = UserInspiration::find($relation);
            $userId = auth::user()->id;
            $musicId = $user_musics->music->id;
            $trash = UserInspiration::where('user_id',$userId)->where('inspirerende_id',$musicId)->get();
            foreach($trash as $mus){
                $mus->delete();
            }
        }
        return redirect()->back();
    }
    public function SportList(Request $request){
        if($request->state == 0){
            UserSport::create([
                'userId'=>auth::user()->id,
                'sportId'=>$request->musicId
            ]);
        }else {
            $relation = $request->relation;
            $user_musics = UserSport::find($relation);
            $userId = auth::user()->id;
            $musicId = $user_musics->music->id;
            $trash = UserSport::where('userId',$userId)->where('sportId',$musicId)->get();
            foreach($trash as $mus){
                $mus->delete();
            }
        }
        return redirect()->back();
    }


    public function editProfile(Request $request){

        User::update([
            'name'=>$request->name,
            'username'=>$request->username,
            'email'=>$request->email,
            'password'=>$request->password,
            'birthDate'=>$request->birthDate,
            'phone'=>$request->phone,
            'gender'=>$request->gender,
            'city'=>$request->city,
            'country'=>$request->country,
            'type',
            'stateId',
            'jobTitle',
            'verification_code',
            'state',
            'age',
            'official'
        ]);
    }

    private function getPost($user,$post){

        $post->sponsored = false;

        $all_sponsored_posts = DB::select(DB::raw('select categories.id as sponsor_category,sponsored.gender,sponsored.created_at as sponsored_at,sponsored_time.duration,posts.*,sponsored_reach.reach,countries.id as country_id,cities.id as city_id,sponsored_ages.from,sponsored_ages.to from
                                        posts,sponsored,sponsored_reach,sponsored_ages,countries,cities,sponsored_time,categories
                                        where sponsored.postId = posts.id and sponsored.reachId = sponsored_reach.id
                                        and sponsored.age_id = sponsored_ages.id and sponsored.country_id = countries.id
                                        and sponsored.city_id = cities.id and sponsored.timeId = sponsored_time.id and sponsored.category_id = categories.id ORDER BY posts.created_at DESC'));

        foreach ($all_sponsored_posts as $sponsored) {
            if ($sponsored->id == $post->id) {
                $post->sponsored = true;
            }
        }




        if ($post->mentions != null) {
            $post->edit = $post->body;
            $mentions = explode(',', $post->mentions);
            foreach ($mentions as $mention) {
                $mention_id = DB::table('users')->select('id')->where('user_name',$mention)->first();
                $post->body = str_replace('@' . $mention,
                    '<a href="'.route('user.view.profile',$mention_id->id).'" style="color: #ffc107">' . $mention . '</a>',
                    $post->body);
            }
        }
        $post->publisher = User::find($post->publisherId);
        $comments = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->whereNull('comment_id')
            ->limit(5)
            ->offset(0)
            ->orderBy('created_at', 'desc')
            ->get();
        $total_comments_count = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->count();
        $likes = DB::table('likes')->where('model_id', $post->id)->where('model_type', 'post')->get();
        $shares = DB::table('posts')->where('post_id', $post->id)->get()->toArray();
        $post->comments_count = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->whereNull('comment_id')
            ->count();

        $post->comments = $comments;
        $post->likes = $likes;
        $post->type = $post->post_id != null ? 'share' : 'post';

        if (count($likes) > 0) {

            $reacts = DB::table('reacts')->get();

            $stat = '_stat';

            foreach ($reacts as $react){
                ${$react->name_en.$stat} = [];
            }

            foreach ($likes as $like) {
                $reactname = DB::select(DB::raw('select reacts.name_en from likes,reacts
                        where likes.reactId = reacts.id
                    AND likes.reactId = ' . $like->reactId . ' AND likes.senderId = ' . $like->senderId . ' AND
                    likes.model_id = ' . $post->id . ' AND likes.model_type = "post"
                    '));

                $like->publisher = User::find($like->senderId);
                $like->react_name = $reactname[0]->name_en;

                array_push(${$reactname[0]->name_en . $stat}, $like);
            }

            $post->reacts_stat = [];

            foreach ($reacts as $react){
                array_push($post->reacts_stat,${$react->name_en.$stat});
            }
        }

        if ($post->page_id != null) {
            $post->source = "page";
            $page = DB::table('pages')->where('id', $post->page_id)->first();
            $post->isPageAdmin = DB::table('page_members')->where('page_id', $post->page_id)
                ->where('user_id',auth()->user()->id)
                ->where('isAdmin',1)
                ->first();
            $post->page = $page;
        } elseif ($post->group_id != null) {
            $post->source = "group";
            $group = DB::table('groups')->where('id', $post->group_id)->first();
            $post->group = $group;
        } else {
            $post->source = "normal post";
        }

        if ($post->tags != null) {
            $tags_ids = explode(',', $post->tags);
            $post->tags_ids = $tags_ids;
            $tags_info = [];
            $post->tagged = false;
            foreach ($tags_ids as $id) {
                if ($id == $user->id) {
                    $post->tagged = true;
                }
                $tagged_friend = User::find($id);
                array_push($tags_info, $tagged_friend);
            }
            $post->tags_info = $tags_info;
        }

        if ($post->type == 'share') {
            $shared_post = DB::table('posts')->where('id', $post->post_id)->first();
            if ($shared_post->mentions != null) {
                $shared_post->edit = $shared_post->body;
                $mentions = explode(',', $shared_post->mentions);
                foreach ($mentions as $mention) {
                    $mention_id = DB::table('users')->select('id')->where('user_name',$mention)->first();
                    $shared_post->body = str_replace('@' . $mention,
                        '<a href="'.route('user.view.profile',$mention_id->id).'" style="color: #ffc107">' . $mention . '</a>',
                        $shared_post->body);
                }
            }
            $post->media = DB::table('media')->where('model_id', $post->id)->where('model_type', 'post')->get();
            $shared_post->publisher = User::find($shared_post->publisherId);
            $shared_post->media = DB::table('media')->where('model_id', $shared_post->id)->where('model_type', 'post')->get();
            if ($shared_post->page_id != null) {
                $shared_post->source = "page";
                $page = DB::table('pages')->where('id', $shared_post->page_id)->first();
                $shared_post->isPageAdmin = DB::table('page_members')->where('page_id', $shared_post->page_id)
                    ->where('user_id',auth()->user()->id)
                    ->where('isAdmin',1)
                    ->first();
                $shared_post->page = $page;
            } elseif ($shared_post->group_id != null) {
                $shared_post->source = "group";
                $group = DB::table('groups')->where('id', $shared_post->group_id)->first();
                $shared_post->group = $group;
            } else {
                $shared_post->source = "normal post";
            }

            if ($shared_post->tags != null) {
                $tags_ids = explode(',', $shared_post->tags);
                $shared_post->tags_ids = $tags_ids;
                $tags_info = [];
                $shared_post->tagged = false;
                foreach ($tags_ids as $id) {
                    if ($id == $user->id) {
                        $shared_post->tagged = true;
                    }
                    $tagged_friend = User::find($id);
                    array_push($tags_info, $tagged_friend);
                }
                $shared_post->tags_info = $tags_info;
            }

            $shared_post->sponsored = false;

            $post->shared_post = $shared_post;
        } else {
            $post->media = DB::table('media')->where('model_id', $post->id)->where('model_type', 'post')->get();
        }

        $post->comments->count = $total_comments_count;
        $post->likes->count = count($likes);
        $post->shares = count($shares);
        $post->share_details = [];

        if ($post->shares > 0 && $post->type == "post") {
            foreach ($shares as $share) {
                $share->publisher = User::find($share->publisherId);
                array_push($post->share_details, $share);
            }
        }

        $post->liked = DB::table('likes')->where('model_id', $post->id)->where('model_type', 'post')->where('senderId', $user->id)->first();

        if ($post->liked) {
            $post->user_react = DB::table('reacts')->where('id', $post->liked->reactId)->get();
        }

        $post->saved = DB::table('saved_posts')->where('post_id', $post->id)->where('user_id', $user->id)->exists();

        if ($post->comments->count > 0) {
            foreach ($post->comments as $comment) {

                $comment->reported = DB::table('reports')->where('user_id', $user->id)
                    ->where('model_id', $comment->id)->where('model_type', 'comment')->exists();

                $comment->type = $comment->comment_id != null ? 'reply' : 'comment';

                if ($comment->reported == false) {

                    if ($comment->mentions != null) {
                        $comment->edit = $comment->body;
                        $mentions = explode(',', $comment->mentions);
                        foreach ($mentions as $mention) {
                            $mention_id = DB::table('users')->select('id')->where('user_name',$mention)->first();
                            $comment->body = str_replace('@' . $mention,
                                '<a href="'.route('user.view.profile',$mention_id->id).'" style="color: #ffc107">' . $mention . '</a>',
                                $comment->body);
                        }
                    }
                    $comment->publisher = User::find($comment->user_id);
                    $comment->media = DB::table('media')->where('model_id', $comment->id)->where('model_type', 'comment')->first();
                    $comment->replies = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->where('comment_id', $comment->id)->get();
                    $comment->likes = DB::table('likes')->where('model_id', $comment->id)->where('model_type', 'comment')->get();
                    $comment->replies->count = count($comment->replies);
                    $comment->likes->count = count($comment->likes);
                    $comment->liked = DB::table('likes')->where('model_id', $comment->id)->where('model_type', 'comment')->where('senderId', $user->id)->first();

                    if ($comment->liked) {
                        $comment->user_react = DB::table('reacts')->where('id', $comment->liked->reactId)->get();
                    }

                    if (count($comment->likes) > 0) {
                        $reacts = DB::table('reacts')->get();

                        $stat = '_stat';

                        foreach ($reacts as $react){
                            ${$react->name_en.$stat} = [];
                        }
                        foreach ($comment->likes as $like) {
                            $reactname = DB::select(DB::raw('select reacts.name_en from likes,reacts
                                                    where likes.reactId = reacts.id
                                                AND likes.reactId = ' . $like->reactId . ' AND likes.senderId = ' . $like->senderId . ' AND
                                                likes.model_id = ' . $comment->id . ' AND likes.model_type = "comment"
                                                '));

                            $like->publisher = User::find($like->senderId);
                            $like->react_name = $reactname[0]->name_en;

                            array_push(${$reactname[0]->name_en . $stat}, $like);
                        }

                        $comment->reacts_stat = [];

                        foreach ($reacts as $react){
                            array_push($comment->reacts_stat,${$react->name_en.$stat});
                        }
                    }

                    if (count($comment->replies) > 0) {
                        foreach ($comment->replies as $reply) {

                            $reply->reported = DB::table('reports')->where('user_id', $user->id)
                                ->where('model_id', $reply->id)->where('model_type', 'comment')->exists();

                            if ($reply->reported == false) {

                                if ($reply->mentions != null) {
                                    $reply->edit = $reply->body;
                                    $mentions = explode(',', $reply->mentions);
                                    foreach ($mentions as $mention) {
                                        $mention_id = DB::table('users')->select('id')->where('user_name',$mention)->first();
                                        $reply->body = str_replace('@' . $mention,
                                            '<a href="'.route('user.view.profile',$mention_id->id).'" style="color: #ffc107">' . $mention . '</a>',
                                            $reply->body);
                                    }
                                }
                                $reply->publisher = User::find($reply->user_id);
                                $reply->media = DB::table('media')->where('model_id', $reply->id)->where('model_type', 'comment')->first();
                                $reply->likes = DB::table('likes')->where('model_id', $reply->id)->where('model_type', 'comment')->get();
                                $reply->likes->count = count($reply->likes);
                                $reply->liked = DB::table('likes')->where('model_id', $reply->id)->where('model_type', 'comment')->where('senderId', $user->id)->first();

                                if ($reply->liked) {
                                    $reply->user_react = DB::table('reacts')->where('id', $reply->liked->reactId)->get();
                                }

                                if (count($reply->likes) > 0) {
                                    $reacts = DB::table('reacts')->get();

                                    $stat = '_stat';

                                    foreach ($reacts as $react){
                                        ${$react->name_en.$stat} = [];
                                    }
                                    foreach ($reply->likes as $like) {
                                        $reactname = DB::select(DB::raw('select reacts.name_en from likes,reacts
                                                    where likes.reactId = reacts.id
                                                AND likes.reactId = ' . $like->reactId . ' AND likes.senderId = ' . $like->senderId . ' AND
                                                likes.model_id = ' . $reply->id . ' AND likes.model_type = "comment"
                                                '));

                                        $like->publisher = User::find($like->senderId);
                                        $like->react_name = $reactname[0]->name_en;

                                        array_push(${$reactname[0]->name_en . $stat}, $like);
                                    }

                                    $reply->reacts_stat = [];

                                    foreach ($reacts as $react){
                                        array_push($reply->reacts_stat,${$react->name_en.$stat});
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }



        return $post;
    }

}
