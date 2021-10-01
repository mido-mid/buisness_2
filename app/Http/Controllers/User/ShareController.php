<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShareController extends Controller
{
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

        $rules = [
            'body' => ['nullable'],
            'privacy_id' => 'required|integer',
        ];

        $messages = [
            'privacy_id.required' => trans('error.privacy_required'),
            'privacy_id.integer' => trans('error.privacy_integer'),
            'media.mimes' => trans('error.media_mimes'),
            'media.max' => trans('error.media_max'),
            'category_id.required' => trans('error.category_required'),
            'category_id.integer' => trans('error.category_integer'),
            'category_id.not_in' => trans('error.category_notin')
        ];

        $validator = Validator::make($request->all(), $rules,$messages);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()->first()
            ], 402);
        }

        $share = Post::create([
            'body' => $request->body,
            'privacyId' => $request->privacy_id,
            'senderId' => $user->id,
        ]);

        if($share){
            return redirect()->route('home');
        }
        else{
            return redirect()->route('home');
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
    public function update(Request $request, $share_id)
    {
        //
        $share = Post::find($share_id);

        $user = auth()->user();

        $rules = [
            'body' => 'required','min:10','not_regex:/([%\$#\*<>]+)/',
            'privacy_id' => 'required|integer',
        ];

        $this->validate($request,$rules);

        if($share){

           $share->update([
                'body' => $request->body,
                'privacyId' => $request->privacy_id,
                'publisherId' => $user->id,
            ]);

            return redirect()->route('home');
        }

        else{
            return redirect()->route('home');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($share_id)
    {
        //
        $share = Post::find($share_id);

        if($share)
        {
            $share->delete();
            return redirect()->route('home');
        }
        else
        {
            return redirect()->route('home');
        }
    }
}
