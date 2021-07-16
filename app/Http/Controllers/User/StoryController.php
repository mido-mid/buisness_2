<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Media;
use App\Models\Story;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoryController extends Controller
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
        $cover_file_to_store = null;
        $rules = [
            'body' => ['required','not_regex:/([%\$#\*<>]+)/'],
            'privacy_id' => 'required|integer',
            'cover_image' => 'nullable|mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpg,jpeg,png|max:100040',
            'media' => 'nullable',
            'media.*' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpg,jpeg,png|max:100040',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()->first()
            ],402);
        }


        if($request->hasFile('cover_image')){

            $file = $request->file('cover_image');

            $fileextension = $file->getClientOriginalExtension();

            $filename = $file->getClientOriginalName();

            $cover_file_to_store = time() . '_' . explode('.', $filename)[0] . '_.' . $fileextension;

            $file->move('media', $cover_file_to_store);
        }

        $story = Story::create([
            'body' => $request->body,
            'cover_image' => $cover_file_to_store,
            'privacyId' => $request->privacy_id,
            'publisherId' => $user->id,
        ]);


        if($request->hasFile('media')){

            $image_ext = ['jpg', 'png', 'jpeg','JPG'];

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
                        'model_id' => $story->id,
                        'model_type' => "story"
                    ]);
                }
            }
        }

        if($story){
            $story->publisher = User::find($story->publisherId);
            $story->media = DB::table('media')->where('model_id',$story->id)->where('model_type','story')->first();
            $view = view('includes.partialstory', compact('story'));

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['story'];
        }
        else{
            return $this->returnError('something wrong happened',402);
        }
    }


    public function viewStory(Request $request)
    {
        //
        $user = auth()->user();

        $story = DB::table('stories_views')->insert([
            'story_id' => $request->story_id,
            'user_id' => $user->id,
        ]);


        if($story){
            return $this->returnSuccessMessage('success');
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
    public function update(Request $request, $story_id)
    {
        //
        $story = Story::find($story_id);

        $user = auth()->user();

        $rules = [
            'body' => 'required','min:10','not_regex:/([%\$#\*<>]+)/',
            'privacy_id' => 'required|integer',
            'media' => 'nullable',
            'media.*' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpg,jpeg,png|max:100040',
        ];

        $this->validate($request,$rules);

        if($story){

            $story->update([
                'body' => $request->body,
                'privacyId' => $request->privacy_id,
                'publisherId' => $user->id,
            ]);

            if($request->hasFile('images')){

                $image_ext = ['jpg', 'png', 'jpeg'];

                $video_ext = ['mpeg', 'ogg', 'mp4', 'webm', '3gp', 'mov', 'flv', 'avi', 'wmv', 'ts'];

                $files = $request->file('media');

                foreach ($files as $file) {

                    $story_media = DB::table('media')->where('model_id',$request->story_id)->get();

                    foreach ($story_media as $media){
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
                            'model_type' => "story"
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

            $story->publisher = User::find($story->publisherId);
            $story->media = DB::table('media')->where('model_id',$story->id)->where('model_type','story')->first();
            $view = view('includes.partialstory', compact('story'));

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['story'];
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
    public function destroy($story_id)
    {
        //
        $story = Story::find($story_id);

        if($story)
        {
            $story_media = DB::table('media')->where('model_id',$story->id)->get();

            foreach ($story_media as $media){
                $media->delete();
                unlink('media/' . $media->filename);
            }

            $story->delete();

            return $this->returnSuccessMessage('post successfully deleted');
        }
        else
        {
            return $this->returnError('something wrong happened',402);
        }
    }
}
