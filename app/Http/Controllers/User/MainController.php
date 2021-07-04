<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Group;
use App\Models\Page;
use App\Models\Post;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $friends_posts = [];
        $groups_posts = [];
        $pages_posts = [];
        $friends_stories = [];
        $expected_friends = [];
        $user_sponsored_posts = [];
        $user_groups_ids = [];
        $user_pages_ids = [];
        // friends posts he follows and are public and in groups you are in and in pages you liked
        $friends = DB::table('friendships')->where(function ($q) use ($user){
            $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
        })->where('stateId',2)->get();

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
                array_push($user_sponsored_posts,$post);
            }
        }


        $user_groups = DB::select(DB::raw('select groups.* from groups,group_members
                        where group_members.group_id = groups.id
                        AND group_members.user_id = 1 and group_members.stateId = 1'));

        $user_pages = DB::select(DB::raw('select pages.* from pages,user_pages
                        where user_pages.page_id = pages.id
                        AND user_pages.user_id = 1'));

        //his posts, shares , friends posts who are followed
        $user_stories = DB::table('stories')->where('publisherId',$user->id)->get()->toArray();
        $user_posts = DB::table('posts')->where('publisherId',$user->id)->where('postTypeId',2)->where('stateId',2)->get()->toArray();
        foreach ($friends as $friend){
            $friend_id = $friend->receiverId == 1 ? $friend->senderId : $friend->receiverId;
            $friend_posts = DB::table('posts')->where('publisherId',$friend_id)->where('postTypeId',1)->where('stateId',2)->get();
            $friend_stories = DB::table('stories')->where('publisherId',$friend_id)->get();
            $friends_of_friend = DB::table('friendships')->where(function ($q) use($friend){
                $q->where('senderId', $friend->id)->orWhere('receiverId', $friend->id);
            })->where('stateId',2)->limit(3)->get();

            foreach ($friend_posts as $post){
                $post->type = $post->post_id == null ? "post" : "share";
                array_push($friends_posts,$post);
            }

            foreach ($friend_stories as $story){
                array_push($friends_stories,$story);
            }

            foreach ($friends_of_friend as $user_friend){
//                $friends = DB::table('friendships')->select('id')->where(function ($q,$user){
//                    $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
//                })->where('stateId',2)->get();
                array_push($expected_friends,$user_friend);
            }
        }

        foreach ($user_groups as $group){
            $group_posts = DB::table('posts')->where('group_id',$group->id)->get();
            array_push($user_groups_ids,$group->id);
            foreach ($group_posts as $post){
                $post->type = $post->post_id == null ? "post" : "share";
                array_push($groups_posts,$post);
            }
        }

        foreach ($user_pages as $page){
            $page_posts = DB::table('posts')->where('page_id',$page->id)->get();
            array_push($user_pages_ids,$page->id);
            foreach ($page_posts as $post){
                $post->type = $post->post_id == null ? "post" : "share";
                array_push($pages_posts,$post);
            }
        }

        foreach ($user_posts as $post){
            $post->type = $post->post_id == null ? "post" : "share";
        }


        $posts = array_merge($user_posts,$friends_posts,$groups_posts,$pages_posts,$user_sponsored_posts);

        foreach ($posts as $post){
            $comments = DB::table('comments')->where('model_id',$post->id)->where('model_type',$post->type)->get()->toArray();
            $likes = DB::table('likes')->where('model_id',$post->id)->where('model_type',$post->type)->get()->toArray();
            $shares = DB::table('posts')->where('post_id',$post->id)->get()->toArray();

            $post->comments = $comments;
            $post->likes = $likes;

            $post->comments['count'] = count($comments);
            $post->likes['count'] = count($likes);
            $post->shares = count($shares);
        }

        $stories = array_merge($user_stories,$friends_stories);


        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.categoryId = categories.id
                        AND user_categories.userId ='.$user->id .' and categories.type = "post"'));

        $user_interests_array = [];

        foreach ($user_interests as $interest){
            array_push($user_interests_array,$interest->id);
        }

//public posts having same interest of user
        $expected_posts = DB::table('posts')->whereIn('categoryId',$user_interests_array)->where('publisherId','!=',$user->id)->where('privacyId',1)->where('postTypeId',2)->limit(3)->get();
        foreach ($expected_posts as $post){
            $post->publisher = User::find($post->publisherId);
            $post->media = DB::table('media')->where('model_id',$post->id)->where('model_type','post')->get();
        }

        $expected_groups = Group::whereIn('category_id',$user_interests_array)->whereNotIn('id',$user_groups_ids)->limit(3)->get();

        $expected_pages = Page::whereIn('category_id',$user_interests_array)->whereNotIn('id',$user_pages_ids)->limit(3)->get();

        foreach ($expected_groups as $group){
            $group_members_count = DB::select(DB::raw('select count(group_members.id) as count from group_members
                        where group_members.group_id ='.$group->id .' and group_members.stateId = 2'
            ))[0]->count;

            $group->members = $group_members_count;
        }

        foreach ($expected_pages as $page){
            $page_likes_count = DB::select(DB::raw('select count(user_pages.id) as count from user_pages
                        where user_pages.page_id ='.$page->id
            ))[0]->count;

            $page->members = $page_likes_count;
        }

        return view('home', compact('posts', 'stories', 'expected_friends', 'expected_groups', 'expected_pages', 'expected_posts', 'user_sponsored_posts'));
    }
}
