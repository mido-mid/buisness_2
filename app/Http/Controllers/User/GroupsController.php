<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupsController extends Controller
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
            $groups = Group::all();

            foreach ($groups as $group) {
                $user_group = DB::table('group_members')
                    ->where([['group_id',$group->id],['user_id',1],['state',1]])
                    ->first();

                $groups_users = DB::table('group_members')
                    ->where('group_id',$group->id)->where('state',1)
                    ->count();

                $group->users = $groups_users;

                if ($user_group) {
                    $group['entered'] = 1;
                }
                else{
                    $group['entered'] = 0;
                }
            }
        }
        else{
            $groups = DB::select(DB::raw('select groups.* from groups,group_members
                        where group_members.group_id = groups.id
                        AND group_members.user_id = 1 and group_members.state = 1'));

            foreach ($groups as $group) {
                $groups_users = DB::table('group_members')
                    ->where('group_id',$group->id)->where('state',1)
                    ->count();

                $group->users = $groups_users;
            }
        }

        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.categoryId = categories.id
                        AND user_categories.userId = 1 and categories.type = 0'));

        $user_interests_array = [];

        foreach ($user_interests as $interest){
            array_push($user_interests_array,$interest->id);
        }

        $expected_groups = Group::whereIn('category_id',$user_interests_array)->limit(3);

        foreach ($expected_groups as $group) {
            $groups_users = DB::table('group_members')
                ->where('group_id',$group->id)->where('state',1)
                ->count();

            $group->users = $groups_users;
        }

        return view('User.groups.index');
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

    public function enterGroup(Request $request) {
            $group_id = $request->group_id;
            $flag = $request->flag;

            $user = auth()->user();

            $group = Group::find($group_id);

            if ($flag == 0) {
                if ($group->privacy == 1) {
                    DB::table('group_members')->insert([
                        'group_id' => $group_id,
                        'user_id' => $user->id,
                        'state' => 2,
                        'isAdmin'=>0

                    ]);

                    return $this->returnSuccessMessage('your request has been sent', 200);
                } else {
                    DB::table('group_members')->insert([
                        'group_id' => $group_id,
                        'user_id' => $user->id,
                        'state' => 1,
                        'isAdmin'=>0
                    ]);
                    return $this->returnSuccessMessage('you have entered the group successfully', 200);
                }
            } else {
                $user_id = auth()->user()->id;
                $current_group = DB::table('group_members')->where('group_id',$group_id)->where('user_id',$user_id)->first();
                $current_group_id = $current_group->id;
                if($this->isGroupAdmin($current_group_id) == 1){
                    if($this->groupAdmins($group_id) > 1 ){
                        $current_group = DB::table('group_members')->find($current_group_id);
                        $current_group->delete();
                        return $this->returnSuccessMessageWithStatus('Done Successfully',200,true);
                    }else{
                        return $this->returnSuccessMessageWithStatus('group must have at least one admin',200,false);
                    }
                }else{
                    $current_group = DB::table('group_members')->find($current_group_id);
                    $current_group->delete();
                    return $this->returnSuccessMessageWithStatus('Done Successfully',200,true);
                }
                #endregion
            }
        }

    public function isGroupAdmin($member_id){
        $group_member =  DB::table('group_members')->find($member_id);
        return $group_member->isAdmin;
    }
    public function groupAdmins($group_id){
        $group_admins =  DB::table('group_members')->where('group_id',$group_id)->count();
        return $group_admins;
    }
}
