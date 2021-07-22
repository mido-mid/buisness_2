<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Group;
use App\Models\Page;
use App\Models\Post;
use App\Models\Report;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{

    use GeneralTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $friends_posts = [];
        $expected_ids = [];
        $groups_posts = [];
        $pages_posts = [];
        $friends_stories = [];
        $user_groups_ids = [];
        $user_pages_ids = [];
        $friends_info = [];


        // friends posts he follows and are public and in groups you are in and in pages you liked
        $friends = DB::table('friendships')->where(function ($q) use ($user){
            $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
        })->where('stateId',2)->get();

        $friends_ids = [];

        foreach ($friends as $friend2){
            $friend2_id = $friend2->receiverId == $user->id ? $friend2->senderId : $friend2->receiverId;
            array_push($friends_ids,$friend2_id);
        }

        array_push($friends_ids,$user->id);

        $user_groups = DB::select(DB::raw('select groups.*,group_members.stateId from groups,group_members
                        where group_members.group_id = groups.id
                        AND group_members.user_id = '.$user->id.' and group_members.stateId in (2,3)'));

        $user_pages = DB::select(DB::raw('select pages.* from pages,user_pages
                        where user_pages.page_id = pages.id
                        AND user_pages.user_id = '.$user->id));

        $user_follows = DB::select(DB::raw('select users.* from users,following
                        where following.followingId = users.id
                        AND following.followerId = '.$user->id));

        //his posts, shares , friends posts who are followed
        $user_posts = DB::table('posts')->where('publisherId',$user->id)->where('postTypeId',2)->where('stateId',2)->get()->toArray();

        foreach ($friends as $friend){
            $friend_id = $friend->receiverId == $user->id ? $friend->senderId : $friend->receiverId;
            if (($key = array_search($friend_id, $friends_ids)) !== false) {
                unset($friends_ids[$key]);
            }

            $friend_followed = DB::table('following')->where('followerId',$user->id)->where('followingId',$friend_id)->exists();

            if($friend_followed){
                $friend_posts = DB::table('posts')->where('publisherId',$friend_id)->where('postTypeId',2)->where('stateId',2)->where('privacyId',1)->get();

                foreach ($friend_posts as $post){
                    $post->reported = DB::table('reports')->where('user_id', $user->id)
                        ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                    if($post->reported == false) {
                        $post->type = $post->post_id == null ? "post" : "share";
                        $post->source = "friend";
                        array_push($friends_posts,$post);
                    }
                }
            }

            $friend_stories = DB::table('stories')->where('publisherId',$friend_id)->where('privacyId',1)->get();

            $friends_of_friend = DB::table('friendships')->where(function ($q) use($friend_id){
                $q->where('senderId', $friend_id)->orWhere('receiverId', $friend_id);
            })->where('stateId',2)->whereNotIn('receiverId',$friends_ids)->whereNotIn('senderId',$friends_ids)->get();

            array_push($friends_ids,$friend_id);

            foreach ($friend_stories as $story){
                array_push($friends_stories,$story);
            }

            foreach ($friends_of_friend as $user_friend){
                $friend_of_friend_id = $user_friend->receiverId == $friend_id ? $user_friend->senderId : $user_friend->receiverId;
                $friend_request = DB::table('friendships')->where(function ($q) use ($user){
                    $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
                })->where(function ($q) use ($friend_of_friend_id){
                    $q->where('senderId', $friend_of_friend_id)->orWhere('receiverId', $friend_of_friend_id);
                })->whereIn('stateId',[1,3])->exists();
                if($friend_request == false){
                    array_push($expected_ids,$friend_of_friend_id);
                }
            }

            $friend_info = DB::table('users')->select('id','name')->where('id',$friend_id)->first();

            array_push($friends_info,$friend_info);
        }


        foreach ($user_groups as $group){
            if($group->stateId == 2){
                $group_posts = DB::table('posts')->where('group_id',$group->id)->get();
                foreach ($group_posts as $post){
                    $post->reported = DB::table('reports')->where('user_id', $user->id)
                        ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                    if($post->reported == false) {
                        $post->type = $post->post_id == null ? "post" : "share";
                        array_push($groups_posts, $post);
                    }
                }
            }
            array_push($user_groups_ids,$group->id);
        }

        foreach ($user_pages as $page){
            $page_posts = DB::table('posts')->where('page_id',$page->id)->get();
            array_push($user_pages_ids,$page->id);
            foreach ($page_posts as $post){
                $post->type = $post->post_id == null ? "post" : "share";
                array_push($pages_posts,$post);
            }
        }

        $expected_users = $this->getExpectedFriends($user,$expected_ids);

        $posts = $this->getPosts($user,$user_follows,$friends_posts,$user_pages,$user_groups,$user_posts);

        $stories = $this->getStories($user,$friends_stories);

        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.categoryId = categories.id
                        AND user_categories.userId ='.$user->id .' and categories.type = "post"'));

        $user_interests_array = [];

        foreach ($user_interests as $interest){
            array_push($user_interests_array,$interest->id);
        }

        $expected_posts = $this->getExpectedPosts($user,$user_interests_array);

        $expected_groups = $this->getExpectedGroups($user_interests_array,$user_groups_ids);

        $expected_pages = $this->getExpectedPages($user_interests_array,$user_pages_ids);

        $privacy = DB::table('privacy_type')->get();

        $categories = DB::table('categories')->where('type','post')->get();

        $times = DB::table('sponsored_time')->get();

        $reaches = DB::table('sponsored_reach')->get();

        $ages = DB::table('sponsored_ages')->get();

        $reacts = DB::table('reacts')->get();


        return view('home', compact('posts', 'stories', 'expected_users', 'expected_groups', 'expected_pages', 'expected_posts','privacy','categories','times','ages','reaches','reacts','friends_info'));
    }


    public function getPosts($user,$user_follows,$friends_posts,$user_pages,$user_groups,$user_posts){

        $pages_posts = [];

        $user_pages_ids = [];

        $follows_posts = [];

        $user_groups_ids = [];

        $groups_posts = [];

        foreach ($user_groups as $group) {

            if($group->stateId == 2) {
                $group_posts = DB::table('posts')->where('group_id', $group->id)->get();
                foreach ($group_posts as $post) {
                    $post->reported = DB::table('reports')->where('user_id', $user->id)
                        ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                    if($post->reported == false) {
                        $post->type = $post->post_id == null ? "post" : "share";
                        $post->source = "group";
                        $post->page = $group;
                        array_push($groups_posts, $post);
                    }
                }
            }

            array_push($user_groups_ids, $group->id);
        }


        foreach ($user_pages as $page) {
            $page_posts = DB::table('posts')->where('page_id', $page->id)->get();
            foreach ($page_posts as $post) {
                $post->reported = DB::table('reports')->where('user_id', $user->id)
                    ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                if($post->reported == false) {
                    $post->type = $post->post_id == null ? "post" : "share";
                    $post->source = "page";
                    $post->page = $page;
                    array_push($pages_posts, $post);
                }
            }
            array_push($user_pages_ids, $page->id);
        }

        foreach ($user_follows as $followed_user) {
            $is_friend = DB::table('friendships')->where(function ($q) use ($user){
                $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
            })->where(function ($q) use ($followed_user){
                $q->where('senderId', $followed_user->id)->orWhere('receiverId', $followed_user->id);
            })->where('stateId',2)->exists();

            if($is_friend == false){
                $followed_user_posts = DB::table('posts')->where('publisherId', $followed_user->id)->where('postTypeId',2)->where('stateId',2)->where('privacyId',1)->get();
                foreach ($followed_user_posts as $post) {
                    $post->reported = DB::table('reports')->where('user_id', $user->id)
                        ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                    if($post->reported == false) {
                        $post->type = $post->post_id == null ? "post" : "share";
                        $post->source = "follow";
                        array_push($follows_posts, $post);
                    }
                }
            }
        }

        foreach ($user_posts as $post){
            $post->type = $post->post_id == null ? "post" : "share";
            $post->source = "user";
        }

        $user_sponsored_posts = $this->getSponsoredPosts();


        $posts = array_merge($follows_posts,$user_posts,$friends_posts,$groups_posts,$pages_posts,$user_sponsored_posts);

        foreach ($posts as $post){
            $post->publisher = User::find($post->publisherId);
            $comments = DB::table('comments')->where('model_id',$post->id)->where('model_type','post')->get();
            $likes = DB::table('likes')->where('model_id',$post->id)->where('model_type','post')->get();
            $shares = DB::table('posts')->where('post_id',$post->id)->get()->toArray();

            $post->comments = $comments;
            $post->likes = $likes;
            $post->type = $post->post_id != null ? 'share' : 'post';

            if($post->tags != null){
                $tags_ids = explode(',',$post->tags);
                $tags_info = [];
                $post->tagged = false;
                foreach ($tags_ids as $id){
                    if($id == $user->id){
                        $post->tagged = true;
                    }
                    $tagged_friend = User::find($id);
                    array_push($tags_info,$tagged_friend);
                }
                $post->tags_info = $tags_info;
            }

            if ($post->type == 'share'){
                $shared_post = DB::table('posts')->where('id',$post->post_id)->first();
                if($shared_post->post_id != null) {
                    $post->media = DB::table('media')->where('model_id',$shared_post->post_id)->where('model_type','post')->get();
                }
                else{
                    $post->media = DB::table('media')->where('model_id',$post->post_id)->where('model_type','post')->get();
                }
            }else{
                $post->media = DB::table('media')->where('model_id',$post->id)->where('model_type','post')->get();
            }

            $post->comments->count = count($comments);
            $post->likes->count = count($likes);
            $post->shares = count($shares);

            $post->liked = DB::table('likes')->where('model_id',$post->id)->where('model_type','post')->where('senderId',$user->id)->first();

            if($post->liked){
                $post->user_react = DB::table('reacts')->where('id',$post->liked->reactId)->get();
            }

            $post->saved = DB::table('saved_posts')->where('post_id',$post->id)->where('user_id',$user->id)->exists();

            if($post->comments->count > 0) {
                foreach ($post->comments as $comment) {
                    $comment->publisher = User::find($comment->user_id);
                    $comment->media = DB::table('media')->where('model_id',$comment->id)->where('model_type','comment')->first();
                }
            }
        }
        return $posts;
    }

    public function getStories($user,$friends_stories){
        $user_stories = DB::table('stories')->where('publisherId',$user->id)->get()->toArray();

        $stories = array_merge($user_stories,$friends_stories);

        foreach ($stories as $story){
            $story->publisher = User::find($story->publisherId);
            $story->viewers = DB::select(DB::raw('select users.* from users,stories_views
                        where stories_views.story_id ='. $story->id.
                        ' AND stories_views.user_id = users.id'));
            $story->media = DB::table('media')->where('model_id',$story->id)->where('model_type','story')->first();
        }

        return $stories;
    }


    public function getExpectedFriends($user,$expected_ids)
    {

        $expected_friends = [];

        $expected_people = array_unique($expected_ids);

        foreach ($expected_people as $id){
            $user_id = $user->id;
            $friendship = DB::table('friendships')->where(function ($q) use($id){
                $q->where('senderId', $id)->orWhere('receiverId', $id);
            })->where(function ($q) use($user_id){
                $q->where('senderId', $user_id)->orWhere('receiverId', $user_id);
            })->whereIn('stateId',[1,3])->first();
            if($friendship == null){
                $friends_of_friend_info = DB::table('users')->where('id',$id)->first();
                array_push($expected_friends,$friends_of_friend_info);
            }
        }


        foreach ($expected_friends as $friend){
            $friend->followers = DB::table('following')->where('followingId',$friend->id)->count();
        }

        shuffle($expected_friends);

        $expected_users = array_slice($expected_friends,0,3);

        if(count($expected_users) == 0){
            $expected_people = DB::table('users')->where('country',auth()->user()->country)->where('id','!=',auth()->user()->id)->get();
            foreach ($expected_people as $expected_user){
                $expected_user_id = $expected_user->id;
                $expected_user->followers = DB::table('following')->where('followingId',$expected_user->id)->count();
                $friendship = DB::table('friendships')->where(function ($q) use($expected_user_id){
                    $q->where('senderId', $expected_user_id)->orWhere('receiverId', $expected_user_id);
                })->where(function ($q){
                    $q->where('senderId', auth()->user()->id)->orWhere('receiverId', auth()->user()->id);
                })->whereIn('stateId',[1,3])->first();
                if($friendship == null){
                    array_push($expected_users,$expected_user);
                }
            }
        }

        return $expected_users;
    }


    public function getSponsoredPosts()
    {
        $user_sponsored_posts = [];

        $all_sponsored_posts = DB::select(DB::raw('select sponsored.gender,sponsored.created_at as sponsored_at,sponsored_time.duration,posts.*,sponsored_reach.reach,countries.name as country_name,cities.name as city_name,sponsored_ages.from,sponsored_ages.to from
                                        posts,sponsored,sponsored_reach,sponsored_ages,countries,cities,sponsored_time
                                        where sponsored.postId = posts.id and sponsored.reachId = sponsored_reach.id
                                        and sponsored.age_id = sponsored_ages.id and sponsored.country_id = countries.id
                                        and sponsored.city_id = cities.id and sponsored.timeId = sponsored_time.id'));

        foreach ($all_sponsored_posts as $post){
            $post_users = DB::table('users')->whereBetween('age',[$post->from,$post->to])
                ->where('country',$post->country_name)
                ->where('city',$post->city_name)
                ->where('gender',$post->gender)
                ->limit($post->reach)->pluck('id')->toArray();

            $post->type = 'sponsored';

            if( in_array(1,$post_users) != false && Carbon::parse( $post->sponsored_at)->addDays(7) >= Carbon::today()){
                $post->reported = DB::table('reports')->where('user_id', auth()->user()->id)
                    ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                if($post->reported == false) {
                    array_push($user_sponsored_posts,$post);
                }
            }
        }

        return $user_sponsored_posts;
    }

    public function getExpectedPosts($user,$user_interests_array){

//public posts having same interest of user
        $expected_posts = DB::table('posts')->whereIn('categoryId',$user_interests_array)->where('publisherId','!=',$user->id)->where('privacyId',1)->where('postTypeId',2)->whereNull('post_id')->limit(3)->get();
        foreach ($expected_posts as $post){
            $post->publisher = User::find($post->publisherId);
            $post->media = DB::table('media')->where('model_id',$post->id)->where('model_type','post')->get();
        }

        return $expected_posts;
    }

    public function getExpectedGroups($user_interests_array,$user_groups_ids){
        $expected_groups = Group::whereIn('category_id',$user_interests_array)->whereNotIn('id',$user_groups_ids)->limit(3)->get();

        foreach ($expected_groups as $group){
            $group_members_count = DB::select(DB::raw('select count(group_members.id) as count from group_members
                        where group_members.group_id ='.$group->id .' and group_members.stateId = 2'
            ))[0]->count;

            $group->members = $group_members_count;
        }

        return $expected_groups;
    }

    public function getExpectedPages($user_interests_array,$user_pages_ids){

        $expected_pages = Page::whereIn('category_id',$user_interests_array)->whereNotIn('id',$user_pages_ids)->limit(3)->get();

        foreach ($expected_pages as $page){
            $page_likes_count = DB::select(DB::raw('select count(user_pages.id) as count from user_pages
                        where user_pages.page_id ='.$page->id
            ))[0]->count;

            $page->members = $page_likes_count;
        }

        return $expected_pages;
    }

    public function report(Request $request){

        $user = auth()->user();

        $rules = [
            'body' => 'required','not_regex:/([%\$#\*<>]+)/',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return $this->returnValidationError(402,$validator);
        }

        $report = Report::create([
            'body' => $request->body,
            'user_id' => $user->id,
            'stateId' => 3,
            'model_id' => $request->model_id,
            'model_type' => $request->model_type,
        ]);

        if($report){
            return $this->returnSuccessMessage('report sent successfully');
        }
        else{
            return $this->returnError('something wrong happened',402);
        }
    }




//    public function index()
//    {
//        $user = auth()->user();
//
//        $friends_posts = [];
//        $expected_ids = [];
//        $groups_posts = [];
//        $pages_posts = [];
//        $friends_stories = [];
//        $expected_friends = [];
//        $user_sponsored_posts = [];
//        $user_groups_ids = [];
//        $user_pages_ids = [];
//        // friends posts he follows and are public and in groups you are in and in pages you liked
//        $friends = DB::table('friendships')->where(function ($q) use ($user){
//            $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
//        })->where('stateId',2)->get();
//
//        $friends_ids = [];
//
//        foreach ($friends as $friend2){
//            $friend2_id = $friend2->receiverId == $user->id ? $friend2->senderId : $friend2->receiverId;
//            array_push($friends_ids,$friend2_id);
//        }
//
//        array_push($friends_ids,$user->id);
//
//        $all_sponsored_posts = DB::select(DB::raw('select sponsored.gender,sponsored.created_at as sponsored_at,sponsored_time.duration,posts.*,sponsored_reach.reach,countries.name as country_name,cities.name as city_name,sponsored_ages.from,sponsored_ages.to from
//                                        posts,sponsored,sponsored_reach,sponsored_ages,countries,cities,sponsored_time
//                                        where sponsored.postId = posts.id and sponsored.reachId = sponsored_reach.id
//                                        and sponsored.age_id = sponsored_ages.id and sponsored.country_id = countries.id
//                                        and sponsored.city_id = cities.id and sponsored.timeId = sponsored_time.id'));
//
//        foreach ($all_sponsored_posts as $post){
//            $post_users = DB::table('users')->whereBetween('age',[$post->from,$post->to])
//                ->where('country',$post->country_name)
//                ->where('city',$post->city_name)
//                ->where('gender',$post->gender)
//                ->limit($post->reach)->pluck('id')->toArray();
//
//            $post->type = 'sponsored';
//
//            if( in_array(1,$post_users) != false && Carbon::parse( $post->sponsored_at)->addDays(7) >= Carbon::today()){
//                array_push($user_sponsored_posts,$post);
//            }
//        }
//
//
//        $user_groups = DB::select(DB::raw('select groups.* from groups,group_members
//                        where group_members.group_id = groups.id
//                        AND group_members.user_id = 1 and group_members.stateId = 1'));
//
//        $user_pages = DB::select(DB::raw('select pages.* from pages,user_pages
//                        where user_pages.page_id = pages.id
//                        AND user_pages.user_id = 1'));
//
//        //his posts, shares , friends posts who are followed
//        $user_stories = DB::table('stories')->where('publisherId',$user->id)->get()->toArray();
//        $user_posts = DB::table('posts')->where('publisherId',$user->id)->where('postTypeId',2)->where('stateId',2)->get()->toArray();
//        foreach ($friends as $friend){
//            $friend_id = $friend->receiverId == $user->id ? $friend->senderId : $friend->receiverId;
//            if (($key = array_search($friend_id, $friends_ids)) !== false) {
//                unset($friends_ids[$key]);
//            }
//            $friend_posts = DB::table('posts')->where('publisherId',$friend_id)->where('postTypeId',2)->where('stateId',2)->get();
//            $friend_stories = DB::table('stories')->where('publisherId',$friend_id)->get();
//
//            $friends_of_friend = DB::table('friendships')->where(function ($q) use($friend_id){
//                $q->where('senderId', $friend_id)->orWhere('receiverId', $friend_id);
//            })->where('stateId',2)->whereNotIn('receiverId',$friends_ids)->whereNotIn('senderId',$friends_ids)->get();
//
//            array_push($friends_ids,$friend_id);
//
//            foreach ($friend_posts as $post){
//                $post->type = $post->post_id == null ? "post" : "share";
//                array_push($friends_posts,$post);
//            }
//
//            foreach ($friend_stories as $story){
//                array_push($friends_stories,$story);
//            }
//
//            foreach ($friends_of_friend as $user_friend){
//                $friend_of_friend_id = $user_friend->receiverId == $friend_id ? $user_friend->senderId : $user_friend->receiverId;
//                array_push($expected_ids,$friend_of_friend_id);
//            }
//        }
//
//        $expected_people = array_unique($expected_ids);
//
//        foreach ($expected_people as $id){
//            $user_id = $user->id;
//            $friendship = DB::table('friendships')->where(function ($q) use($id){
//                $q->where('senderId', $id)->orWhere('receiverId', $id);
//            })->where(function ($q) use($user_id){
//                $q->where('senderId', $user_id)->orWhere('receiverId', $user_id);
//            })->where('stateId',3)->first();
//            if(!$friendship){
//                $friends_of_friend_info = DB::table('users')->where('id',$id)->first();
//                array_push($expected_friends,$friends_of_friend_info);
//            }
//        }
//
//        foreach ($expected_friends as $friend){
//            $friend->followers = DB::table('following')->where('followingId',$friend->id)->count();
//        }
//
//        shuffle($expected_friends);
//
//        $expected_users = array_slice($expected_friends,0,3);
//
//        foreach ($user_groups as $group){
//            $group_posts = DB::table('posts')->where('group_id',$group->id)->get();
//            array_push($user_groups_ids,$group->id);
//            foreach ($group_posts as $post){
//                $post->type = $post->post_id == null ? "post" : "share";
//                array_push($groups_posts,$post);
//            }
//        }
//
//        foreach ($user_pages as $page){
//            $page_posts = DB::table('posts')->where('page_id',$page->id)->get();
//            array_push($user_pages_ids,$page->id);
//            foreach ($page_posts as $post){
//                $post->type = $post->post_id == null ? "post" : "share";
//                array_push($pages_posts,$post);
//            }
//        }
//
//        foreach ($user_posts as $post){
//            $post->type = $post->post_id == null ? "post" : "share";
//        }
//
//
//        $posts = array_merge($user_posts,$friends_posts,$groups_posts,$pages_posts,$user_sponsored_posts);
//
//        foreach ($posts as $post){
//            $post->publisher = User::find($post->publisherId);
//            $post->media = DB::table('media')->where('model_id',$post->id)->where('model_type','post')->get();
//            $comments = DB::table('comments')->where('model_id',$post->id)->where('model_type','post')->get();
//            $likes = DB::table('likes')->where('model_id',$post->id)->where('model_type','post')->get();
//            $shares = DB::table('posts')->where('post_id',$post->id)->get()->toArray();
//
//            $post->comments = $comments;
//            $post->likes = $likes;
//
//            $post->comments->count = count($comments);
//            $post->likes->count = count($likes);
//            $post->shares = count($shares);
//
//            $post->liked = DB::table('likes')->where('model_id',$post->id)->where('model_type','post')->where('senderId',$user->id)->exists();
//
//            $post->saved = DB::table('saved_posts')->where('post_id',$post->id)->where('user_id',$user->id)->exists();
//
//            if($post->comments->count > 0) {
//                foreach ($post->comments as $comment) {
//                    $comment->publisher = User::find($comment->user_id);
//                    $comment->media = DB::table('media')->where('model_id',$comment->id)->where('model_type','comment')->get();
//                }
//            }
//        }
//
//        $stories = array_merge($user_stories,$friends_stories);
//
//
//        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
//                        where user_categories.categoryId = categories.id
//                        AND user_categories.userId ='.$user->id .' and categories.type = "post"'));
//
//        $user_interests_array = [];
//
//        foreach ($user_interests as $interest){
//            array_push($user_interests_array,$interest->id);
//        }
//
////public posts having same interest of user
//        $expected_posts = DB::table('posts')->whereIn('categoryId',$user_interests_array)->where('publisherId','!=',$user->id)->where('privacyId',1)->where('postTypeId',2)->whereNull('post_id')->limit(3)->get();
//        foreach ($expected_posts as $post){
//            $post->publisher = User::find($post->publisherId);
//            $post->media = DB::table('media')->where('model_id',$post->id)->where('model_type','post')->get();
//        }
//
//        $expected_groups = Group::whereIn('category_id',$user_interests_array)->whereNotIn('id',$user_groups_ids)->limit(3)->get();
//
//        $expected_pages = Page::whereIn('category_id',$user_interests_array)->whereNotIn('id',$user_pages_ids)->limit(3)->get();
//
//        foreach ($expected_groups as $group){
//            $group_members_count = DB::select(DB::raw('select count(group_members.id) as count from group_members
//                        where group_members.group_id ='.$group->id .' and group_members.stateId = 2'
//            ))[0]->count;
//
//            $group->members = $group_members_count;
//        }
//
//        foreach ($expected_pages as $page){
//            $page_likes_count = DB::select(DB::raw('select count(user_pages.id) as count from user_pages
//                        where user_pages.page_id ='.$page->id
//            ))[0]->count;
//
//            $page->members = $page_likes_count;
//        }
//
//        foreach ($stories as $story){
//            $story->publisher = User::find($story->publisherId);
//            $story->media = DB::table('media')->where('model_id',$story->id)->where('model_type','story')->first();
//        }
//
//        return view('home', compact('posts', 'stories', 'expected_users', 'expected_groups', 'expected_pages', 'expected_posts'));
//    }
}
