<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\models\Comment;
use App\models\Likes;
use App\models\Media;
use Illuminate\Http\Request;
use App\models\Following;
use App\models\Friendship;
use App\models\Hoppy;
use App\models\Inspiration;
use App\models\Music;
use App\models\Post;
use App\models\Sport;
use App\models\UserHoppy;
use App\models\UserInspiration;
use App\models\UserMusic;
use App\models\UserSport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use GeneralTrait;

    #region Check
    public $valid_token;
    public $user_verified;
    public $user;

    public function __construct()
    {
        if (auth('api')->user()) {
            $this->valid_token = 1;
            $this->user = auth('api')->user();
            $this->user_verified = $this->user['email_verified_at'];
        } else {
            $this->valid_token = 0;
        }
    }

    public function unValidToken($state)
    {
        if ($state == 0) {
            return $this->returnError('Token is invalid, User is not authenticated', 404);
        }
    }

    public function unVerified($state)
    {
        if ($state == null) {
            return $this->returnError('User is not verified check your email', 404);
        }
    }

    #endregion
    public $myProfile;

    public function getUserById($id)
    {
        return User::find($id);
    }

    public function index($user_id)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $MyId = $this->user->id;

            if ($MyId == $user_id) {
                $this->myProfile = 1;
            } else {
                $this->myProfile = 0;
            }
            $unRelatedUsersIds = $this->getUnRelatedUsersIds($user_id);

            $myProfile = $this->myProfile;
            $profileId = User::find($user_id);
            $profileId->myProfile = $myProfile;

            unset(
                $profileId->email_verified_at,
                $profileId->verification_code,
                $profileId->remember_token,
                $profileId->type,
                $profileId->stateId,
            );
            $this->profileId = $profileId;
            $friends = $this->GetFirends($user_id);
            $followers = $this->GetFollower($user_id);
            $followings = $this->GetFollowing($user_id);

            $Myfollowers = $this->GetFollower($MyId);
            $Myfollowings = $this->GetFollowing($MyId);
            $MyFriends = $this->GetFirends($MyId);

            $firendship_state = $this->CheckUserFriendshipState($MyId, $user_id);
            $following_state = $this->CheckUserFollowingState($MyId, $user_id);
            $inspiration_state = $this->CheckInspiration($MyId, $user_id);

            $pending_send = $this->GetPendingSend($user_id);
            $pending_receive = $this->GetPendingReceive($user_id);


            $profileId->friends_num = count($friends);
            $profileId->followers_num = count($followers);
            $profileId->followings_num = count($followings);


            foreach ($friends as $friend) {
                if ($user_id == $friend->senderId) {
                    $senderId = $this->getUserById($friend->receiverId);
                } else {
                    $senderId = $this->getUserById($friend->senderId);
                }
                $friend->user = [
                    'name' => $senderId->name,
                    'personal_image' => $senderId->personal_image,
                    'id' => $senderId->id,
                    'friendship' => $this->CheckUserFriendshipState($MyId, $friend->id)
                ];
                unset(
                    $friend->created_at,
                    $friend->updated_at,
                    $friend->senderId,
                    $friend->receiverId,
                    $friend->stateId
                );
            }
            foreach ($pending_send as $friend) {
                if ($user_id == $friend->senderId) {
                    $senderId = $this->getUserById($friend->receiverId);
                } else {
                    $senderId = $this->getUserById($friend->senderId);
                }
                $friend->user = [
                    'name' => $senderId->name,
                    'personal_image' => $senderId->personal_image,
                    'id' => $senderId->id,
                    'friendship' => $this->CheckUserFriendshipState($MyId, $friend->id)
                ];
                unset(
                    $friend->created_at,
                    $friend->updated_at,
                    $friend->senderId,
                    $friend->receiverId,
                    $friend->stateId
                );
            }
            foreach ($pending_receive as $friend) {
                if ($user_id == $friend->senderId) {
                    $senderId = $this->getUserById($friend->receiverId);
                } else {
                    $senderId = $this->getUserById($friend->senderId);
                }
                $friend->user = [
                    'name' => $senderId->name,
                    'personal_image' => $senderId->personal_image,
                    'id' => $senderId->id,
                    'friendship' => $this->CheckUserFriendshipState($MyId, $friend->id)
                ];
                unset(
                    $friend->created_at,
                    $friend->updated_at,
                    $friend->senderId,
                    $friend->receiverId,
                    $friend->stateId
                );
            }
            foreach ($followers as $friend) {
                if ($user_id == $friend->followerId) {
                    $senderId = $this->getUserById($friend->followingId);
                } else {
                    $senderId = $this->getUserById($friend->followerId);
                }
                $friend->user = [
                    'name' => $senderId->name,
                    'personal_image' => $senderId->personal_image,
                    'id' => $senderId->id,
                    'friendship' => $this->CheckUserFriendshipState($MyId, $friend->id)
                ];
                unset(
                    $friend->created_at,
                    $friend->updated_at,
                    $friend->senderId,
                    $friend->followerId,
                    $friend->followingId
                );
            }
            foreach ($followings as $friend) {
                if ($user_id == $friend->followerId) {
                    $senderId = $this->getUserById($friend->followingId);
                } else {
                    $senderId = $this->getUserById($friend->followerId);
                }
                $friend->user = [
                    'name' => $senderId->name,
                    'personal_image' => $senderId->personal_image,
                    'id' => $senderId->id,
                    'friendship' => $this->CheckUserFriendshipState($MyId, $friend->id)
                ];
                unset(
                    $friend->created_at,
                    $friend->updated_at,
                    $friend->senderId,
                    $friend->followerId,
                    $friend->followingId
                );
            }

            $profileId->firendship_state = $firendship_state;
            $profileId->following_state = $following_state;
            $profileId->inspiration_state = $inspiration_state;
            $profileId->cover_image = 'https://businesskalied.com/api/business/public/assets/images/users/' . $profileId->cover_image;
            $profileId->personal_image = 'https://businesskalied.com/api/business/public/assets/images/users/' . $profileId->personal_image;


            $data = [
                'data' => $profileId,
                'posts' => $this->posts($user_id),
            ];
            /*  'friends' =>[
                  'count'=> count($friends),
                  //'friends'=>$friends
              ],
              'followers'=>[
                  'count'=> count($followers),
                 // 'followers'=>$followers
              ],
              'followings' =>[
               'count'=>count($followings),
               //'followings'=>$followings
              ],
              'pending_send' => [
                  'count'=>count($pending_send),
                  //'pending_send'=>$pending_send
              ],
              'pending_receive' => [
                  'count'=>count($pending_receive),
                  //'pending_receive'=>$pending_receive
              ],*/
            /*'musics' => $musics, 'sports' => $sports, 'hoppies' => $hoppies,
            'user_id' => $user_id*/
            return $this->returnData(['profile'], [$data]);
        }
    }

    public function posts($user_id)
    {
        $group_posts = Post::where('publisherId', $user_id)->orderBy('created_at', 'ASC')->get();
        foreach ($group_posts as $groupx) {
            $user = $this->getUserById($groupx->publisherId);
            $groupx->user = [
                'name' => $user->name,
                'personal_image' => $user->personal_image
            ];
            unset(
                $groupx->postTypeId,
                $groupx->categoryId,
                $groupx->group_id,
                $groupx->price,
                $groupx->title,
                $groupx->privacyId,
                $groupx->stateId,
                $groupx->publisherId,
                $groupx->page_id,
                $groupx->post_id,
                $groupx->created_at,
                $groupx->updated_at,
            );

            $groupx->likes_num = count(Likes::where('model_type', 'post')->where('model_id', $groupx->id)->get());
            $groupx->comments_num = $this->GetCommentsNumber($groupx->id);
            $groupx->shares_num = $this->getPostshareNumber($user_id, $groupx->id);

            $groupx->images = $this->Fetchmedia($groupx->id, 'image');
            $groupx->videos = $this->Fetchmedia($groupx->id, 'video');
        }
        return $group_posts;
    }
    //Musics
    //view all
    public function musics(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $user_id = $this->user->id;
            $ids = [];
            $myMusics = UserMusic::where('userId', $user_id)->get();
            foreach ($myMusics as $music) {
                $ids [] = $music->musicId;
                $music->musicId = Music::find($music->musicId);
                $music->musicId->image = 'https://businesskalied.com/public/assets/images/musics/' . $music->musicId->image;
                $music->musicId->music = 'https://businesskalied.com/public/assets/images/musics/' . $music->musicId->music;

            }

            $allMusics = Music::whereNotIn('id', $ids)->get();
            $msg = 'View my musics';
            return $this->returnData(['myMusics'], [$myMusics], $msg);
        }
    }

    //add music
    public function addMusic(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $request->validate([
                'musicName' => 'required',
                'musicCover' => 'required|mimes:jpeg,jpg,png,gif|max:10000',
                'music' => 'required|mimes:mp3',
            ]);

            $Name = $request->musicName;
            if ($request->hasFile('musicCover')) {
                $musicCover = $request->file('musicCover');
                $musicCoverName = $musicCover->getClientOriginalName();
                $musicCoverExt = $musicCover->getClientOriginalExtension();
                $file_to_store = time() . '-' . $musicCoverName;
                $file_to_store = str_replace(' ', '', $file_to_store);
                $musicCover->move(public_path('assets/' . 'images' . '/musics/'), $file_to_store);
            }
            if ($request->hasFile('music')) {
                $music = $request->file('music');
                $musicName = $music->getClientOriginalName();
                $musicExt = $music->getClientOriginalExtension();
                $file_to_store1 = time() . '-' . $musicName;
                $file_to_store1 = str_replace(' ', '',$file_to_store1);
                $music->move(public_path('assets/' . 'images' . '/musics/'), $file_to_store1);
            }

            $generated = Music::create([
                'name' => $Name,
                'image' => $file_to_store,
                'music' => $file_to_store1
            ]);
            UserMusic::create([
                'userId' => $this->user->id,
                'musicId' => $generated->id
            ]);
            $generated->image = 'https://businesskalied.com/public/assets/images/musics/' . $generated->image;
            $generated->music = 'https://businesskalied.com/public/assets/images/musics/' . $generated->music;

            return $this->returnData(['add music'], [$generated]);
        }
    }

    public function reomveMusic(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $music = UserMusic::find($request->taregt_id);
            if ($music) {
                Music::find($music->musicId)->delete();
                $music->delete();

                return $this->returnData(['remove music'], [[]], 'Your music deleted successfully');
            } else {
                return $this->returnData(['remove music'], [[]], 'Your music did not deleted successfully');

            }
        }
    }
    //delete music

    //Sports
    public function allsports(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $ids = [];
            $myMusics = UserSport::where('userId', $this->user->id)->get();
            foreach ($myMusics as $music) {
                $ids [] = $music->sportId;
            }
            if (count($ids) > 0) {
                $allMusics = Sport::whereNotIn('id', $ids)->get();
            } else {
                $allMusics = Sport::get();
            }
            if(count($allMusics) > 0 ){
                foreach($allMusics as $music){
                    $music->image = 'https://businesskalied.com/public/assets/images/sports/' . $music->image;
                }
            }


            $msg = 'View all sports';
            return $this->returnData(['allSports'], [$allMusics], $msg);
        }

    }

    /*    public function sports(Request $request)
        {
            if ($this->valid_token == 0) {
                return $this->unValidToken($this->valid_token);
            } else {
                if (!$this->user_verified) {
                    return $this->unVerified($this->user_verified);
                }
                $ids = [];
                $myMusics = UserSport::where('userId', $this->user->id)->get();
                foreach ($myMusics as $music) {
                    $ids [] = $music->sportId;
                    $music->sportId = Sport::find($music->sportId);
                    $music_owner = $this->getUserById($music->userId);
                    $music->music_owner = [
                        'name'=>$music_owner->name,
                        'personal_image'=>$music_owner->personal_image,
                        'user_id'=>$music_owner->id
                    ];
                    $hobby = Sport::find($music->sportId);
                    $hobby->image = 'https://businesskalied.com/public/assets/images/sports/' . $hobby->image;

                    $music->sport =$hobby;

                    unset(
                        $music->user_id,
                        $music->created_at,
                        $music->updated_at,
                    );


                }
                $allMusics = Sport::whereNotIn('id', $ids)->get();

                $msg = 'View my sports';
                return $this->returnData(['mySports'], [$myMusics], $msg);
            }

        }*/
    public function sports(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $ids = [];
            $myMusics = UserSport::where('userId', $this->user->id)->get();
            foreach ($myMusics as $music) {
                $ids [] = $music->sportId;
                $music_owner = $this->getUserById($music->userId);

                $music->owner = [
                    'name'=>$music_owner->name,
                    'personal_image'=>$music_owner->personal_image,
                    'user_id'=>$music_owner->id
                ];
                $hobby = Sport::find($music->sportId);
                $hobby->image = 'https://businesskalied.com/public/assets/images/sports/' . $hobby->image;

                $music->sport =$hobby;

                unset(
                    $music->userId,
                    $music->created_at,
                    $music->updated_at,
                    $music->sportId
                );
            }
            $allMusics = Sport::whereNotIn('id', $ids)->get();
            foreach ($allMusics as $music) {
                $music->sport = Sport::find($music->hoppieId);
                unset(
                    $music->userId,
                    $music->created_at,
                    $music->updated_at,
                    $music->sportId
                );
            }
            $msg = 'View my sports';
            return $this->returnData(['myHoppies'], [$myMusics], $msg);
        }

    }

    public function addSport(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $old = UserSport::where('userId', $this->user->id)->where('sportId', $request->target_id)->get();
            if (count($old) > 0) {
                $msg = ' you added this before';
                return $this->returnData(['mySports'], [[]], $msg);
            } else {
                $userSport = UserSport::create([
                    'userId' => $this->user->id,
                    'sportId' => $request->target_id
                ]);
            }

            if ($userSport) {
                $msg = ' added successfully';
                return $this->returnData(['mySports'], [$userSport], $msg);
            } else {
                $msg = ' error occurred';
                return $this->returnData(['mySports'], [[]], $msg);
            }
        }
    }

    public function reomveSport(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $music = UserSport::find(intval($request->taregt_id));
            if ($music) {
                $music->delete();
                return $this->returnData(['remove sport'], [[]], 'Your sport deleted successfully');
            } else {
                return $this->returnData(['remove sport'], [[]], 'Your sport did not deleted successfully');

            }
        }
    }

    //Hobbies
    public function allhobbies(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $ids = [];
            $myMusics = UserHoppy::where('userId', $this->user->id)->get();
            foreach ($myMusics as $music) {
                $ids [] = $music->hoppieId;
            }

            if (count($ids) > 0) {
                $allMusics = Hoppy::whereNotIn('id', $ids)->get();
            } else {
                $allMusics = Hoppy::get();
            }
            if(count($allMusics) > 0 ){
                foreach($allMusics as $music){
                    $music->image = 'https://businesskalied.com/public/assets/images/hobbies/' . $music->image;
                }
            }
            $msg = 'View all Hobbies';

            return $this->returnData(['allHoppies'], [$allMusics], $msg);
        }

    }

    public function hobbies(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $ids = [];
            $myMusics = UserHoppy::where('userId', $this->user->id)->get();
            foreach ($myMusics as $music) {
                $ids [] = $music->sportId;
                $music_owner = $this->getUserById($music->userId);

                $music->owner = [
                    'name'=>$music_owner->name,
                    'personal_image'=>$music_owner->personal_image,
                    'user_id'=>$music_owner->id
                ];
                $hobby = Hoppy::find($music->hoppieId);
                $hobby->image = 'https://businesskalied.com/public/assets/images/hobbies/' . $hobby->image;

                $music->hobby =$hobby;

                unset(
                    $music->userId,
                    $music->created_at,
                    $music->updated_at,
                    $music->hoppieId
                );
            }
            $allMusics = Hoppy::whereNotIn('id', $ids)->get();
            foreach ($allMusics as $music) {
                $music->hoppy = Hoppy::find($music->hoppieId);
                unset(
                    $music->userId,
                    $music->created_at,
                    $music->updated_at,
                    $music->hoppieId
                );
            }
            $msg = 'View my hoppies';
            return $this->returnData(['myHoppies'], [$myMusics], $msg);
        }

    }

    public function addHobby(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $old = UserHoppy::where('userId', $this->user->id)->where('hoppieId', $request->target_id)->get();
            if (count($old) > 0) {
                $msg = ' you added this before';
                return $this->returnData(['myHoppies'], [[]], $msg);
            } else {
                $userSport = UserHoppy::create([
                    'userId' => $this->user->id,
                    'hoppieId' => $request->target_id
                ]);
            }

            if ($userSport) {
                $msg = ' added successfully';
                return $this->returnData(['myHoppies'], [$userSport], $msg);
            } else {
                $msg = ' error occurred';
                return $this->returnData(['myHoppies'], [[]], $msg);
            }
        }
    }

    public function reomveHobby(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $music = UserHoppy::find(intval($request->taregt_id));
            if ($music) {
                $music->delete();
                return $this->returnData(['remove hoppy'], [[]], 'Your hoppy deleted successfully');
            } else {
                return $this->returnData(['remove hoppy'], [[]], 'Your hoppy did not deleted successfully');

            }
        }
    }

    //Inspirations

    public function inspirations(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $ids = [];
            $myMusics = UserInspiration::where('inspirerende_id', $this->user->id)->get();
            foreach($myMusics as  $groupx){
                $senderId = $this->getUserById($groupx->user_id);
                $groupx->user = [
                    'name' => $senderId->name,
                    'personal_image' =>    'https://businesskalied.com/api/business/public/assets/images/users/' .$senderId->personal_image,
                    'id' => $senderId->id,
                    'friendship' => $this->CheckUserFriendshipState($this->user->id, $groupx->id)
                ];
                unset(
                    $groupx->user_id,
                    $groupx->inspirerende_id
                );
            }
            $msg = 'View my Inspiration';
            return $this->returnData(['myInspiration'], [$myMusics], $msg);
        }
    }
    //Videos
    //Images
    public function getProfileMedia(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $group_posts = Post::where('publisherId', $this->user->id)->orderBy('created_at', 'ASC')->get();
            foreach ($group_posts as $groupx) {
                unset(
                    $groupx->postTypeId,
                    $groupx->categoryId,
                    $groupx->group_id,
                    $groupx->price,
                    $groupx->title,
                    $groupx->privacyId,
                    $groupx->stateId,
                    $groupx->publisherId,
                    $groupx->page_id,
                    $groupx->post_id,
                    $groupx->created_at,
                    $groupx->updated_at,
                );
                $groupx->images = $this->Fetchmedia($groupx->id, 'image');
                $groupx->videos = $this->Fetchmedia($groupx->id, 'video');
            }
            if ($request->type == 'image') {
                foreach ($group_posts as $groupx) {
                    unset(
                        $groupx->videos,
                        $groupx->id,
                        $groupx->body,
                        $groupx->tag,
                        $groupx->country_id,
                        $groupx->city_id,
                        $groupx->mentions,
                        $groupx->tags,
                    );
                }
                return $this->returnData(['media'], [$group_posts],'images');
            } else {
                unset(
                    $groupx->images,
                    $groupx->id,
                    $groupx->body,
                    $groupx->tag,
                    $groupx->country_id,
                    $groupx->city_id,
                    $groupx->mentions,
                    $groupx->tags,
                );

                return $this->returnData(['media'], [$group_posts],'videos');
            }
        }
    }


    public function GetCommentsNumber($post_id)
    {
        $res = 0;
        $comments = Comment::where('model_type', 'post')->where('model_id', $post_id)->get();
        $res += count($comments);
        if (count($comments) > 0) {
            foreach ($comments as $comment) {
                $commentsx = Comment::where('model_type', 'comment')->where('model_id', $comment->id)->get();
                $res += count($commentsx);
            }
        }
        return $res;
    }

    public function Fetchmedia($modal_id, $media_type)
    {
        $media = Media::where('model_type', 'post')->where('model_id', $modal_id)->where('mediaType', $media_type)->get();
        return $media;

    }

    public function getPostshareNumber($group_id, $post_id)
    {
        $posts = Post::where('publisherId', $group_id)->where('post_id', $post_id)->get();

        return count($posts);
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

    function CheckInspiration($user, $enemy)
    {
        $records = Inspiration::where('user_id', $user)->where('inspirerende_id', $enemy)->get();
        if (count($records) > 0) {
            return 1;
        } else {
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

    public function friendshipId($user, $enemy)
    {
        $friendship = Friendship::where('senderId', $user)->where('receiverId', $enemy)->get();
        if (count($friendship) > 0) {
            return $friendship[0]->id;
        } else {
            $friendship = Friendship::where('senderId', $enemy)->where('receiverId', $user)->get();
            if (count($friendship) > 0) {
                return $friendship[0]->id;
            } else {
                return 99;
            }
        }

    }

    function followingId($user, $enemy)
    {
        $friendship = Following::where('followerId', $user)->where('followingId', $enemy)->get();
        if (count($friendship) > 0) {
            return $friendship[0]->id;
        } else {
            $friendship = Following::where('followingId', $enemy)->where('followerId', $user)->get();
            if (count($friendship) > 0) {
                return $friendship[0]->id;
            } else {
                return 99;
            }
        }
    }

    function inspirationId($user, $enemy)
    {
        $records = Inspiration::where('user_id', $user)->where('inspirerende_id', $enemy)->get();
        if (count($records) > 0) {
            return $records[0]->id;
        } else {
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
        $trash = Following::where('followingId', $followingId)->where('followerId', $followerId)->get();
        foreach ($trash as $t) {
            $t->delete();
        }
        $html = $this->rednderButtonsProfile($receiverFriend);
        return $html;
    }

    public function addInspirationProfile(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            //Iam the sender
            $senderId = $request->senderId;
            $receiverId = $this->user->id;
            $count = Inspiration::where('user_id',$senderId)->where('inspirerende_id',$receiverId)->get();
            if(count($count) > 0){
                return $this->returnSuccessMessageWithStatus('error while adding new inspiration he is actually your inspiration', 401, false);
            }else {
                $inspiration = Inspiration::create([
                    'user_id' => $senderId,
                    'inspirerende_id' => $receiverId
                ]);
            }
            if ($inspiration) {
                return $this->returnSuccessMessageWithStatus('you added new inspiration', 200, true);
            }
            return $this->returnSuccessMessageWithStatus('error while adding new inspiration', 401, false);
        }

    }

    public function removeInspirationProfile(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }

            $user_id = $this->user->id;
            $inspirerende_id = $request->inspirerende_id;
            $old = Inspiration::where('user_id', $inspirerende_id)->where('inspirerende_id', $user_id)->get();
            if(count($old) > 0) {
                foreach ($old as $o) {
                    $o->delete();
                }
                return $this->returnSuccessMessageWithStatus('you removed your inspiration', 200, true);
            }else{
                return $this->returnSuccessMessageWithStatus('error while removing inspiration', 401, false);

            }

        }

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


    //add music

    public function MusicList(Request $request)
    {
        if ($request->state == 0) {
            UserMusic::create([
                'userId' => auth::user()->id,
                'musicId' => $request->musicId
            ]);
        } else {
            $relation = $request->relation;
            $user_musics = UserMusic::find($relation);
            $userId = auth::user()->id;
            $musicId = $user_musics->music->id;
            $trash = UserMusic::where('userId', $userId)->where('musicId', $musicId)->get();
            foreach ($trash as $mus) {
                $mus->delete();
            }
        }
        return redirect()->back();
    }

    public function HobbyList(Request $request)
    {
        if ($request->state == 0) {
            UserHoppy::create([
                'userId' => auth::user()->id,
                'hoppieId' => $request->musicId
            ]);
        } else {
            $relation = $request->relation;
            $user_musics = UserHoppy::find($relation);
            $userId = auth::user()->id;
            $musicId = $user_musics->music->id;
            $trash = UserHoppy::where('userId', $userId)->where('hoppieId', $musicId)->get();
            foreach ($trash as $mus) {
                $mus->delete();
            }
        }
        return redirect()->back();
    }

    public function inspirationList(Request $request)
    {
        if ($request->state == 0) {
            UserInspiration::create([
                'user_id' => auth::user()->id,
                'inspirerende_id' => $request->musicId
            ]);

        } else {
            $relation = $request->relation;
            $user_musics = UserInspiration::find($relation);
            $userId = auth::user()->id;
            $musicId = $user_musics->music->id;
            $trash = UserInspiration::where('user_id', $userId)->where('inspirerende_id', $musicId)->get();
            foreach ($trash as $mus) {
                $mus->delete();
            }
        }
        return redirect()->back();
    }

    public function SportList(Request $request)
    {
        if ($request->state == 0) {
            UserSport::create([
                'userId' => auth::user()->id,
                'sportId' => $request->musicId
            ]);
        } else {
            $relation = $request->relation;
            $user_musics = UserSport::find($relation);
            $userId = auth::user()->id;
            $musicId = $user_musics->music->id;
            $trash = UserSport::where('userId', $userId)->where('sportId', $musicId)->get();
            foreach ($trash as $mus) {
                $mus->delete();
            }
        }
        return redirect()->back();
    }


    public function editProfile(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }

            if ($request->name) {
                $name = $request->name;
            } else {
                $name = $this->user->name;
            }
            if ($request->username) {
                $username = $request->username;
            } else {
                $username = $this->user->user_name;
            }
            if ($request->email) {
                $email = $request->email;
            } else {
                $email = $this->user->email;
            }
            if ($request->birthDate) {
                $birthDate = $request->birthDate;
            } else {
                $birthDate = $this->user->birthDate;
            }
            if ($request->phone) {
                $phone = $request->phone;
            } else {
                $phone = $this->user->phone;
            }
            if ($request->gender) {
                $gender = $request->gender;
            } else {
                $gender = $this->user->gender;
            }
            if ($request->city) {
                $city = $request->city;
            } else {
                $city = $this->user->city_id;
            }
            if ($request->country) {
                $country = $request->country;
            } else {
                $country = $this->user->country_id;
            }
            if ($request->jobTitle) {
                $jobTitle = $request->jobTitle;
            } else {
                $jobTitle = $this->user->jobTitle;
            }
            if ($request->businessType) {
                $businessType = $request->businessType;
            } else {
                $businessType = $this->user->businessType;
            }
            if ($request->Specialty) {
                $Specialty = $request->Specialty;
            } else {
                $Specialty = $this->user->Specialty;
            }
            $this->user->update([
                'name' => $name,
                'user_name' => $username,
                'email' => $email,
                'birthDate' => $birthDate,
                'phone' => $phone,
                'gender' => $gender,
                'city_id' => $city,
                'country_id' => $country,
                'jobTitle' => $jobTitle,
                'businessType' => $businessType,
                'Specialty' => $Specialty
            ]);
            $msg = 'user updated successfully';
            return $this->returnData(['client'], [$this->user], $msg);

        }
    }
    public function editImage(Request $request){
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $profile_image = time() . '.' . $request->image->extension();
            $request->image->move(public_path('assets/images/users/'), $profile_image);
            switch($request->type){
                case 'cover':
                    $this->user->update([
                        'cover_image' => $profile_image
                    ]);
                    break;
                case 'profile':
                    $this->user->update([
                        'personal_image' =>$profile_image
                    ]);
                    break;
            }
            $msg = 'image updated successfully';
            $this->user->cover_image = 'https://businesskalied.com/api/business/public/assets/images/users/' . $this->user->cover_image;
            $this->user->personal_image = 'https://businesskalied.com/api/business/public/assets/images/users/' . $this->user->personal_image;

            return $this->returnData(['client'], [$this->user], $msg);

        }
    }
}

