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
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $user = auth()->user();
        $rules = [
            'body' => ['required'],
            'privacy_id' => 'required|integer',
            'media' => 'nullable',
            'media.*' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpg,jpeg,png,svg,gif|max:100040',
            'category_id' => 'required|integer'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()->first()
            ],402);
        }

        if($request->hasFile('media')){

            $image_ext = ['jpg', 'png', 'jpeg','svg','gif'];

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
            'body' => $request->body,
            'privacyId' => $request->privacy_id,
            'postTypeId' => 2,
            'stateId' => 2,
            'publisherId' => $user->id,
            'categoryId' => $request->category_id,
            'group_id' => $request->group_id,
            'page_id' => $request->page_id,
            'post_id' => $request->post_id
        ]);

        $privacy = DB::table('privacy_type')->get();

        $categories = DB::table('categories')->where('type','post')->get();

        $times = DB::table('sponsored_time')->get();

        $reaches = DB::table('sponsored_reach')->get();

        $ages = DB::table('sponsored_ages')->get();

        $reacts = DB::table('reacts')->get();

        $post->publisher = User::find($post->publisherId);
        $comments = DB::table('comments')->where('model_id',$post->id)->where('model_type','post')->get();
        $likes = DB::table('likes')->where('model_id',$post->id)->where('model_type','post')->get();
        $shares = DB::table('posts')->where('post_id',$post->id)->get()->toArray();

        $post->comments = $comments;
        $post->likes = $likes;
        $post->type = $post->post_id != null ? 'share' : 'post';

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

        if($post){
            $view = view('includes.partialpost', compact('post','privacy','categories','times','ages','reaches','reacts'));

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['post'];
        }
        else{
            return $this->returnError('something wrong happened',402);
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
            'body' => ['required'],
            'privacy_id' => 'required|integer',
            'media' => 'nullable',
            'media.*' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpg,jpeg,png|max:100040',
            'category_id' => 'required|integer'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return $this->returnValidationError(402,$validator);
        }

        if($post){

            $post->update([
                'body' => $request->body,
                'privacyId' => $request->privacy_id,
                'postTypeId' => 2,
                'stateId' => 2,
                'publisherId' => $user->id,
                'categoryId' => $request->category_id,
                'group_id' => $request->group_id,
                'page_id' => $request->page_id,
                'post_id' => $request->post_id
            ]);

            if($request->hasFile('media')){

                $image_ext = ['jpg', 'png', 'jpeg','svg','gif'];

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

            $privacy = DB::table('privacy_type')->get();

            $categories = DB::table('categories')->where('type','post')->get();

            $times = DB::table('sponsored_time')->get();

            $reaches = DB::table('sponsored_reach')->get();

            $ages = DB::table('sponsored_ages')->get();

            $reacts = DB::table('reacts')->get();

            $post->publisher = User::find($post->publisherId);
            $comments = DB::table('comments')->where('model_id',$post->id)->where('model_type','post')->get();
            $likes = DB::table('likes')->where('model_id',$post->id)->where('model_type','post')->get();
            $shares = DB::table('posts')->where('post_id',$post->id)->get()->toArray();

            $post->comments = $comments;
            $post->likes = $likes;
            $post->type = $post->post_id != null ? 'share' : 'post';

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

            $view = view('includes.partialpost', compact('post','privacy','categories','times','ages','reaches','reacts'));

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['post'];
        }

       else{
           return $this->returnError('something wrong happened',402);
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
            $post_media = DB::table('media')->where('model_id',$post->id)->where('model_type','post')->get();

            foreach ($post_media as $media){
                DB::table('media')->where('id',$media->id)->delete();
                unlink('media/' . $media->filename);
            }

            $post->delete();

            return $this->returnSuccessMessage('post successfully deleted');
        }
        else
        {
            return $this->returnError('something wrong happened',402);
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

            return $this->returnSuccessMessage('saved');
        }
        else{

            $user_post = DB::table('saved_posts')->where('post_id',$post_id)->where('user_id',$user->id)->first();

            DB::table('saved_posts')->delete($user_post->id);

            return $this->returnSuccessMessage('save post');
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
