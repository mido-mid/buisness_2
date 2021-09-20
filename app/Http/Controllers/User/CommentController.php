<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Comment;
use App\Models\Media;
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

        $user_mentions = [];

        $user = auth()->user();

        $rules = [
            'body' => ['required'],
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return $this->returnValidationError(402,$validator);
        }

        preg_match_all("/(@\w+)/", $request->body, $mentions);

        if(count($mentions[0]) > 0) {
            foreach ($mentions[0] as $mention) {
                $user_name = str_replace('@', '', $mention);
                $user_exist = DB::table('users')->whereRaw("user_name like '$user_name%'")->exists();
                if($user_exist) {
                    array_push($user_mentions, $user_name);
                }
            }
        }

        $comment_mentions = implode(',',$user_mentions);

        $comment = Comment::create([
            'body' => $request->body,
            'user_id' => $user->id,
            'model_id' => $post->id,
            'model_type' => "post",
            'mentions' => $comment_mentions,
            'comment_id' => $request->comment_id
        ]);


        if($comment){

            if ($request->hasFile('media')) {

                $image_ext = ['jpg', 'png', 'jpeg', 'svg', 'gif','JPG'];

                $file = $request->file('media');

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
                        'model_id' => $comment->id,
                        'model_type' => "comment"
                    ]);
                }

            }

            $reacts = DB::table('reacts')->get();

            $post_comments = DB::table('comments')->where('model_id',$post->id)->where('model_type','post')->get();

            $post->comments = $post_comments;

            $comment = $this->getComment($user,$comment,$post);

            if($comment->comment_id != null) {
                $reply = $comment;
                $parent_comment = Comment::find($comment->comment_id);
                $comment = $parent_comment;
                $view = view('includes.partialreply', compact('post','comment','reply','reacts'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['reply'];
            }
            else{
                $another_comments = 'exist';
                $comments[] = $comment;
                $view = view('includes.partialcomment', compact('comments','post','reacts','another_comments'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['comment'];
            }
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

        $user_mentions = [];

        $comment = Comment::find($comment_id);

        $rules = [
            'body' => ['required'],
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()->first()
            ],402);
        }

        if($comment){

            preg_match_all("/(@\w+)/", $request->body, $mentions);

            if(count($mentions[0]) > 0) {
                foreach ($mentions[0] as $mention) {
                    $user_name = str_replace('@', '', $mention);
                    $user_exist = DB::table('users')->whereRaw("user_name like '$user_name%'")->exists();
                    if($user_exist) {
                        array_push($user_mentions, $user_name);
                    }
                }
            }

            $comment_mentions = implode(',',$user_mentions);

            $comment->update([
                'body' => $request->body,
                'user_id' => $user->id,
                'model_id' => $request->model_id,
                'model_type' => "post",
                'mentions' => $comment_mentions
            ]);

            if ($request->hasFile('media')) {

                $comment_media = DB::table('media')->where('model_id', $comment_id)->get();

                foreach ($comment_media as $media) {
                    DB::table('media')->where('filename', $media->filename)->delete();
                    unlink('media/' . $media->filename);
                }

                $image_ext = ['jpg', 'png', 'jpeg', 'svg', 'gif', 'JPG'];

                $file = $request->file('media');

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
                        'model_id' => $comment->id,
                        'model_type' => "comment"
                    ]);
                }
            }

            $reacts = DB::table('reacts')->get();

            $post_comments = DB::table('comments')->where('model_id',$post->id)->where('model_type','post')->get();

            $post->comments = $post_comments;

            $comment = $this->getComment($user,$comment,$post);

            if($comment->comment_id != null) {
                $reply = $comment;
                $parent_comment = Comment::find($comment->comment_id);
                $comment = $parent_comment;
                $view = view('includes.partialreply', compact('post','comment','reply','reacts'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['reply'];
            }
            else{
                $another_comments = 'exist';
                $comments[] = $comment;
                $view = view('includes.partialcomment', compact('comments','post','reacts','another_comments'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['comment'];
            }
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
            $post = Post::find($comment->model_id);
            $post->comments = DB::table('comments')->where('model_id',$post->id)->where('model_type','post')->get();
            if ($comment->comment_id != null) {
                $type = 'reply';
            } else {
                $type = 'comment';
            }

            return response()->json([
                'type' => $type,
                'msg' => trans('home.delete_comment'),
                'count' => count($post->comments)
            ]);
        }
        else
        {
            return $this->returnError('something wrong happened',402);
        }
    }


    private function getComment($user,$comment,$post){

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

        return $comment;
    }
}
