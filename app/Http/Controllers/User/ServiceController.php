<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\models\Likes;
use App\Models\Media;
use App\Models\Phone;
use App\Models\Post;
use App\Models\React;
use App\Models\State;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ratchet\App;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($category_id = null)
    {
        //
        $categories = Category::where('type','service')->get();
        if($category_id != null) {

            $services = Post::where('postTypeId', 1)->where('categoryId',$category_id)->get();
            if (count($services) > 0) {
                foreach ($services as $service) {
                    $media = DB::table('media')->where('model_id',$service->id)->where('model_type','post')->get()->toArray();
                    $publisher = User::find($service->publisherId);
                    $follow = DB::table('following')->Where('followerId',2)->first();
                    $service->media = $media;
                    $service->publisher = $publisher;
                    $service->follow = $follow  ? 'true' : 'false';
                }
            }
        }
        else{
            $services = Post::where('postTypeId', 1)->get();
            if (count($services) > 0) {
                foreach ($services as $service) {
                    $media = DB::table('media')->where('model_id',$service->id)->where('model_type','post')->get()->toArray();
                    $publisher = User::find($service->publisherId);
                    $follow = DB::table('following')->Where('followerId',2)->first();
                    $service->media = $media;
                    $service->publisher = $publisher;
                    $service->follow = $follow  ? 'true' : 'false';
                }
            }
        }

        return view('User.services.show',compact('services','categories','category_id'));
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

        $service = Post::where('postTypeId',2)->where('id',$id)->first();
        $user = auth()->user();
        if( $service ) {
            $media = DB::table('media')->where('model_id',$service->id)->where('model_type','post')->get()->toArray();
            $publisher = User::find($service->publisherId);
            $follow = DB::table('following')->Where('followerId',2)->first();
            $service->media = $media;
            $service->publisher = $publisher;
            $service->follow = $follow  ? 'true' : 'false';
            return view('User.services.show',compact('service'));
        }else{
            return redirect()->route('services.show')->withStatus('no service with this id');
        }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getCategories() {
        $categories = Category::where('type','service')->get();
        return view('User.services.index',compact('categories'));
    }

//    public function follow(Request $request) {
//
//        $followerId = $request->followerId;
//        $followingId = $request->followingId;
//
//        $following = DB::table('following')->insert([
//            'followerId' => $followerId,
//            'followingId' => $followingId
//        ]);
//
//        return $this->returnSuccessMessage('you are now following this user','');
//    }
//
//    public function postLikes($postId){
//        $reacts = React::get();
//        foreach ($reacts as $react){
//            $likes  []= [$react->name => Likes::where('postId',$postId)->where('reactId',$react->id)->get()];
//        }
//        return $likes;
//    }
}
