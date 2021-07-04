<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Page;
use App\Models\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($flag)
    {

        $user = auth()->user();

        if($flag == 0) {
            $pages = Page::all();

            foreach ($pages as $page) {
                $user_page = DB::table('user_pages')
                    ->where([['page_id',$page->id],['user_id',$user->id]])
                    ->first();

                $page_users = DB::table('user_pages')
                    ->where('group_id',$page->id)
                    ->count();

                $page->users = $page_users;

                if ($user_page) {
                    $page['liked'] = 1;
                }
                else{
                    $page['liked'] = 0;
                }
            }
        }
        else{
            $pages = DB::select(DB::raw('select pages.* from pages,user_pages
                        where user_pages.page_id = pages.id
                        AND user_pages.user_id =
                        '.$user->id));
        }

        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.category_id = categories.id
                        AND user_categories.user_id ='.$user->id.'and categories.type = 0'));

        $user_interests_array = [];

        foreach ($user_interests as $interest){
            array_push($user_interests_array,$interest->id);
        }

        $expected_pages = Page::whereIn('category_id',$user_interests)->limit(3);

        foreach ($expected_pages as $page) {
            $page_users = DB::table('group_members')
                ->where('page_id',$page->id)->where('state',1)
                ->count();

            $page->users = $page_users;
        }

        return view('User.pages.index');
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

    public function likePage(Request $request,$flag) {

        $page_id = $request->page_id;

        $user = auth()->user();

        $page = Page::find(2);

        if($flag == 0) {

            DB::table('user_pages')->insert([
                'page_id' => $page_id,
                'user_id' => $user->id,
            ]);

//          return $this->returnSuccessMessage('you have entered the group successfully', 200);
        }
        else{
            $user_page = DB::table('user_pages')->where('page_id',2)->where('user_id',1)->first();

            DB::table('user_pages')->delete($user_page->id);

//            return $this->returnSuccessMessage('you have exit this group', 200);
        }
    }
}
