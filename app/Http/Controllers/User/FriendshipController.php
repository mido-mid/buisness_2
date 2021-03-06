<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Following;
use App\Models\Friendship;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendshipController extends Controller
{
    //

    use GeneralTrait;

//    public function friendship(Request $request)
//    {
//        $requestType = $request->requestType;
//        switch ($requestType) {
//            case 'addFriendRequest':
//                $senderId = auth()->user()->id;
//                $receiverId = $request->receiverId;
//                $state = 3;
//                $friendRequest = DB::table('friendships')->insert([
//                    'senderId' => $senderId,
//                    'receiverId' => $receiverId,
//                    'stateId' => $state,
//                ]);
//                //Sender is the follower
//                //Receiver is the following
//                $follower = $senderId;
//                $following = $receiverId;
//
//                $followedBefore = DB::table('following')->where('followerId',$follower)
//                    ->where('followingId',$following)->exists();
//
//                if(!$followedBefore) {
//                    $followingRequest = DB::table('following')->insert([
//                        'followerId' => $follower,
//                        'followingId' => $following,
//                    ]);
//                }
//                return $this->returnSuccessMessage('remove request');
//
//            case 'removeFriendRequest':
//                $senderId = auth()->user()->id;
//                $receiverId = $request->receiverId;
//                $friendRequest = DB::table('friendships')->where('senderId',$senderId)
//                    ->where('receiverId',$receiverId)->delete();
//                $followingRequest = DB::table('following')->where('followerId',$senderId)
//                    ->where('followingId',$receiverId)->delete();
//                return $this->returnSuccessMessage('add friend');
//
//            case 'acceptFriendRequest':
//                $friendshipId = $request->friendshipId;
//                $friendshipRecord = Friendship::find($friendshipId);
//                $senderId = $friendshipRecord->senderId;
//                $receiverId = $friendshipRecord->receiverId;
//                //Receiver is the follower
//                //Sender is the following
//                $follower = $receiverId;
//                $following = $senderId;
//                $followingRequest = Following::create([
//                    'followerId' => $follower,
//                    'followingId' => $following,
//                ]);
//                $friendshipRecord->stateId = 1;
//                $friendshipRecord->save();
//                $msg = 'You have accepted the request successfully';
//                return $this->returnSuccessMessage($msg, 200);
//            case 'refuseFriendRequest':
//                $friendshipId = $request->friendshipId;
//                $friendshipRecord = Friendship::destroy($friendshipId);
//                $msg = 'You have refused the request successfully';
//                return $this->returnSuccessMessage($msg, 200);
//            case 'sentRequest':
//                $senderId = $request->senderId;
//                $friendshipRecord = Friendship::where('senderId',$senderId)->get();
//                $msg = 'sentRequest';
//                return $this->returnData(['sentRequest'], [$friendshipRecord], 'sentRequest');
//            case 'receivedRequest':
//                $receiverId = $request->receiverId;
//                $friendshipRecord = Friendship::where('receiverId',$receiverId)->get();
//                $msg = 'receivedRequest';
//                return $this->returnData(['receivedRequest'], [$friendshipRecord], 'receivedRequests');
//        }
//    }

    public function friendship(Request $request)
    {
        $requestType = $request->requestType;
        switch ($requestType) {
            case 'addFriendRequest':
                $senderId = $request->senderId;
                $receiverId = $request->receiverId;
                $state = 3;
                $friendRequest = DB::table('friendships')->insert([
                    'senderId' => $senderId,
                    'receiverId' => $receiverId,
                    'stateId' => $state,
                ]);
                //Sender is the follower
                //Receiver is the following
                $follower = $senderId;
                $following = $receiverId;

                $followedBefore = DB::table('following')->where('followerId',$follower)
                    ->where('followingId',$following)->exists();

                if(!$followedBefore) {
                    $followingRequest = DB::table('following')->insert([
                        'followerId' => $follower,
                        'followingId' => $following,
                    ]);
                }
                return response()->json([
                    'msg' => trans('groups.un_friend_request'),
                    'state' => 'remove request',
                ]);

            case 'removeFriendRequest':
                $senderId = $request->senderId;
                $receiverId = $request->receiverId;
                $friendRequest = DB::table('friendships')->where('senderId',$senderId)
                    ->where('receiverId',$receiverId)->delete();
                $followingRequest = DB::table('following')->where('followerId',$senderId)
                    ->where('followingId',$receiverId)->delete();

                $user_id = $receiverId == auth()->user()->id ? $senderId : $receiverId;
                return response()->json([
                    'msg' => trans('groups.add_friend'),
                    'state' => 'add friend',
                    'sender' => auth()->user()->id,
                    'receiver' => $user_id
                ]);

            case 'acceptFriendRequest':
                $senderId = $request->senderId;
                $receiverId = $request->receiverId;
                $friendshipRecord = DB::table('friendships')->where('senderId',$senderId)
                    ->where('receiverId',$receiverId)->update([
                        'stateId' => 2
                    ]);
                //Receiver is the follower
                //Sender is the following
                $follower = $receiverId;
                $following = $senderId;
                $followingRequest = Following::create([
                    'followerId' => $follower,
                    'followingId' => $following,
                ]);
                $user_id = $receiverId == auth()->user()->id ? $senderId : $receiverId;
                $user = User::find($user_id);
                $user->friendship = 'unfriend';
                $user->request_type = 'removeFriendRequest';
                $user->sender = $user->id;
                $user->receiver = auth()->user()->id;

                $view = view('includes.partialfriendship', compact('user'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['friendship'];
            case 'refuseFriendRequest':
                $senderId = $request->senderId;
                $receiverId = $request->receiverId;
                $friendRequest = DB::table('friendships')->where('senderId',$senderId)
                    ->where('receiverId',$receiverId)->delete();
                $user_id = $receiverId == auth()->user()->id ? $senderId : $receiverId;
                $user = User::find($user_id);
                $user->friendship = 'add friend';
                $user->request_type = 'addFriendRequest';
                $user->sender = auth()->user()->id;
                $user->receiver = $user->id;
                $view = view('includes.partialfriendship', compact('user'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['friendship'];

            case 'removeBlockRequest':
                $senderId = $request->senderId;
                $receiverId = $request->receiverId;
                $blockRequest = DB::table('blocks')->where('senderId',$senderId)->where('receiverId',$receiverId)->delete();

                return response()->json([
                    'msg' => trans('home.block'),
                    'state' => 'block',
                ]);

            case 'addBlockRequest':
                $senderId = $request->senderId;
                $receiverId = $request->receiverId;

                $blockRequest = DB::table('blocks')->insert([
                    'senderId' => $senderId,
                    'receiverId' => $receiverId,
                ]);

                return response()->json([
                    'msg' => trans('home.remove_block'),
                    'state' => 'remove block',
                ]);

            case 'sentRequest':
                $senderId = $request->senderId;
                $friendshipRecord = Friendship::where('senderId',$senderId)->get();
                $msg = 'sentRequest';
                return $this->returnData(['sentRequest'], [$friendshipRecord], 'sentRequest');
            case 'receivedRequest':
                $receiverId = $request->receiverId;
                $friendshipRecord = Friendship::where('receiverId',$receiverId)->get();
                $msg = 'receivedRequest';
                return $this->returnData(['receivedRequest'], [$friendshipRecord], 'receivedRequests');
        }
    }

    public function follow(Request $request)
    {
        $follower = auth()->user()->id;
        $following = $request->followingId;

        $following = Following::create([
            'followerId' => $follower,
            'followingId'=> $following,
        ]);
        return response()->json([[
            'message' => 'you have followed this one successfully',
        ]]);
    }

    public function unfollow(Request $request)
    {
        $auth_user = auth()->user()->id;
        $followingId = $request->followingId;
        $following = Following::where('followerId', $auth_user)->where('followingId', $followingId)->get();
        foreach ($following as $follow) {
            $follow->delete();
        }
        return $this->returnSuccessMessage('you are now unfollowing this user', 200);

    }
}
