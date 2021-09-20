<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Category;
use App\Models\Following;
use App\Models\Friendship;
use App\Models\Group;
use App\Models\Media;
use App\Models\Page;
use App\models\PageMember;
use App\Models\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PagesController extends Controller
{

    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($flag)
    {

        $user = auth()->user();

        if($flag == 0) {
            $pages = Page::all();

            foreach ($pages as $page) {
                $user_page = DB::table('page_members')
                    ->where([['page_id',$page->id],['user_id',$user->id]])
                    ->first();

                $page_users = DB::table('page_members')
                    ->where('group_id',$page->id)
                    ->count();

                $page->users = $page_users;

                if ($user_page) {
                    $page['liked'] = 1;
                }
                else{
                    $page['liked'] = 0;
                }
            }
        }
        else{
            $pages = DB::select(DB::raw('select pages.* from pages,page_members
                        where page_members.page_id = pages.id
                        AND page_members.user_id =
                        '.$user->id));
        }

        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.category_id = categories.id
                        AND user_categories.user_id ='.$user->id.'and categories.type = 0'));

        $user_interests_array = [];

        foreach ($user_interests as $interest){
            array_push($user_interests_array,$interest->id);
        }

        $expected_pages = Page::whereIn('category_id',$user_interests)->limit(3);

        foreach ($expected_pages as $page) {
            $page_users = DB::table('group_members')
                ->where('page_id',$page->id)->where('state',1)
                ->count();

            $page->users = $page_users;
        }

        return view('User.pages.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categroys = Category::get();

        $user_pages_ids = [];
        $user = auth()->user();
        $related_pages = Page::limit(3)->get();
        $all_pages = Page::paginate(30);

        $user_pages = DB::select(DB::raw('select pages.* from pages,page_members
                        where page_members.page_id = pages.id
                        AND page_members.user_id = ' . $user->id));

        foreach ($user_pages as $page) {
            array_push($user_pages_ids, $page->id);
        }


        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.categoryId = categories.id
                        AND user_categories.userId =' . $user->id . ' and categories.type = "post"'));

        $user_interests_array = [];

        foreach ($user_interests as $interest) {
            array_push($user_interests_array, $interest->id);
        }

        $expected_pages = $this->getExpectedPages($user_interests_array, $user_pages_ids);

        // return $expected_pages;
        return view('User.pages.create', compact('categroys','expected_pages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $rules = [
            'name' => 'required',
        ];

        $this->validate($request,$rules);
        $profile_image = '.0' . time() . $request->profile_image->extension();
        $request->profile_image->move(public_path('media'), $profile_image);

        $cover_image = '.1' . time() . $request->cover_image->extension();
        $request->cover_image->move(public_path('media'), $cover_image);

        $page = Page::insertGetId([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'publisher_id' => Auth::guard('web')->user()->id,
            'profile_image' => $profile_image,
            'cover_image' => $cover_image,
            'rules' =>  $request->rules,
            'privacy' => 1
        ]);
        $page_admin = DB::table('page_members')->insert([
            'page_id' => $page,
            'user_id' => Auth::guard('web')->user()->id,
            'state' => 1,
            'isAdmin'=>1
        ]);

        if($page){
            return redirect('main-page/'.$page);
        }
        else
        {
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        if(Auth::guard('web')->user())
        {
            $categroys = Category::get();
            $page = Page::find($id);
            $isAdmin = $this->isAdmin($id);
            if($isAdmin == 1)
            {
                $user_pages_ids = [];
                $user = auth()->user();
                $related_pages = Page::limit(3)->get();
                $all_pages = Page::paginate(30);

                $user_pages = DB::select(DB::raw('select pages.* from pages,page_members
                                where page_members.page_id = pages.id
                                AND page_members.user_id = ' . $user->id));

                foreach ($user_pages as $pages) {
                    array_push($user_pages_ids, $pages->id);
                }


                $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                                where user_categories.categoryId = categories.id
                                AND user_categories.userId =' . $user->id . ' and categories.type = "post"'));

                $user_interests_array = [];

                foreach ($user_interests as $interest) {
                    array_push($user_interests_array, $interest->id);
                }

                $expected_pages = $this->getExpectedPages($user_interests_array, $user_pages_ids);

                return view('User.pages.edit', compact('categroys','page','expected_pages'));
            }
            else
            {
                return redirect()->back();
            }
            // return $isAdmin;
        }
        else
        {
            return redirect('login');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $page = Page::find($id);
        if (isset($request->profile_image)) {
            $profile_image = '.0' . time() . $request->profile_image->extension();
            $request->profile_image->move(public_path('media'), $profile_image);
        } else {
            $profile_image = $page->profile_image;
        }
        if (isset($request->cover_image)) {
            $cover_image = '.1' . time() . $request->cover_image->extension();
            $request->cover_image->move(public_path('media'), $cover_image);
        } else {
            $cover_image  = $page->cover_image;
        }

        $page->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' =>$request->category_id,
            'publisher_id' => $page->publisher_id,
            'profile_image' => $profile_image,
            'cover_image' => $cover_image,
            'rules' => $request->rules,
        ]);

        return redirect()->route('main-page',['id'=>$page->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $page_id = $id;
        //Posts
        //Media
        $page = Page::find($page_id);
        $posts  = Post::where('page_id',$page_id)->get();
        if(count($posts) > 0) {
            foreach ($posts as $post) {
                $post_media = Media::where('model_type','post')->where('model_id',$post->id)->get();
                foreach($post_media as $media) {
                    $file_pointer = $media->filename;
                    /*if (!unlink($file_pointer)) {
                        echo("$file_pointer cannot be deleted due to an error");
                    } else {
                        echo 'sad';
                    }*/
                    $media->delete();

                }
                $post->delete();
            }
        }
        $page->delete();
        return redirect('home');
    }

    public function likePage(Request $request) {

        $page_id = $request->page_id;
        $user = auth()->user();
        $flag = $request->flag;

        if ($flag == 0) {
            DB::table('page_members')->insert([
                'page_id' => $page_id,
                'user_id' => $user->id,
                'isAdmin' => 0
            ]);

            return response()->json([
                'msg' => trans('pages.leave_page'),
                'state' => 'unlike',
            ]);
        } else {
            $user_page = DB::table('page_members')->where('page_id', $page_id)->where('user_id', $user->id)->get();
            foreach ($user_page as $upage) {
                DB::table('page_members')->delete($upage->id);
            }

            return response()->json([
                'msg' => trans('pages.like'),
                'state' => 'like',
            ]);
        }
    }

    public function relatedPages($category){
        // $related_pages = Page::where('category_id',$category)->inRandomOrder()->limit(3)->get();
        $user_pages_ids = [];
        $user = auth()->user();

        $user_pages = DB::select(DB::raw('select pages.* from pages,page_members
                        where page_members.page_id = pages.id
                        AND page_members.user_id = ' . $user->id));

        foreach ($user_pages as $page) {
            array_push($user_pages_ids, $page->id);
        }


        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.categoryId = categories.id
                        AND user_categories.userId =' . $user->id . ' and categories.type = "post"'));

        $user_interests_array = [];

        foreach ($user_interests as $interest) {
            array_push($user_interests_array, $interest->id);
        }

        $related_pages = $this->getExpectedPages($user_interests_array, $user_pages_ids);

        return $related_pages;
    }

    public function memberState($id)
    {
        $myState=0;
        if(Auth::guard('web')->user())
        {
            $state = PageMember::where('page_id',$id)->where('user_id',Auth::guard('web')->user()->id)->get();
            if(count($state)>0)
            {
                $myState = $state[0]['state'];
            }
        }
        return $myState;
    }

    public function isAdmin($id)
    {
        $isAdmin=0;
        if(Auth::guard('web')->user())
        {
            $state = PageMember::where('page_id',$id)->where('user_id',Auth::guard('web')->user()->id)->where('isAdmin',1)->get();
            // $publisher = Page::where('id', $id)->where('publisher_id', Auth::guard('web')->user()->id)->get();
            if(count($state)>0)
            {
                $isAdmin = 1;
            }

        }
        return $isAdmin;
    }

    public function pagePosts($page_id)
    {
        $user = auth()->user();
        $page = Page::find($page_id);
        $related_pages = $this->relatedPages($page->category_id);
        $myState = $this->memberState($page_id);
        $isAdmin = $this->isAdmin($page_id);
        $page_liked = DB::table('page_members')->where('page_id', $page_id)->where('user_id', $user->id)->exists();
        $page_members = PageMember::where('page_id',$page_id)->where('state',1)->get();
        $page_posts = DB::table('posts')->where('page_id', $page_id)
            ->orderBy('created_at', 'desc')->get();
        foreach ($page_posts as $post){
            $post = $this->getPost($user,$post);
            $post->sponsored = false;
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


        return view('User.pages.posts',compact('page_posts','page_members','page_liked','isAdmin','myState','page','related_pages','privacy', 'categories', 'times', 'ages', 'reaches', 'reacts','cities','countries','friends_info'));
    }

    public function singlePost($page_id,$post_id)
    {
        $user = auth()->user();
        $page = Page::find($page_id);
        $related_pages = $this->relatedPages($page->category_id);
        $myState = $this->memberState($page_id);
        $isAdmin = $this->isAdmin($page_id);
        $page_liked = DB::table('page_members')->where('page_id', $page_id)->where('user_id', $user->id)->exists();
        $page_members = PageMember::where('page_id',$page_id)->where('state',1)->get();
        $page_posts = DB::table('posts')->where('id', $post_id)
            ->orderBy('created_at', 'desc')->get();
        foreach ($page_posts as $post){
            $post = $this->getPost($user,$post);
            $post->sponsored = false;
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


        return view('User.pages.posts',compact('page_posts','page_members','page_liked','isAdmin','myState','page','related_pages','privacy', 'categories', 'times', 'ages', 'reaches', 'reacts','cities','countries','friends_info'));
    }

    public function aboutPage($id){
        $page = Page::find($id);
        $related_pages = $this->relatedPages($page->category_id);
        $myState = $this->memberState($id);
        $isAdmin = $this->isAdmin($id);
        $page_members = PageMember::where('page_id',$id)->where('state',1)->where('isAdmin','!=', 1)->get();
        return view('User.pages.about',compact('page','page_members','myState', 'isAdmin', 'related_pages'));
    }

    public function pageMedia($type, $id)
    {
        //0 image
        //1 video
        $media_ids = [];
        $page_posts = Post::where('page_id', $id)->orderBy('created_at', 'ASC')->get();
        switch ($type) {
            case 'image':
                $media = Media::where('mediaType', $type)->where('model_type', 'post')->get();
                break;
            case 'video':
                $media = Media::where('mediaType', $type)->where('model_type', 'post')->get();
                break;
        }
        if(count($page_posts)>0){
            foreach ($page_posts as $gro) {
                $page_posts_ids[] = $gro->id;
            }
            foreach ($media as $med) {
                $media_post_id = $med->model_id;
                if (in_array($media_post_id, $page_posts_ids)) {
                    $media_ids[] = $med->id;
                }
            }
        }
        return $media_ids;
    }

    public function imagesPage($id){
        $page = Page::find($id);
        $related_pages = $this->relatedPages($page->category_id);
        $myState = $this->memberState($id);
        $isAdmin = $this->isAdmin($id);
        $page_members = PageMember::where('page_id',$id)->where('state',1)->where('isAdmin','!=', 1)->get();
        //0 image
        //1 video
        $images = [];
        $media = $this->pageMedia('image', $id);
        for ($i = 0; $i < count($media); $i++) {
            $images[] = Media::find($media[$i]);
        }
        return view('User.pages.images',compact('page','page_members','myState', 'isAdmin', 'related_pages', 'images'));
    }

    public function videosPage($id){
        $page = Page::find($id);
        $related_pages = $this->relatedPages($page->category_id);
        $myState = $this->memberState($id);
        $isAdmin = $this->isAdmin($id);
        $page_members = PageMember::where('page_id',$id)->where('state',1)->where('isAdmin','!=', 1)->get();
        //0 image
        //1 video
        $videos = [];
        $media = $this->pageMedia('video', $id);
        for ($i = 0; $i < count($media); $i++) {
            $videos[] = Media::find($media[$i]);
        }
        return view('User.pages.videos',compact('page','page_members','myState', 'isAdmin', 'related_pages', 'videos'));
    }

    public function joinPage(Request $request){
        //Leave & Join
        $requestType = $request->requestType;
        $page_id = intval($request->page_id);
        $user_id = intval($request->user_id);

        switch($requestType){
            case 'join':
                #region join
                //If the page is public users will join directly otherwise they will waite for the admin.
                //1 public
                //0 private
                $current_page = Page::find($page_id);
                if($current_page->privacy == 1){
                    //1 Public page
                    //State wil be 1 => accepted
                    $new_member = PageMember::create([
                        'user_id'=>  $user_id,
                        'page_id'=>$page_id,
                        'state'=>1,
                        'isAdmin' =>0
                    ]);
                    $page_members = PageMember::where('page_id',$page_id)->where('state',1)->count();
                    return 1 .'|'.$page_id.'|'. $page_members;
                }
                else{
                    //0 Private page
                    //State will be 2 pending
                    $new_member = PageMember::create([
                        'user_id'=>$user_id,
                        'page_id'=>$page_id,
                        'state'=>2,
                        'isAdmin' =>0
                    ]);
                    $page_members = PageMember::where('page_id',$page_id)->where('state',1)->count();
                    return 2 .'|'.$page_id.'|'.$page_members;
                }
                #endregion
                break;
            case 'leave':
                #region leave
                $current_page = PageMember::where('page_id',$page_id)->where('user_id',$user_id)->get();
                $current_page_id = $current_page[0]->id;
                $current_page = PageMember::find($current_page_id);
                $current_page->delete();
                #endregion
                $page_members = PageMember::where('page_id',$page_id)->where('state',1)->count();
                return 0 .'|'.$page_id.'|'.$page_members;
                break;

            case 'confirm':
                $current_page = PageMember::where('page_id',$page_id)->where('user_id',$user_id)->get();
                $current_page_id = $current_page[0]->id;
                $current_page = PageMember::find($current_page_id);
                $current_page->update([
                    'state'=>1
                ]);

                $page_members = PageMember::where('page_id',$page_id)->where('state',1)->count();
                return 1 .'|'.$page_id.'|'.$page_members;
                break;
        }
        // return redirect()->back()->with('message','Done Successfully');
        // return $requestType;
    }

    public function requestsPage($id)
    {
        $page = Page::find($id);
        $related_pages = $this->relatedPages($page->category_id);
        $myState = $this->memberState($id);
        $isAdmin = $this->isAdmin($id);
        $page_members = PageMember::where('page_id',$id)->where('state',1)->where('isAdmin','!=', 1)->get();
        $page_requests = PageMember::where('page_id',$id)->where('state',2)->get();
        return view('User.pages.requests',compact('page','page_members','myState', 'isAdmin', 'related_pages','page_requests'));
    }

    public function changePage(Request $request)
    {
        $page = PageMember::find($request->request_id);
        if($request->requestType == 'delete')
        {
            $page->delete();
            return $request->request_id;
        }
        elseif($request->requestType == 'conferm')
        {
            $page->update([
                'state' => 1
            ]);
            return $request->request_id;
        }
    }

    public function adminLeft($id)
    {
        $admins = PageMember::where('page_id',$id)->where('isAdmin',1)->get();
        if(count($admins) > 1)
        {
            $admin = PageMember::where('user_id',Auth::guard('web')->user()->id)->get();
            $admin[0]->delete();
            return redirect('all-page');
        }
        else
        {
            return redirect()->back()->with('error', "you can't left page asign anther admin or remove page");
        }
    }

    public function membersPage($id)
    {
        $page = Page::find($id);
        $related_pages = $this->relatedPages($page->category_id);
        $myState = $this->memberState($id);
        $isAdmin = $this->isAdmin($id);
        $page_members = PageMember::where('page_id',$id)->where('state',1)->where('isAdmin','!=', 1)->get();

        //$admins = PageMember::where('page_id',$id)->where('isAdmin',1)->get();

        $accepteds = [];
        $admins = [];
        // $myFriends =[];

        $accepteds = PageMember::where('page_id', $id)->where('state', 1)->where('isAdmin','!=', 1)->get();


        $admins = PageMember::where('page_id', $id)->where('isAdmin',1)->get();

        if(Auth::guard('web')->user())
        {
            foreach( $accepteds as $enemy){
                $enemy_id= $enemy->user_id;
                $enemy->friendship = $this->CheckUserFriendshipState(Auth::guard('web')->user()->id,$enemy_id);
                $enemy->follow = $this->CheckUserFollowingState(Auth::guard('web')->user()->id,$enemy_id);
            }

            foreach( $admins as $enemyy){
                $enemy_id= $enemyy->user_id;
                $enemyy->friendship = $this->CheckUserFriendshipState(Auth::guard('web')->user()->id,$enemy_id);
                $enemyy->follow = $this->CheckUserFollowingState(Auth::guard('web')->user()->id,$enemy_id);
            }
            $myData = User::find(Auth::guard('web')->user()->id);
            return view('User.pages.members',compact('page','page_members', 'myData', 'admins', 'accepteds', 'myState', 'isAdmin', 'related_pages'));

        }
        else
        {
            return view('User.pages.members',compact('page','page_members', 'admins', 'accepteds', 'myState', 'isAdmin', 'related_pages'));
        }
    }

    public function CheckUserFriendshipState($user,$enemy){
        //Different users
        //1. User => From token
        //2. Enemy=> The person i want to check my friendship with
        /*
         * D
         */
        #region different States
        //Friend
        //pending login => request  cancel request
        //cancel
        //accepted => cancel request
        //guest  => add request

        //return $user . '|' . $enemy;

        $friendship = Friendship::where('senderId',$user)->where('receiverId',$enemy)->get();
        if(count($friendship) > 0){
            switch($friendship[0]->stateId){
                case '3':
                    return 'pending';
                case '2':
                    return 'accepted';
            }
        }else {
            $friendship = Friendship::where('senderId', $enemy)->where('receiverId', $user)->get();
            if (count($friendship) > 0) {
                switch($friendship[0]->stateId){
                    case '3':
                        return 'request';
                    case '2':
                        return 'accepted';
                }
            }else{
                return 'guest';
            }
        }
        //Guest
        //Didn't accept my request
        //i didn't accept his request
        #endregion
    }

    public function frirndshipPage(Request $request){
        //return $request->requestType . $request->enemy_id . Auth::guard('web')->user()->id;
        $requestType = $request->requestType;
        $enemy_id = $request->enemy_id;
        $user_id = Auth::guard('web')->user()->id;

        switch($requestType){
            case 'add':
                Friendship::create([
                    'senderId'=>$user_id,
                    'receiverId'=>$enemy_id,
                    'stateId'=>3
                ]);
                $current_following = Following::where('followerId',$user_id)->where('followingId',$enemy_id)->get();
                if(count($current_following) == 0)
                {
                    Following::create([
                        'followerId'=>$user_id,
                        'followingId'=>$enemy_id,
                    ]);
                }

                $followers = Following::where('followingId',$enemy_id)->count();
                return '3|' . $enemy_id . '|' . $followers;
                break;

            case 'remove':
                $current_friendship = Friendship::where('senderId',$user_id)->where('receiverId',$enemy_id)->get();
                if(count($current_friendship) > 0)
                {
                    $current_friendship_id = $current_friendship[0]->id;
                    $current_friendship = Friendship::find($current_friendship_id);
                    $current_friendship->delete();
                }
                else
                {
                    $current_friendship = Friendship::where('receiverId',$user_id)->where('senderId',$enemy_id)->get();
                    $current_friendship_id = $current_friendship[0]->id;
                    $current_friendship = Friendship::find($current_friendship_id);
                    $current_friendship->delete();
                }
                $current_following = Following::where('followerId',$user_id)->where('followingId',$enemy_id)->get();
                if(count($current_following) > 0)
                {
                    $current_following = Following::find($current_following[0]->id);
                    $current_following->delete();
                }
                $followers = Following::where('followingId',$enemy_id)->count();
                return 0 . '|' . $enemy_id . '|' . $followers;
                break;

            case 'confirm':
                $current_friendship = Friendship::where('receiverId',$user_id)->where('senderId',$enemy_id)->get();
                $current_friendship_id = $current_friendship[0]->id;
                $current_friendship = Friendship::find($current_friendship_id);
                $current_friendship->update([
                    'stateId' =>2
                ]);

                $current_following = Following::where('followerId',$user_id)->where('followingId',$enemy_id)->get();
                if(count($current_following) == 0)
                {
                    Following::create([
                        'followerId'=>$user_id,
                        'followingId'=>$enemy_id,
                    ]);
                }
                $followers = Following::where('followingId',$enemy_id)->count();
                return 2 . $enemy_id . '|' . $followers;
                break;
        }

    }

    public function CheckUserFollowingState($user,$enemy){
        ////Following => Enemy
        ////Follower  => User
        $following = Following::where('followerId',$user)->where('followingId',$enemy)->get();
        if(count($following) > 0){
            return 1;
        }else{
            return 0;
        }
    }

    public function followingPage(Request $request){
        $requestType = $request->requestType;
        $enemy_id = $request->enemy_id;
        $user_id = Auth::guard('web')->user()->id;

        switch($requestType){
            case 'addFollowing':
                Following::create([
                    'followerId'=>$user_id,
                    'followingId'=>$enemy_id,
                ]);

                $followers = Following::where('followingId',$enemy_id)->count();
                return '1|' . $enemy_id . '|' . $followers;
                break;

            case 'removeFollowing':
                $current_following = Following::where('followerId',$user_id)->where('followingId',$enemy_id)->get();
                $current_following = Following::find($current_following[0]->id);
                $current_following->delete();

                $followers = Following::where('followingId',$enemy_id)->count();
                return 0 . '|' . $enemy_id . '|' . $followers;
                break;
        }

    }

    public function asignAdmin(Request $request){
        $requestType = $request->requestType;
        $enemy_id = $request->enemy_id;
        $page_id = $request->page_id;

        switch($requestType){
            case 'addAdmin':
                $current_member = PageMember::where('page_id',$page_id)->where('user_id',$enemy_id)->get();
                $current_member = PageMember::find($current_member[0]->id);
                $current_member->update([
                    'isAdmin' =>1,
                    'state' =>1,
                ]);

                return 1;

                break;

            case 'removeMember':
                $current_member = PageMember::where('page_id',$page_id)->where('user_id',$enemy_id)->get();
                $current_member = PageMember::find($current_member[0]->id);
                $current_member->delete();

                return '0|' . $enemy_id ;
                break;

            case 'invite':
                $current_member = PageMember::where('page_id',$page_id)->where('user_id',$enemy_id)->get();
                if(count($current_member)>0)
                {
                    $current_member = PageMember::find($current_member[0]->id);
                    $current_member->update([
                        'state' =>3,
                    ]);
                }
                if(count($current_member) == 0)
                {
                    PageMember::create([
                        'user_id'=>$enemy_id,
                        'page_id'=>$page_id,
                        'state' =>3,
                    ]);
                }

                return '0|' . $enemy_id ;
                break;

        }

    }

    public function allPage()
    {
        $user_pages_ids = [];
        $user = auth()->user();
        $related_pages = Page::limit(3)->get();
        $all_pages = Page::paginate(30);

        $user_pages = DB::select(DB::raw('select pages.* from pages,page_members
                        where page_members.page_id = pages.id
                        AND page_members.user_id = ' . $user->id));

        foreach ($user_pages as $page) {
            array_push($user_pages_ids, $page->id);
        }


        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.categoryId = categories.id
                        AND user_categories.userId =' . $user->id . ' and categories.type = "post"'));

        $user_interests_array = [];

        foreach ($user_interests as $interest) {
            array_push($user_interests_array, $interest->id);
        }

        $expected_pages = $this->getExpectedPages($user_interests_array, $user_pages_ids);

        return view('User.pages.allPages',compact('related_pages','all_pages','expected_pages'));
    }

    public function myPage()
    {
        $user_pages_ids = [];
        $user = auth()->user();
        $related_pages = Page::limit(3)->get();


        $user_pages = DB::select(DB::raw('select pages.* from pages,page_members
                        where page_members.page_id = pages.id
                        AND page_members.user_id = ' . $user->id));

        foreach ($user_pages as $page) {
            array_push($user_pages_ids, $page->id);
        }


        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.categoryId = categories.id
                        AND user_categories.userId =' . $user->id . ' and categories.type = "post"'));

        $user_interests_array = [];

        foreach ($user_interests as $interest) {
            array_push($user_interests_array, $interest->id);
        }

        $expected_pages = $this->getExpectedPages($user_interests_array, $user_pages_ids);

        if(Auth::guard('web')->user())
        {
            $my_pages = PageMember::where('user_id',Auth::guard('web')->user()->id)->get();
            return view('User.pages.myPages',compact('related_pages','my_pages','expected_pages'));
        }


        return view('User.pages.myPages',compact('related_pages','all_pages','expected_pages'));
    }


    private function getExpectedPages($user_interests_array, $user_pages_ids)
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
