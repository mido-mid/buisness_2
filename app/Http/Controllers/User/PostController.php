<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
