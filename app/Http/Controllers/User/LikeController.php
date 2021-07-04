<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\models\Likes;
use Illuminate\Http\Request;

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

        $like = Likes::create([
            'model_id' => $request->model_id,
            'senderId' => $user->id,
            'model_type' => $request->model_type,
            'reactId' => $request->react_id,
        ]);

        if($like){
//            return $this->returnData(['like'],$like);
            return "";
        }
        else{
//            return $this->returnError(403,'error happened');
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
}
