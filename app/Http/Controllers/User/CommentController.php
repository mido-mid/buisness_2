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

//        $post_id = $request->post_id;
//
//        $post = Post::find($post_id);

        $post_user = User::find(3);

        $user = auth()->user();

//        $rules = [
//            'body' => 'required','not_regex:/([%\$#\*<>]+)/',
//        ];
//
//        $this->validate($request,$rules);

        $comment = Comment::create([
            'body' => 'letgo',
            'user_id' => 4,
            'model_id' => 17,
            'model_type' => "post",
        ]);

        if($comment){
            try {
                Notification::send($post_user, new CommentCreated($comment));
            } catch(\Exception $e){

            }
//            return $this->returnData(['user','comment'],[$post_user->load('notifications'),$comment]);
            return "";
        }
        else{
//            return $this->returnError(402,'error happened');
            return "";
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
                'model_id' => $request->post_id,
                'model_type' => $request->type,
                'comment_id' => $request->comment_id
            ]);
//            return $this->returnData(['comment'],[$comment]);
            return "";
        }
        else{
//            return $this->returnError(402,'error happened');
            return "";
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
            $comment->delete();
//            return $this->returnSuccessMessage('comment deleted',200);
            return "";
        }
        else
        {
//            return $this->returnError(402,'error happened');
            return "";
        }
    }
}
