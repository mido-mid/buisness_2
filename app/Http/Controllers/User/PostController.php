<?php

namespace App\Http\Controllers\User;

use App\Friendship;
use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Comment;
use App\Models\Group;
use App\models\Likes;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
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
        $groups_posts = [];
        $pages_posts = [];
        $friends_stories = [];
        $expected_friends = [];
        $user_sponsored_posts = [];
        $user_groups_ids = [];
        $user_pages_ids = [];
        // friends posts he follows and are public and in groups you are in and in pages you liked
        $friends = DB::table('friendships')->where(function ($q){
            $q->where('senderId', 1)->orWhere('receiverId', 1);
        })->where('stateId',2)->get();

        $all_sponsored_posts = DB::select(DB::raw('select sponsored.gender,sponsored.created_at as sponsored_at,sponsored_time.time,posts.*,sponsored_reach.reach,countries.name as country_name,cities.name as city_name,sponsored_ages.from,sponsored_ages.to from
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
        $user_posts = DB::table('posts')->where('publisherId',1)->where('postTypeId',1)->where('stateId',2)->get()->toArray();
        foreach ($friends as $friend){
            $friend_id = $friend->receiverId == 1 ? $friend->senderId : $friend->receiverId;
            $friend_posts = DB::table('posts')->where('publisherId',$friend_id)->where('postTypeId',1)->where('stateId',2)->get();
            $friend_stories = DB::table('stories')->where('publisherId',$friend_id)->get();
            $friends_of_friend = DB::table('friendships')->where(function ($q) use($friend){
                $q->where('senderId', $friend->id)->orWhere('receiverId', $friend->id);
            })->where('stateId',2)->limit(3);

            foreach ($friend_posts as $post){
                $post->type = $post->post_id == null ? "post" : "share";
                array_push($friends_posts,$post);
            }

            foreach ($friend_stories as $story){
                array_push($friends_stories,$story);
            }

            foreach ($friends_of_friend as $user){
                array_push($expected_friends,$user);
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

        $posts = array_merge($user_posts,$friends_posts,$group_posts,$page_posts);

        foreach ($posts as $post){
            $comments = DB::table('comments')->where('model_id',$post->id)->where('type',$post->type)->get()->toArray();
            $likes = DB::table('likes')->where('postId',$post->id)->get()->toArray();
            $shares = DB::table('posts')->where('post_id',$post->id)->get()->toArray();

            $post->comments = $comments;
            $post->likes = $likes;

            $post->comments['count'] = count($comments);
            $post->likes['count'] = count($likes);
            $post->shares = count($shares);
        }

        $stories = array_merge($user_stories,$friends_stories);

        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.categories_id = categories.id
                        AND user_categories.user_id ='.$user->id .'and categories.type = "post"'));

        $user_interests_array = [];

        foreach ($user_interests as $interest){
            array_push($user_interests_array,$interest->id);
        }

//public posts having same interest of user
        $expected_posts = Post::whereIn('category_id',$user_interests_array)->where('publisherId','!=',$user->id)->where('privacyId',1)->limit(3);

        $expected_groups = Group::whereIn('category_id',$user_interests_array)->whereNotIn('id',$user_groups_ids)->limit(3);

        $expected_pages = Page::whereIn('category_id',$user_interests_array)->whereNotIn('id',$user_pages_ids)->limit(3);

        return view('home',compact('posts','stories','expected_friends','expected_groups','expected_pages','expected_posts','user_sponsored_posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $user = auth()->user();
        $rules = [
            'body' => 'required','not_regex:/([%\$#\*<>]+)/',
            'privacy_id' => 'required|integer',
            'media' => 'nullable',
            'media.*' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpg,jpeg,png|max:100040',
            'category_id' => 'required|integer'
        ];

        $this->validate($request,$rules);

        if($request->hasFile('images')){

            $image_ext = ['jpg', 'png', 'jpeg'];

            $video_ext = ['mpeg', 'ogg', 'mp4', 'webm', '3gp', 'mov', 'flv', 'avi', 'wmv', 'ts'];

            $files = $request->file('media');

            foreach ($files as $file) {

                $fileextension = $file->getClientOriginalExtension();

                if (in_array($fileextension, $image_ext)) {
                    $mediaType = 'image';
                } else {
                    $mediaType = 'video';
                }

                $filename = $file->getClientOriginalName();
                $file_to_store = time() . '_' . explode('.', $filename)[0] . '_.' . $fileextension;

                if($file->move('media', $file_to_store)) {
                    Media::create([
                        'filename' => $file_to_store,
                        'mediaType' => $mediaType,
                        'model_id' => $request->model_id,
                        'model_type' => "post"
                    ]);
                }
            }
        }

        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'privacyId' => $request->privacy_id,
            'postTypeId' => 1,
            'stateId' => 1,
            'publisherId' => $user->id,
            'categoryId' => $request->category_id,
            'group_id' => $request->group_id,
            'page_id' => $request->page_id,
            'post_id' => $request->post_id
        ]);

        if($post){
            return redirect()->route('posts.index')->withStatus('post successfully created');
        }
        else{
            return redirect()->route('posts.index')->withStatus('something wrong happened');
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $post_id)
    {
        //
        $post = Post::find($post_id);

        $user = auth()->user();

        $rules = [
            'title' => 'required','min:5','not_regex:/([%\$#\*<>]+)/',
            'body' => 'required','min:10','not_regex:/([%\$#\*<>]+)/',
            'privacy_id' => 'required|integer',
            'media' => 'nullable',
            'media.*' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpg,jpeg,png|max:100040',
            'category_id' => 'required|integer'
        ];

        $this->validate($request,$rules);

        if($post){

            $post->update([
                'title' => $request->title,
                'body' => $request->body,
                'privacyId' => $request->privacy_id,
                'postTypeId' => 1,
                'stateId' => 1,
                'publisherId' => $user->id,
                'categoryId' => $request->category_id,
                'group_id' => $request->group_id,
                'page_id' => $request->page_id,
                'post_id' => $request->post_id
            ]);

            if($request->hasFile('images')){

                $image_ext = ['jpg', 'png', 'jpeg'];

                $video_ext = ['mpeg', 'ogg', 'mp4', 'webm', '3gp', 'mov', 'flv', 'avi', 'wmv', 'ts'];

                $files = $request->file('media');

                foreach ($files as $file) {

                    $post_media = DB::table('media')->where('model_id',$request->post_id)->get();

                    foreach ($post_media as $media){
                        $media->delete();
                        unlink('media/' . $media->filename);
                    }

                    $fileextension = $file->getClientOriginalExtension();

                    if (in_array($fileextension, $image_ext)) {
                        $mediaType = 'image';
                    } else {
                        $mediaType = 'video';
                    }

                    $filename = $file->getClientOriginalName();
                    $file_to_store = time() . '_' . explode('.', $filename)[0] . '_.' . $fileextension;

                    if($file->move('media', $file_to_store)) {
                        Media::create([
                            'filename' => $file_to_store,
                            'mediaType' => $mediaType,
                            'model_id' => $request->model_id,
                            'model_type' => "post"
                        ]);
                    }
                }

            }

            if($request->has('checkedimages')){

                $post_media = [];

                foreach ($post_media as $media){
                    $post_media = $media->filename;
                }

                $checkedimages = $request->input('checkedimages');

                $deleted_media = array_diff($post_media, $checkedimages);

                if (!empty($deleted_media)) {
                    foreach ($deleted_media as $media) {
                        DB::table('media')->where('filename',$media)->delete();
                        unlink('product_images/' . $media);
                    }
                }
            }

            return redirect()->route('posts.index')->withStatus('post successfully created');
        }

       else{
           return redirect()->route('posts.index')->withStatus('something wrong happened');
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($post_id)
    {
        //

        $post = Post::find($post_id);

        if($post)
        {
            $post_media = DB::table('media')->where('postId',$post->id)->get();

            foreach ($post_media as $media){
                $media->delete();
                unlink('media/' . $media->filename);
            }

            $post->delete();

            return redirect()->route('posts.index')->withStatus('post successfully created');
        }
        else
        {
            return redirect()->route('posts.index')->withStatus('something wrong happened');
        }
    }

    public function savedPosts()
    {
        //
        $user = auth()->user();

        $saved_posts = DB::select(DB::raw('select posts.* from posts,saved_posts
                        where saved_posts.post_id = posts.id
                        AND saved_posts.user_id ='.$user->id));

        foreach ($saved_posts as $post){
            $comments = DB::table('comments')->where('model_id',$post->id)->where('type',$post->type)->get()->toArray();
            $likes = DB::table('likes')->where('postId',$post->id)->get()->toArray();
            $shares = DB::table('posts')->where('post_id',$post->id)->get()->toArray();

            $post->comments = count($comments);
            $post->likes = count($likes);
            $post->shares = count($shares);
            $post->comments_details = $comments;
            $post->likes_details = $likes;

            $post->publisher = User::find($post->publisherId);

            $post_media = DB::table('media')->where('model_id',$post->id)->where('model_type','post')->get();

            if(count($post_media) > 0){
                foreach ($post_media as $media) {
                    array_push($post_media_array,asset('media/'.$media->filename));
                }
            }
            $post->media = $post_media_array;
        }

        $user_interests = DB::select(DB::raw('select interests.id from categories,user_interests
                        where user_interests.interest_id = interests.id
                        AND user_interests.user_id ='.$user->id));

        $expected_pages = Page::whereIn('interest_id',$user_interests)->limit(3);

        return view('User.saved_posts',compact('saved_posts','expected_pages'));
    }

    public function savePost(Request $request) {

        $post_id = $request->post_id;

        $user = auth()->user();

        $flag = $request->flag;

        if($flag == 0) {

            DB::table('saved_posts')->insert([
                'post_id' => $post_id,
                'user_id' => $user->id,
            ]);

            return $this->returnSuccessMessage('you have saved this post', 200);
        }
        else{

            $user_post = DB::table('saved_posts')->where('post_id',$post_id)->where('user_id',$user->id)->first();

            DB::table('saved_posts')->delete($user_post->id);

            return $this->returnSuccessMessage('you have unsaved this post', 200);
        }
    }


    public function sponsor(Request $request) {

        $user = auth()->user();

        $sponsored = DB::table('sponsored')->insert([
            'timeId' => $request->time_id,
            'reachId' => $request->reach_id,
            'postId' => $request->post_id,
            'stateId' => $request->state_id,
            'age_id' => $request->age_id,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'price' => $request->price,
            'gender' => $request->gender
        ]);

        if($sponsored){
            return $this->returnSuccessMessage('you have saved this post', 200);
        }
        else{
            return $this->returnError('something wrong happened',402);
        }
    }

    public function report(Request $request) {

        $user = auth()->user();

        $report = DB::table('report')->insert([
            'body' => $request->body,
            'model_id' => $request->model_id,
            'model_type' => $request->type,
            'stateId' => 2,
        ]);

        if($report){
            return $this->returnSuccessMessage('your report have been created successfully', 200);
        }
        else{
            return $this->returnError('something wrong happened',402);
        }
    }
}
