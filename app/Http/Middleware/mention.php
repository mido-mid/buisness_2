<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class mention
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $friends_info = [];

        if(Auth::guard('web')->user()){
            $user = auth()->user();

            // friends posts he follows and are public and in groups you are in and in pages you liked
            $friends = DB::table('friendships')->where(function ($q) use ($user){
                $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
            })->where('stateId',2)->get();

            foreach ($friends as $friend){
                $friend_id = $friend->receiverId == $user->id ? $friend->senderId : $friend->receiverId;

                $friend_info = DB::table('users')->select('id','name','user_name as username','personal_image as image')->where('id',$friend_id)->first();

                array_push($friends_info,$friend_info);

            }
        }
        else{
            $friends_info = 'not authenticated';
        }

        View::composer(['layouts.app','layouts.auth'], function($view) use ($friends_info)
        {
            $view->with('friends_mention', json_encode($friends_info));
        });

        return $next($request);
    }
}
