<?php

namespace App\Http\Controllers\User;

use App\Country;
use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Page;
use App\Models\Post;
use App\Models\Report;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class MainController extends Controller
{

    use GeneralTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $take = null, $start = null)
    {

//        $countries_allowed = ['Egypt','Saudi Arabia','Iraq','Algeria','Syria','Tunisia','Yemen',
//            'Qatar','United Arab Emirates','Morocco','Jordan','Oman','Bahrain','Lebanon','Sudan',
//            'Libya','Palestinian Territory','Kuwait'];
//
//        $url = 'https://countriesnow.space/api/v0.1/countries';
//        $response = file_get_contents($url);
//        $newsData = json_decode($response);
//        $records = $newsData->data;
//        //0 20
//        for($i=0;$i<count($records);$i++){
//            $country = $records[$i]->country;
//            if(in_array($country, $countries_allowed)) {
//                if(DB::table('countries')->where('name',$country)->doesntExist()) {
//                    $cities = $records[$i]->cities;
//                    $oneCountry = Country::create([
//                        'name' => $country,
//                    ]);
//                    foreach ($cities as $city) {
//                        DB::table('cities')->insert([
//                            'name' => $city,
//                            'country_id' => $oneCountry->id
//                        ]);
//                    }
//                }
//            }
//        }

        $user = auth()->user();

        $friends_posts = [];
        $expected_ids = [];
        $user_groups_ids = [];
        $user_pages_ids = [];
        $friends_info = [];
        $limit = 5;
        $offset = 0;


        if ($request->ajax()) {
            $limit = $take;
            $offset = $start;
        }

        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.categoryId = categories.id
                        AND user_categories.userId =' . $user->id . ' and categories.type = "post"'));

        $user_interests_array = [];

        foreach ($user_interests as $interest) {
            array_push($user_interests_array, $interest->id);
        }


        $user_sponsored_posts = $this->getSponsoredPosts($user_interests_array,$limit,$offset);


        // friends posts he follows and are public and in groups you are in and in pages you liked
        $friends = DB::table('friendships')->where(function ($q) use ($user) {
            $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
        })->where('stateId', 2)->get();

        $friends_ids = [];

        foreach ($friends as $friend2) {
            $friend2_id = $friend2->receiverId == $user->id ? $friend2->senderId : $friend2->receiverId;
            array_push($friends_ids, $friend2_id);
        }

        array_push($friends_ids, $user->id);

        $user_groups = DB::select(DB::raw('select groups.*,group_members.state from groups,group_members
                        where group_members.group_id = groups.id
                        AND group_members.user_id = ' . $user->id));

        $user_pages = DB::select(DB::raw('select pages.* from pages,page_members
                        where page_members.page_id = pages.id
                        AND page_members.user_id = ' . $user->id));

        $user_follows = DB::select(DB::raw('select users.* from users,following
                        where following.followingId = users.id
                        AND following.followerId = ' . $user->id));

        //his posts, shares , friends posts who are followed
        $user_posts = DB::table('posts')->where('publisherId', $user->id)->where('postTypeId', 2)->where('stateId', 2)
            ->whereNull(['page_id', 'group_id'])
            ->limit($limit)
            ->offset($offset)
            ->orderBy('created_at', 'desc')->get()->toArray();

        foreach ($friends as $friend) {
            $friend_id = $friend->receiverId == $user->id ? $friend->senderId : $friend->receiverId;
            if (($key = array_search($friend_id, $friends_ids)) !== false) {
                unset($friends_ids[$key]);
            }

            $friend_followed = DB::table('following')->where('followerId', $user->id)->where('followingId', $friend_id)->exists();

            if ($friend_followed) {
                $friend_posts = DB::table('posts')->where('publisherId', $friend_id)->where('postTypeId', 2)
                    ->where('stateId', 2)->where('privacyId', 1)
                    ->limit($limit)
                    ->offset($offset)
                    ->orderBy('created_at', 'desc')->get();
                foreach ($friend_posts as $post) {
                    $post->sponsored = false;
                    if ($post->page_id == null and $post->group_id == null) {
                        $post->reported = DB::table('reports')->where('user_id', $user->id)
                            ->where('model_id', $post->id)->where('model_type', 'post')->exists();

                        $isblocked = DB::table('blocks')->where('senderId',auth()->user()->id)->where('receiverId',$post->publisherId)->exists();

                        if ($post->reported == false && $isblocked == false) {
                            foreach ($user_sponsored_posts as $sponsored) {
                                if ($sponsored->id == $post->id) {
                                    $post->sponsored = true;
                                }
                            }
                            if ($post->sponsored == false) {
                                array_push($friends_posts, $post);
                            }
                        }
                    }
                }
            }

            $friends_of_friend = DB::table('friendships')->where(function ($q) use ($friend_id) {
                $q->where('senderId', $friend_id)->orWhere('receiverId', $friend_id);
            })->where('stateId', 2)->whereNotIn('receiverId', $friends_ids)->whereNotIn('senderId', $friends_ids)->get();


            array_push($friends_ids, $friend_id);

            foreach ($friends_of_friend as $user_friend) {
                $friend_of_friend_id = $user_friend->receiverId == $friend_id ? $user_friend->senderId : $user_friend->receiverId;
                $friend_request = DB::table('friendships')->where(function ($q) use ($user) {
                    $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
                })->where(function ($q) use ($friend_of_friend_id) {
                    $q->where('senderId', $friend_of_friend_id)->orWhere('receiverId', $friend_of_friend_id);
                })->whereIn('stateId', [2,3])->exists();

                $isblocked = DB::table('blocks')->where('senderId',auth()->user()->id)->where('receiverId',$friend_of_friend_id)->exists();

                if ($friend_request == false && $isblocked == false) {
                    array_push($expected_ids, $friend_of_friend_id);
                }
            }

            $friend_info = DB::table('users')->select('id', 'name', 'cover_image', 'personal_image')->where('id', $friend_id)->first();

            array_push($friends_info, $friend_info);

        }


        foreach ($user_groups as $group) {
            array_push($user_groups_ids, $group->id);
        }

        foreach ($user_pages as $page) {
            array_push($user_pages_ids, $page->id);
        }

        $expected_users = $this->getExpectedFriends($user,$expected_ids);

        $posts = $this->getPosts($limit, $offset, $user, $user_follows, $friends_posts, $user_pages, $user_groups, $user_posts, $user_sponsored_posts);

        $stories = $this->getStories($user,5,0);

        $expected_posts = $this->getExpectedPosts($user, $user_interests_array);

        $expected_groups = $this->getExpectedGroups($user_interests_array, $user_groups_ids);

        $expected_pages = $this->getExpectedPages($user_interests_array, $user_pages_ids);

        $privacy = DB::table('privacy_type')->get();

        $categories = DB::table('categories')->where('type', 'post')->get();

        $times = DB::table('sponsored_time')->get();

        $reaches = DB::table('sponsored_reach')->get();

        $ages = DB::table('sponsored_ages')->get();

        $reacts = DB::table('reacts')->get();

        $cities = DB::table('cities')->get();

        $countries = DB::table('countries')->get();

        $another_comments = 'exist';


        if ($request->ajax()) {

            if (count($posts) > 0) {

                $view = view('includes.partialpost', compact('posts', 'privacy', 'categories', 'times', 'ages', 'reaches', 'reacts', 'friends_info', 'cities', 'countries', 'another_comments'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['post'];
            } else {
                return response()->json([
                    'msg' => 'end'
                ]);
            }
        }


        return view('home', compact('posts', 'stories', 'expected_users', 'expected_groups', 'expected_pages', 'expected_posts', 'privacy', 'categories', 'times', 'ages', 'reaches', 'reacts', 'friends_info', 'cities', 'countries', 'another_comments'));
    }

    private function getPosts($limit, $offset, $user, $user_follows, $friends_posts, $user_pages, $user_groups, $user_posts, $user_sponsored_posts)
    {

        $pages_posts = [];

        $user_pages_ids = [];

        $follows_posts = [];

        $user_groups_ids = [];

        $groups_posts = [];

        $auth_user_posts = [];

        foreach ($user_groups as $group) {

            if ($group->state == 1) {
                $group_posts = DB::table('posts')->where('group_id', $group->id)
                    ->limit($limit)
                    ->offset($offset)
                    ->orderBy('created_at', 'desc')->get();
                foreach ($group_posts as $post) {
                    $post->reported = DB::table('reports')->where('user_id', $user->id)
                        ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                    if ($post->reported == false) {
                        $post->sponsored = false;
                        foreach ($user_sponsored_posts as $sponsored) {
                            if ($sponsored->id == $post->id) {
                                $post->sponsored = true;
                            }
                        }
                        if ($post->sponsored == false) {
                            array_push($groups_posts, $post);
                        }
                    }
                }
            }

            array_push($user_groups_ids, $group->id);
        }


        foreach ($user_pages as $page) {
            $page_posts = DB::table('posts')->where('page_id', $page->id)
                ->limit($limit)
                ->offset($offset)
                ->orderBy('created_at', 'desc')->get();
            foreach ($page_posts as $post) {
                $post->reported = DB::table('reports')->where('user_id', $user->id)
                    ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                if ($post->reported == false) {
                    $post->sponsored = false;
                    foreach ($user_sponsored_posts as $sponsored) {
                        if ($sponsored->id == $post->id) {
                            $post->sponsored = true;
                        }
                    }
                    if ($post->sponsored == false) {
                        array_push($pages_posts, $post);
                    }
                }
            }
            array_push($user_pages_ids, $page->id);
        }

        foreach ($user_follows as $followed_user) {
            $is_friend = DB::table('friendships')->where(function ($q) use ($user) {
                $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
            })->where(function ($q) use ($followed_user) {
                $q->where('senderId', $followed_user->id)->orWhere('receiverId', $followed_user->id);
            })->where('stateId', 2)->exists();

            if ($is_friend == false) {
                $followed_user_posts = DB::table('posts')->where('publisherId', $followed_user->id)->where('postTypeId', 2)
                    ->where('stateId', 2)->where('privacyId', 1)
                    ->limit($limit)
                    ->offset($offset)
                    ->orderBy('created_at', 'desc')->get();
                foreach ($followed_user_posts as $post) {
                    $post->sponsored = false;
                    if ($post->page_id == null and $post->group_id == null) {
                        $post->reported = DB::table('reports')->where('user_id', $user->id)
                            ->where('model_id', $post->id)->where('model_type', 'post')->exists();

                        $isblocked = DB::table('blocks')->where('senderId',auth()->user()->id)->where('receiverId',$post->publisherId)->exists();

                        if ($post->reported == false && $isblocked == false) {
                            foreach ($user_sponsored_posts as $sponsored) {
                                if ($sponsored->id == $post->id) {
                                    $post->sponsored = true;
                                }
                            }
                            if ($post->sponsored == false) {
                                array_push($follows_posts, $post);
                            }
                        }
                    }
                }
            }
        }

        foreach ($user_posts as $post) {
            $post->sponsored = false;
            foreach ($user_sponsored_posts as $sponsored) {
                if ($sponsored->id == $post->id) {
                    $post->sponsored = true;
                }
            }
            if ($post->sponsored == false) {
                array_push($auth_user_posts, $post);
            }
        }


        $posts = array_merge($follows_posts, $auth_user_posts, $friends_posts, $groups_posts, $pages_posts, $user_sponsored_posts);

        shuffle($posts);

        foreach ($posts as $post) {
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
            $post->comments_count = count($comments);

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
        }

        return $posts;
    }

    private function getStories($user,$limit,$offset,$ajax_request = false)
    {

        $friends_stories = [];

        $friends = DB::table('friendships')->where(function ($q) use ($user) {
            $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
        })->where('stateId', 2)
            ->limit($limit)
            ->offset($offset)
            ->get();

        foreach ($friends as $friend) {
            $friend_id = $friend->receiverId == $user->id ? $friend->senderId : $friend->receiverId;

            $friend_stories = DB::table('stories')->where('publisherId', $friend_id)->where('privacyId', 1)->orderBy('created_at','desc')->get();

            $isblocked = DB::table('blocks')->where('senderId',auth()->user()->id)->where('receiverId',$friend_id)->exists();

            if (count($friend_stories) > 0) {
                if($isblocked == false){
                    $friend_stories->publisher = User::find($friend_id);
                    array_push($friends_stories, $friend_stories);
                }
            }
        }

        if($ajax_request == false){
            $user_stories = DB::table('stories')->where('publisherId', $user->id)->orderBy('created_at','desc')->get();

            $auth_user_stories = [];

            if (count($user_stories) > 0) {
                $user_stories->publisher = User::find($user->id);
                array_push($auth_user_stories, $user_stories);
            }

            $stories = array_merge($auth_user_stories, $friends_stories);
        }
        else{
            $stories = $friends_stories;
        }


        if (count($stories)){
            foreach ($stories as $story) {
                foreach ($story as $inner_story) {
                    $inner_story->viewers = DB::select(DB::raw('select users.* from users,stories_views
                            where stories_views.story_id =' . $inner_story->id .
                        ' AND stories_views.user_id = users.id'));
                    $inner_story->media = DB::table('media')->where('model_id', $inner_story->id)->where('model_type', 'story')->first();
                }
            }
        }

        return $stories;
    }

    public function getExpectedFriends($user, $expected_ids)
    {

        $expected_friends = [];

        $expected_people = array_unique($expected_ids);

        if (count($expected_people) == 0) {
            $expected_people = DB::table('users')->where('country_id', auth()->user()->country)->where('id', '!=', auth()->user()->id)->get();
            foreach ($expected_people as $expected_user) {
                $expected_user_id = $expected_user->id;
                $friendship = DB::table('friendships')->where(function ($q) use ($expected_user_id) {
                    $q->where('senderId', $expected_user_id)->orWhere('receiverId', $expected_user_id);
                })->where(function ($q) {
                    $q->where('senderId', auth()->user()->id)->orWhere('receiverId', auth()->user()->id);
                })->whereIn('stateId', [2,3])->first();

                $isblocked = DB::table('blocks')->where('senderId',auth()->user()->id)->where('receiverId',$expected_user_id)->exists();
                if ($friendship == null && $isblocked == false) {
                    $expected_user->followers = DB::table('following')->where('followingId', $expected_user_id)->count();
                    array_push($expected_friends, $expected_user);
                }
            }
        }
        else {

            foreach ($expected_people as $expected_user) {
                $expected_user_id = $expected_user;
                $user_id = $user->id;
                //            $friendship = DB::table('friendships')->where(function ($q) use ($id) {
                //                $q->where('senderId', $id)->orWhere('receiverId', $id);
                //            })->where(function ($q) use ($user_id) {
                //                $q->where('senderId', $user_id)->orWhere('receiverId', $user_id);
                //            })->whereIn('stateId', [2,3])->first();
                //            if ($friendship == null) {
                $isblocked = DB::table('blocks')->where('senderId', $user_id)->where('receiverId', $expected_user_id)->exists();

                if ($isblocked == false) {
                    $friends_of_friend_info = DB::table('users')->where('id', $expected_user_id)->first();
                    $friends_of_friend_info->followers = DB::table('following')->where('followingId', $expected_user_id)->count();
                    array_push($expected_friends, $friends_of_friend_info);
                }
            }
        }

        shuffle($expected_friends);

        $expected_users = array_slice($expected_friends, 0, 3);

        return $expected_users;
    }

    public function getSponsoredPosts($user_interests,$limit = null ,$offset = null)
    {
        $user_sponsored_posts = [];

        if($limit == null){
            $all_sponsored_posts = DB::select(DB::raw('select categories.id as sponsor_category,sponsored.gender,sponsored.created_at as sponsored_at,sponsored_time.duration,posts.*,sponsored_reach.reach,countries.id as country_id,cities.id as city_id,sponsored_ages.from,sponsored_ages.to from
                                        posts,sponsored,sponsored_reach,sponsored_ages,countries,cities,sponsored_time,categories
                                        where sponsored.postId = posts.id and sponsored.reachId = sponsored_reach.id
                                        and sponsored.age_id = sponsored_ages.id and sponsored.country_id = countries.id
                                        and sponsored.city_id = cities.id and sponsored.timeId = sponsored_time.id and sponsored.category_id = categories.id ORDER BY posts.created_at DESC'));
        }
        else{
            $all_sponsored_posts = DB::select(DB::raw('select categories.id as sponsor_category,sponsored.gender,sponsored.created_at as sponsored_at,sponsored_time.duration,posts.*,sponsored_reach.reach,countries.id as country_id,cities.id as city_id,sponsored_ages.from,sponsored_ages.to from
                                        posts,sponsored,sponsored_reach,sponsored_ages,countries,cities,sponsored_time,categories
                                        where sponsored.postId = posts.id and sponsored.reachId = sponsored_reach.id
                                        and sponsored.age_id = sponsored_ages.id and sponsored.country_id = countries.id
                                        and sponsored.city_id = cities.id and sponsored.timeId = sponsored_time.id and sponsored.category_id = categories.id ORDER BY posts.created_at DESC limit '.$limit.' offset '.$offset));
        }

        //if there is city and country

        foreach ($all_sponsored_posts as $post) {
            $post_users = DB::table('users')->whereBetween('age', [$post->from, $post->to])
                ->where('country_id', $post->country_id)
                ->where('city_id', $post->city_id)
                ->where('gender', $post->gender)
                ->limit($post->reach)->pluck('id')->toArray();

            //if there is categories

            if (in_array(auth()->user()->id, $post_users) != false && Carbon::parse($post->sponsored_at)->addDays($post->duration) >= Carbon::today() && in_array($post->sponsor_category,$user_interests)) {
                $post->reported = DB::table('reports')->where('user_id', auth()->user()->id)
                    ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                if ($post->reported == false) {
                    $post->sponsored = true;
                    array_push($user_sponsored_posts, $post);
                }
            }
            //else
        }

        return $user_sponsored_posts;
    }

    public function getExpectedPosts($user, $user_interests_array)
    {

//public posts having same interest of user
        $expected_posts = DB::table('posts')->whereIn('categoryId', $user_interests_array)->where('publisherId', '!=', $user->id)->where('privacyId', 1)->where('postTypeId', 2)->whereNull(['post_id', 'page_id', 'group_id'])->limit(3)->get();
        $allowed_expected_posts = [];
        foreach ($expected_posts as $post) {
            $post->reported = DB::table('reports')->where('user_id', auth()->user()->id)
                ->where('model_id', $post->id)->where('model_type', 'post')->exists();

            $isblocked = DB::table('blocks')->where('senderId',auth()->user()->id)->where('receiverId',$post->publisherId)->exists();

            if ($post->reported == false && $isblocked == false) {
                if ($post->mentions != null) {
                    $post->edit = $post->body;
                    $mentions = explode(',', $post->mentions);
                    foreach ($mentions as $mention) {
                        $mention_id = DB::table('users')->select('id')->where('user_name',$mention)->first();
                        $post->body = str_replace('@' . $mention,
                            '<a href="profile/'.$mention_id->id.'" style="color: #ffc107">' . $mention . '</a>',
                            $post->body);
                    }
                }
                $post->publisher = User::find($post->publisherId);
                array_push($allowed_expected_posts, $post);
            }
        }

        return $expected_posts;
    }

    public function getExpectedGroups($user_interests_array, $user_groups_ids)
    {
        $expected_groups = Group::whereIn('category_id', $user_interests_array)->whereNotIn('id', $user_groups_ids)->limit(3)->get();

        foreach ($expected_groups as $group) {
            $group_members_count = DB::select(DB::raw('select count(group_members.id) as count from group_members
                        where group_members.group_id =' . $group->id . ' and group_members.state = 1'
            ))[0]->count;

            $group->members = $group_members_count;
        }

        if(count($expected_groups) == 0){
            $expected_groups = Group::whereNotIn('id', $user_groups_ids)->limit(3)->get();
        }

        return $expected_groups;
    }

    public function getExpectedPages($user_interests_array, $user_pages_ids)
    {

        $expected_pages = Page::whereIn('category_id', $user_interests_array)->whereNotIn('id', $user_pages_ids)->limit(3)->get();

        foreach ($expected_pages as $page) {
            $page_likes_count = DB::select(DB::raw('select count(page_members.id) as count from page_members
                        where page_members.page_id =' . $page->id
            ))[0]->count;

            $page->members = $page_likes_count;
        }

        if(count($expected_pages) == 0){
            $expected_pages = Page::whereNotIn('id', $user_pages_ids)->limit(3)->get();
        }

        return $expected_pages;
    }

    public function report(Request $request)
    {

        $user = auth()->user();

        $rules = [
            'body' => ['nullable'],
        ];

//        $messages = [
//            'body.required' => trans('error.body_required',''),
//        ];

        $validator = Validator::make($rules, $rules);

        if ($validator->fails()) {
            return $this->returnValidationError(402, $validator);
        }

        $report = Report::create([
            'body' => $request->body,
            'user_id' => $user->id,
            'state' => "pending",
            'model_id' => $request->model_id,
            'model_type' => $request->model_type,
        ]);

        if ($report) {
            if ($request->model_type == 'comment') {
                $comment = Comment::find($request->model_id);
                $post = Post::find($comment->model_id);
                $post->comments = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->get();
                if ($comment->comment_id != null) {
                    $type = 'reply';
                } else {
                    $type = 'comment';
                }

                return response()->json([
                    'type' => $type,
                    'msg' => trans('home.report_sent'),
                    'count' => count($post->comments)
                ]);
            } else {
                return response()->json([
                    'msg' => trans('home.report_sent'),
                ]);
            }
        } else {
            return $this->returnError('something wrong happened', 402);
        }
    }

    public function loadComments(Request $request, $post_id, $limit, $offset)
    {

        $user = auth()->user();

        $post = Post::find($post_id);

        $comments = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->whereNull('comment_id')
            ->limit($limit)
            ->offset($offset)
            ->orderBy('created_at', 'desc')
            ->get();

        if (count($comments) > 0) {

            foreach ($comments as $comment) {

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
                                '<a href="profile/'.$mention_id->id.'" style="color: #ffc107">' . $mention . '</a>',
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
                                            '<a href="profile/'.$mention_id->id.'" style="color: #ffc107">' . $mention . '</a>',
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

            $reacts = DB::table('reacts')->get();


            $another_comments = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->whereNull('comment_id')
                ->limit($limit)
                ->offset($offset + 5)
                ->get();


            if (count($another_comments) > 0) {

                $view = view('includes.partialcomment', compact('post', 'comments', 'reacts', 'another_comments'));
            } else {
                $view = view('includes.partialcomment', compact('post', 'comments', 'reacts'));
            }

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['comment'];
        } else {
            return response()->json([
                'msg' => "end"
            ]);
        }
    }

    public function loadStories(Request $request,$limit,$offset)
    {

        $user = auth()->user();

        $stories = $this->getStories($user,$limit,$offset,true);

        $another_stories = $this->getStories($user,$limit,$offset+5);


        if (count($another_stories) > 0) {

            $view = view('includes.partialstories', compact('stories'));

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['home_stories'];
        }
        else{
            return response()->json([
                'msg' => 'end'
            ]);
        }

    }

    public function search(Request $request,$type,$search_param)
    {
        $auth_user = auth()->user();
        if($search_param != "null") {

            if($type == 'groups'){

                $groups = DB::select(DB::raw('
                                select * from groups where groups.name LIKE "%' . $search_param . '%"'));

                foreach ($groups as $group) {
                    $group_members_count = DB::select(DB::raw('select count(group_members.id) as count from group_members
                        where group_members.group_id =' . $group->id . ' and group_members.state = 1'
                    ))[0]->count;

//                    $joined_group = DB::select(DB::raw('select stateId from group_members
//                        where group_members.group_id =' . $group->id . ' and group_members.user_id = ' . $user->id))[0]->stateId;

                    $joined_group = DB::table('group_members')->select('state')->where('user_id',$auth_user->id)
                        ->where('group_id',$group->id)->first();

                    if($joined_group){
//                        if ($joined_group->state == 0){
//                            $group->joined = 'request rejected';
//                            $group->flag = 0;
//                        }
                        if ($joined_group->state == 1){
                            if($joined_group->isAdmin == 1){
                                $group->joined = trans('groups.delet_group');
                                $group->state = 'delete group';
                            }
                            else{
                                $group->joined = trans('groups.leave_group');
                                $group->state = 'leave group';
                                $group->flag = 1;
                            }
                        }
                        elseif($joined_group->state == 2){
                            $group->joined = trans('groups.left_request');
                            $group->state = 'cancel request';
                            $group->flag = 1;
                        }
                        else{
                            $group->joined = trans('groups.confirm_invite');
                            $group->state = 'accept invitation';
                            $group->flag = 0;
                        }
                    }
                    else{
                        $group->joined = trans('groups.join');
                        $group->state = 'join';
                        $group->flag = 0;
                    }

                    $group->members = $group_members_count;
                }

                $view = view('includes.partialgroups', compact('groups'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['groups'];
            }
            elseif ($type == 'pages'){
                $pages = DB::select(DB::raw('
                                select * from pages where pages.name LIKE "%' . $search_param . '%"'));

                foreach ($pages as $page) {
                    $page_likes_count = DB::select(DB::raw('select count(page_members.id) as count from page_members
                        where page_members.page_id =' . $page->id
                    ))[0]->count;


                    $liked_page = DB::table('page_members')->where('user_id',$auth_user->id)
                        ->where('page_id',$page->id)->first();

                        if ($liked_page != null){
                            if($liked_page->isAdmin == 1){
                                $page->liked = trans('pages.delet_page');
                                $page->state = 'delete page';
                            }
                            else{
                                $page->liked = trans('pages.dislike');
                                $page->state = 'unlike';
                                $page->flag = 1;
                            }
                        }
                        else{
                            $page->liked = trans('pages.like');
                            $page->state = 'like';
                            $page->flag = 0;
                        }

                    $page->members = $page_likes_count;
                }

                $view = view('includes.partialpages', compact('pages'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['pages'];
            }
            else{
                $users = DB::select(DB::raw('
                                select * from users where users.name LIKE "%' . $search_param . '%" AND users.id != '.auth()->user()->id));

                foreach ($users as $user){
                    $isfriend = DB::table('friendships')->where(function ($q) use ($user) {
                        $q->where('senderId', auth()->user()->id)->Where('receiverId', $user->id);
                    })->orwhere(function ($q) use ($user) {
                        $q->where('senderId', $user->id)->Where('receiverId', auth()->user()->id);
                    })->first();

                    $isblocked = DB::table('blocks')->where('senderId',auth()->user()->id)->where('receiverId',$user->id)->exists();

                    if ($isfriend) {
                        $auth_user_status = $isfriend->receiverId == auth()->user()->id ? 'receiver' : 'sender';
                        if ($auth_user_status == 'sender') {
                            if ($isfriend->stateId == 2) {
                                $user->friendship = trans('pages.un_friend');
                                $user->state = 'unfriend';
                                $user->request_type = 'removeFriendRequest';
                                $user->sender = auth()->user()->id;
                                $user->receiver = $user->id;
                            } else {
                                $user->friendship = trans('pages.un_friend_request');
                                $user->state = 'remove friend request';
                                $user->request_type = 'removeFriendRequest';
                                $user->sender = auth()->user()->id;
                                $user->receiver = $user->id;
                            }
                        } else {
                            if ($isfriend->stateId == 2) {
                                $user->friendship = trans('pages.un_friend');
                                $user->state = 'unfriend';
                                $user->request_type = 'removeFriendRequest';
                                $user->sender = $user->id;
                                $user->receiver = auth()->user()->id;
                            } else {
                                $user->state = 'receive friend request';
                                $user->sender = $user->id;
                                $user->receiver = auth()->user()->id;
                            }
                        }
                    } else {
                        $user->friendship = trans('pages.add_friend');
                        $user->state = 'add friend';
                        $user->request_type = 'addFriendRequest';
                        $user->sender = auth()->user()->id;
                        $user->receiver = $user->id;
                    }

                    if($isblocked == false) {
                        $user->block = trans('home.block');
                        $user->block_type = 'addBlockRequest';
                    }
                    else{
                        $user->block = trans('home.remove_block');
                        $user->block_type = 'removeBlockRequest';
                    }
                }

                $view = view('includes.partialusers', compact('users'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['users'];
            }
        }
        else {
            $home = $this->index($request,5,0);
            return $home;
        }
    }

}
