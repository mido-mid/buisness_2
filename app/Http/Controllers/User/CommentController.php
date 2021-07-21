<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Comment;
use App\Models\Post;
use App\Notifications\CommentCreated;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{

    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($post_id)
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

        $post_id = $request->post_id;

        $post = Post::find($post_id);

        $user = auth()->user();

        $rules = [
            'body' => 'required','not_regex:/([%\$#\*<>]+)/',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return $this->returnValidationError(402,$validator);
        }

        $comment = Comment::create([
            'body' => $request->body,
            'user_id' => $user->id,
            'model_id' => $post->id,
            'model_type' => "post",
            'comment_id' => $request->comment_id
        ]);

        if($comment){

            $comments = DB::table('comments')->where('model_id',$post->id)->where('model_type','post')->get();

            $post->comments = $comments;

            $comment->publisher = User::find($comment->user_id);

            $comment->media = DB::table('media')->where('model_id',$comment->id)->where('model_type','comment')->first();

            $view = view('includes.partialcomment', compact('comment','post'));

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['comment'];
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
    public function update(Request $request, $comment_id)
    {
        //
        $post = Post::find($request->model_id);

        $user = auth()->user();

        $comment = Comment::find($comment_id);

        $rules = [
            'body' => 'required','not_regex:/([%\$#\*<>]+)/',
        ];

        $this->validate($request,$rules);

        if($comment){
            $comment->update([
                'body' => $request->body,
                'user_id' => $user->id,
                'model_id' => $request->model_id,
                'model_type' => "post",
                'comment_id' => $request->comment_id
            ]);

            $comments = DB::table('comments')->where('model_id',$post->id)->where('model_type','post')->get();

            $post->comments = $comments;

            $comment->publisher = User::find($comment->user_id);

            $comment->media = DB::table('media')->where('model_id',$comment->id)->where('model_type','comment')->first();

            $view = view('includes.partialcomment', compact('comment','post'));

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['comment'];
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
    public function destroy($comment_id)
    {
        //
        $comment = Comment::find($comment_id);

        if($comment)
        {
            $comment_media = DB::table('media')->where('model_id',$comment->id)->where('model_type','comment')->get();

            foreach ($comment_media as $media){
                DB::table('media')->where('id',$media->id)->delete();
                unlink('media/' . $media->filename);
            }
            $comment->delete();
            return $this->returnSuccessMessage('comment deleted',200);
        }
        else
        {
            return $this->returnError('something wrong happened',402);
        }
    }
}
