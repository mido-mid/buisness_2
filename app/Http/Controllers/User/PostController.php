<?php

namespace App\Http\Controllers\User;

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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

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
        return 'ds';
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $user = auth()->user();
        $user_mentions = [];
        $rules = [
            'body' => ['nullable'],
            'privacy_id' => 'required|integer',
            'media' => 'nullable',
            'media.*' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpg,jpeg,png,svg,gif|max:100040',
            'category_id' => 'required|integer|not_in:0'
        ];

//        $messages = [
//            'privacy_id.required' => trans('error.privacy_required'),
//            'privacy_id.integer' => trans('error.privacy_integer'),
//            'media.mimes' => trans('error.media_mimes'),
//            'media.max' => trans('error.media_max'),
//            'category_id.required' => trans('error.category_required'),
//            'category_id.integer' => trans('error.category_integer'),
//            'category_id.not_in' => trans('error.category_notin')
//        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()->first()
            ], 402);
        }


        preg_match_all("/(@\w+)/", $request->body, $mentions);

        if (count($mentions[0]) > 0) {
            foreach ($mentions[0] as $mention) {
                $user_name = str_replace('@', '', $mention);
                $user_exist = DB::table('users')->whereRaw("user_name like '$user_name%'")->exists();
                if ($user_exist) {
                    array_push($user_mentions, $user_name);
                }
            }
        }

        if ($request->tags != null) {
            $tags = implode(',', $request->tags);
        } else {
            $tags = null;
        }

        $post_mentions = implode(',', $user_mentions);

        $post = Post::create([
            'body' => $request->body,
            'privacyId' => $request->privacy_id,
            'tags' => $tags,
            'mentions' => $post_mentions,
            'postTypeId' => 2,
            'stateId' => 2,
            'publisherId' => $user->id,
            'categoryId' => $request->category_id,
            'group_id' => $request->group_id,
            'page_id' => $request->page_id,
            'post_id' => $request->post_id
        ]);


        if ($post) {

            if ($request->hasFile('media')) {

                $image_ext = ['jpg', 'png', 'jpeg', 'svg', 'gif','JPG'];

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

                    if ($file->move('media', $file_to_store)) {
                        Media::create([
                            'filename' => $file_to_store,
                            'mediaType' => $mediaType,
                            'model_id' => $post->id,
                            'model_type' => "post"
                        ]);
                    }
                }
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

            $post = $this->getPost($user,$post);

            $posts[] = $post;

            if($request->group_id){
                $group = 'exist';
                $view = view('includes.partialpost', compact('posts','group','privacy', 'categories', 'times', 'ages', 'reaches', 'reacts','cities','countries','friends_info'));
            }
            else{
                $view = view('includes.partialpost', compact('posts','privacy', 'categories', 'times', 'ages', 'reaches', 'reacts','cities','countries','friends_info'));
            }

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['post'];
        } else {
            return $this->returnError('something wrong happened', 402);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $post = DB::table('posts')->where('id',$id)->first();

        $user = auth()->user();

        $user_groups_ids = [];

        $user_pages_ids = [];

        $main = new MainController();

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

        $user_groups = DB::select(DB::raw('select groups.*,group_members.state from groups,group_members
                        where group_members.group_id = groups.id
                        AND group_members.user_id = ' . $user->id));

        $user_pages = DB::select(DB::raw('select pages.* from pages,page_members
                        where page_members.page_id = pages.id
                        AND page_members.user_id = ' . $user->id));

        foreach ($user_groups as $group) {
            array_push($user_groups_ids, $group->id);
        }

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

        $expected_groups = $main->getExpectedGroups($user_interests_array, $user_groups_ids);

        $expected_pages = $main->getExpectedPages($user_interests_array, $user_pages_ids);

        $another_comments = 'exist';

        $post = $this->getPost($user,$post);

        return view('User.posts.show', compact('post','expected_groups','expected_pages','privacy', 'categories', 'times', 'ages', 'reaches', 'reacts','cities','countries','friends_info','another_comments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $post_id)
    {
        //

        $post_model = Post::find($post_id);

        $user_mentions = [];

        $user = auth()->user();

        $rules = [
            'body' => ['nullable'],
            'privacy_id' => 'required|integer',
            'media' => 'nullable',
            'media.*' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpg,jpeg,png|max:100040',
            'category_id' => 'required|integer|not_in:0'
        ];

//        $messages = [
//            'privacy_id.required' => trans('error.privacy_required'),
//            'privacy_id.integer' => trans('error.privacy_integer'),
//            'media.mimes' => trans('error.media_mimes'),
//            'media.max' => trans('error.media_max'),
//            'category_id.required' => trans('error.category_required'),
//            'category_id.integer' => trans('error.category_integer'),
//            'category_id.not_in' => trans('error.category_notin')
//        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->returnValidationError(402, $validator);
        }

        if ($post_model) {

            preg_match_all("/(@\w+)/", $request->body, $mentions);

            if (count($mentions[0]) > 0) {
                foreach ($mentions[0] as $mention) {
                    $user_name = str_replace('@', '', $mention);
                    $user_exist = DB::table('users')->whereRaw("user_name like '$user_name%'")->exists();
                    if ($user_exist) {
                        array_push($user_mentions, $user_name);
                    }
                }
            }

            $post_mentions = implode(',', $user_mentions);


            if ($request->tags != null) {
                $tags = implode(',', $request->tags);
            } else {
                $tags = null;
            }

            $post_model->update([
                'body' => $request->body,
                'privacyId' => $request->privacy_id,
                'postTypeId' => 2,
                'mentions' => $post_mentions,
                'tags' => $tags,
                'stateId' => 2,
                'publisherId' => $user->id,
                'categoryId' => $request->category_id,
                'group_id' => $request->group_id,
                'page_id' => $request->page_id,
                'post_id' => $request->post_id
            ]);

            $post = DB::table('posts')->where('id',$post_id)->first();

            if($post->post_id == null) {

                $medias = [];

                $post_media = DB::table('media')->where('model_id', $post_id)->get()->toArray();

                foreach ($post_media as $media) {
                    array_push($medias, $media->filename);
                }

                $checkedimages = $request->input('checkedimages');


                if ($checkedimages != null){
                    $deleted_media = array_diff($medias, $checkedimages);
                }
                else{
                    $deleted_media = $medias;
                }


                if (!empty($deleted_media)) {
                    foreach ($deleted_media as $media) {
                        DB::table('media')->where('filename', $media)->delete();
                        unlink('media/' . $media);
                    }
                }
            }


            if ($request->hasFile('media')) {

                $image_ext = ['jpg', 'png', 'jpeg', 'svg', 'gif','JPG'];

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


                    if ($file->move('media', $file_to_store)) {
                        Media::create([
                            'filename' => $file_to_store,
                            'mediaType' => $mediaType,
                            'model_id' => $post->id,
                            'model_type' => "post"
                        ]);
                    }
                }

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

            $post = $this->getPost($user,$post);

            $posts[] = $post;

            if($request->group_id){
                $group = 'exist';
                $view = view('includes.partialpost', compact('posts','group','privacy', 'categories', 'times', 'ages', 'reaches', 'reacts','cities','countries','friends_info'));
            }
            else{
                $view = view('includes.partialpost', compact('posts','privacy', 'categories', 'times', 'ages', 'reaches', 'reacts','cities','countries','friends_info'));
            }

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['post'];
        } else {
            return $this->returnError('something wrong happened', 402);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($post_id)
    {
        //

        $post = Post::find($post_id);

        if ($post) {
            $post_media = DB::table('media')->where('model_id', $post->id)->where('model_type', 'post')->get();

            foreach ($post_media as $media) {
                DB::table('media')->where('id', $media->id)->delete();
                unlink('media/' . $media->filename);
            }

            $post->delete();

            return $this->returnSuccessMessage(trans('home.delete_post'));
        } else {
            return $this->returnError('something wrong happened', 402);
        }
    }

    public function savedPosts()
    {
        //
        $user = auth()->user();

        $user_groups_ids = [];

        $user_pages_ids = [];

        $main = new MainController();

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

        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.categoryId = categories.id
                        AND user_categories.userId =' . $user->id . ' and categories.type = "post"'));

        $user_interests_array = [];

        foreach ($user_interests as $interest) {
            array_push($user_interests_array, $interest->id);
        }

        $saved_posts = DB::select(DB::raw('select posts.* from posts,saved_posts
                        where saved_posts.post_id = posts.id
                        AND saved_posts.user_id =' . $user->id));

        foreach ($saved_posts as $post) {
            $post = $this->getPost($user,$post);
        }

        $user_groups = DB::select(DB::raw('select groups.*,group_members.state from groups,group_members
                        where group_members.group_id = groups.id
                        AND group_members.user_id = ' . $user->id ));

        $user_pages = DB::select(DB::raw('select pages.* from pages,page_members
                        where page_members.page_id = pages.id
                        AND page_members.user_id = ' . $user->id));

        foreach ($user_groups as $group) {
            array_push($user_groups_ids, $group->id);
        }

        foreach ($user_pages as $page) {
            array_push($user_pages_ids, $page->id);
        }

        $expected_posts = $main->getExpectedPosts($user, $user_interests_array);

        $expected_groups = $main->getExpectedGroups($user_interests_array, $user_groups_ids);

        $expected_pages = $main->getExpectedPages($user_interests_array, $user_pages_ids);

        $another_comments = 'exist';

        return view('User.posts.saved_posts', compact('saved_posts', 'expected_posts','expected_groups','expected_pages','privacy', 'categories', 'times', 'ages', 'reaches', 'reacts','cities','countries','friends_info','another_comments'));
    }

    public function savePost(Request $request)
    {

        $post_id = $request->post_id;

        $user = auth()->user();

        $flag = $request->flag;

        if ($flag == 0) {

            DB::table('saved_posts')->insert([
                'post_id' => $post_id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                "msg" => trans('home.saved'),
                "state" => "saved"
            ]);
        } else {

            $user_post = DB::table('saved_posts')->where('post_id', $post_id)->where('user_id', $user->id)->first();

            DB::table('saved_posts')->delete($user_post->id);

            return response()->json([
                "msg" => trans('home.save_post'),
                "state" => "save post"
            ]);
        }
    }


    public function sponsor(Request $request)
    {

        $user = auth()->user();

        $rules = [
            'timeId' => 'required|integer',
            'reachId' => 'required|integer',
            'age_id' => 'required|integer',
            'country_id' => 'required|integer|not_in:0',
            'city_id' => 'required|integer',
            'category_id' => 'required|integer|not_in:0',
            'price' => 'required|numeric',
            'gender' => ['required', 'string']
        ];

//        $messages = [
//            'timeId.required' => trans('error.time_required'),
//            'timeId.integer' => trans('error.time_integer'),
//            'reachId.required' => trans('error.reach_required'),
//            'reachId.integer' => trans('error.reach_integer'),
//            'postId.required' => trans('error.post_required'),
//            'postId.integer' => trans('error.post_integer'),
//            'age_id.required' => trans('error.age_required'),
//            'age_id.integer' => trans('error.age_integer'),
//            'country_id.required' => trans('error.country_required'),
//            'country_id.integer' => trans('error.country_integer'),
//            'city_id.required' => trans('error.city_required'),
//            'city_id.integer' => trans('error.city_integer'),
//            'category_id.required' => trans('error.category_required'),
//            'category_id.integer' => trans('error.category_integer'),
//            'category_id.not_in' => trans('error.category_notin'),
//            'gender.required' => trans('error.gender_required'),
//            'gender.string' => trans('error.gender_string'),
//            'price.required' => trans('error.price_required'),
//            'price.numeric' => trans('error.price_numeric'),
//        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->returnValidationError(402, $validator);
        }

        $request->session()->put([
            'timeId' => $request->time_id,
            'reachId' => $request->reach_id,
            'postId' => $request->post_id,
            'stateId' => $request->state_id,
            'age_id' => $request->age_id,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'gender' => $request->gender
        ]);

        $data = $request->session()->all();

        return response()->json([
            'total_price' => $data['price']
        ]);
    }

    public function payment(Request $request)
    {

        $user = auth()->user();

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
