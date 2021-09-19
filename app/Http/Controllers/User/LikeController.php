<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\models\Likes;
use App\Models\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{

    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

        $liked_before = DB::table('likes')->where('model_type',$request->model_type)
            ->where('model_id',$request->model_id)
            ->where('senderId',$user->id)->first();

        if($liked_before){

            if($request->requestType == "update"){
                DB::table('likes')->where('id',$liked_before->id)->update([
                    'reactId' => $request->reactId,
                ]);

                $like = $liked_before;
                $updatelike = true;

                if($request->model_type == "comment"){
                    $comment = DB::table('comments')->where('id',$request->model_id)->first();

                    $comment = $this->getComment($user,$comment);

                    $model = $comment;

                    $model_type = "comment";

                }
                else{
                    $post = DB::table('posts')->where('id',$request->model_id)->first();

                    $post = $this->getPost($user,$post);

                    $model = $post;

                    $model_type = "post";
                }


                $reacts = DB::table('reacts')->get();


                $view = view('includes.liked',compact('model','model_type','reacts'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['liked'];
            }
            else{

                DB::table('likes')->where('id',$liked_before->id)->delete();

                if($request->model_type == "comment"){
                    $comment = DB::table('comments')->where('id',$request->model_id)->first();

                    $comment = $this->getComment($user,$comment);

                    $model = $comment;

                    $model_type = "comment";
                }
                else{
                    $post = DB::table('posts')->where('id',$request->model_id)->first();

                    $post = $this->getPost($user,$post);

                    $model = $post;

                    $model_type = "post";
                }


                $reacts = DB::table('reacts')->get();

                $view = view('includes.unliked',compact('model','model_type','reacts'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['unliked'];
            }
        }
        else{
            $like = Likes::create([
                'model_id' => $request->model_id,
                'model_type' => $request->model_type,
                'senderId' => $user->id,
                'reactId' => $request->reactId,
            ]);

            $updatelike = false;


            if($request->model_type == "comment"){
                $comment = DB::table('comments')->where('id',$request->model_id)->first();

                $comment = $this->getComment($user,$comment);

                $model = $comment;

                $model_type = "comment";
            }
            else{
                $post = DB::table('posts')->where('id',$request->model_id)->first();

                $post = $this->getPost($user,$post);

                $model = $post;

                $model_type = "post";
            }


            $reacts = DB::table('reacts')->get();

            $view = view('includes.liked',compact('model','model_type','reacts'));

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['liked'];
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
    public function update(Request $request, $id)
    {
        return 'nothing';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($like_id)
    {
        //
        $like = Likes::find($like_id);

        if($like)
        {
            $like->delete();
//            return $this->returnSuccessMessage('like deleted');
            return "";
        }
        else
        {
//            return $this->returnError(402,'error happened');
            return "";
        }
    }

    private function getComment($user,$comment){
        $comment->publisher = User::find($comment->user_id);
        $comment->media = DB::table('media')->where('model_id',$comment->id)->where('model_type','comment')->first();
        $comment->replies = DB::table('comments')->where('model_id',$comment->id)->where('model_type','post')->where('comment_id',$comment->id)->get();
        $comment->likes = DB::table('likes')->where('model_id',$comment->id)->where('model_type','comment')->get();
        $comment->type = $comment->comment_id != null ? 'reply' : 'comment';
        $comment->replies->count = count($comment->replies);
        $comment->likes->count = count($comment->likes);
        $comment->liked = DB::table('likes')->where('model_id',$comment->id)->where('model_type','comment')->where('senderId',$user->id)->first();

        if($comment->liked){
            $comment->user_react = DB::table('reacts')->where('id',$comment->liked->reactId)->get();
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

        return $comment;
    }

    private function getPost($user,$post){
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

        return $post;
    }
}
