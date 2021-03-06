<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api',
    'namespace' => 'Api',
    'prefix' => 'auth'
], function ($router) {
    #region redeny routes 1
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    #endregion
    #region usama routes update 1
    Route::Resource('users','users');
    //Route::get('logout', 'AuthController@logout')->name('logout');
    Route::get('test', 'AuthController@test');

    Route::post('groups/{flag}', 'GroupController@getGroups');
    Route::get('allGroups', 'GroupController@getAllGroups');
    Route::post('entergroup', 'GroupController@enterGroup');
    Route::post('addGroup', 'GroupController@addGroup');
    Route::post('removeGroup', 'GroupController@removeGroup');
    Route::post('editGroup', 'GroupController@updateGroup');
    Route::post('group/details', 'GroupController@show');
    Route::post('getPosts','GroupController@getPosts');

    Route::post('addReport', 'GroupController@addReport');
    Route::post('addPost', 'GroupController@addPost');
    Route::post('removePost', 'GroupController@removePost');
    Route::post('getMyPosts', 'GroupController@getMyPosts');
    Route::post('addComment', 'GroupController@addComment');
    Route::post('updateComment', 'GroupController@updateComment');
    Route::post('removeComment', 'GroupController@removeComment');
    Route::post('getPostComments', 'GroupController@getPostComments');
    Route::post('getPostLikes', 'GroupController@getPostLikes');
    Route::post('addLike', 'GroupController@addLike');
    Route::post('removeLike', 'GroupController@removeLike');
    Route::post('updateLike', 'GroupController@updateLike');
    Route::get('get_all_reacts', 'GroupController@get_all_reacts');

    //Services
    Route::post('addService', 'ServiceController@addService');
    Route::post('editService', 'ServiceController@editService');

    Route::post('group/members', 'GroupController@membersGroupWithState');
    Route::post('group/members/collection', 'GroupController@membersGroupWithCollections');
    Route::post('group/members/collection/pending', 'GroupController@membersGroupWithCollectionsPending');

    Route::post('group/members/handle', 'GroupController@handleGroupMembersRequests');
    Route::post('group/admins/assign', 'GroupController@assignGroupAdmin');

    Route::post('group/media', 'GroupController@GetGroupMedia');



    Route::post('pages/{flag}', 'PageController@getPages');
    Route::post('likepage', 'PageController@likePage');
    Route::post('addPage', 'PageController@addPage');
    Route::post('editPage', 'PageController@updatePage');
    Route::post('removePage', 'PageController@removePage');
    Route::post('page/details', 'PageController@show');



    Route::post('page/members', 'PageController@membersPageWithState');
    Route::post('page/admins/assign', 'PageController@assignPageAdmin');
    Route::post('page/members/collection', 'PageController@membersPageWithCollections');
    Route::post('page/media', 'PageController@GetPageMedia');

    #region friendships
    //requestType = addFriendRequest
    //requestType = acceptFriendRequest
    //requestType = refuseFriendRequest
    //view all requests = viewFriendRequest
    Route::post('friendRequest', 'FriendshipController@friendship');
    Route::get('friends/show/','FriendshipController@showFriendsToInvite');
    Route::post('follow', 'FriendshipController@follow');
    Route::post('unfollow', 'FriendshipController@unfollow');
    //Check Friendship state
    #endregion

    Route::get('savedposts', 'PostController@getSavedPosts');
    Route::post('savepost', 'PostController@savePost');
    Route::resource('posts', 'Posts\PostController');
    //Comments
    Route::post('posts/update/{post}', 'PostController@update');
    Route::resource('CommentController', 'comments');
    Route::post('comments/update/{comment}', 'CommentController@update');
    //Likes
    Route::resource('likes', 'LikeController');
    Route::post('likes/update/{like}', 'LikeController@update');
    //Shares
    Route::post('group/post/add/share','GroupController@shareGroupPost');
    Route::post('group/post/get/share','GroupController@getShareGroupPost');

    #endregion
});
Route::group([
    'middleware' =>'checkLang','check_verified',
    'namespace' => 'Api',
], function ($router) {
    Route::post('register', 'AuthController@register');
    Route::post('verify', 'AuthController@verifycode');
    Route::post('login', 'AuthController@login');
    Route::post('delete_user', 'AuthController@delete_user');


    Route::post('resetpassword', 'AuthController@resetpassword');
    Route::post('forgetpassword', 'AuthController@forgetpassword');
    Route::post('newpassword', 'AuthController@newpassword');
    Route::get('companies', 'ServiceController@getCompanies');
    Route::post('categories', 'ServiceController@getCategories');
    Route::post('services', 'ServiceController@getServices');
    Route::post('service', 'ServiceController@showService');
    Route::post('search/services/', 'ServiceController@searchService');
    Route::get('cities/', 'CitiesController@getCities');

    Route::post('profile/{user_id}', 'ProfileController@index');
    Route::post('profileEdit', 'ProfileController@editProfile');
    Route::post('profileImage', 'ProfileController@editImage');

    Route::post('profile/musics/view', 'ProfileController@musics');
    Route::post('profile/musics/add', 'ProfileController@addMusic');
    Route::post('profile/musics/remove', 'ProfileController@reomveMusic');

    Route::post('profile/sports/view/all', 'ProfileController@allsports');
    Route::post('profile/sports/view', 'ProfileController@sports');
    Route::post('profile/sports/add', 'ProfileController@addSport');
    Route::post('profile/sports/remove', 'ProfileController@reomveSport');

    Route::post('profile/hobbies/view/all', 'ProfileController@allhobbies');
    Route::post('profile/hobbies/view', 'ProfileController@hobbies');
    Route::post('profile/hobbies/add', 'ProfileController@addHobby');
    Route::post('profile/hobbies/remove', 'ProfileController@reomveHobby');

    Route::post('profile/media/{type}', 'ProfileController@getProfileMedia');
    Route::post('profile/inspirations/view', 'ProfileController@inspirations');
    Route::post('profile/inspirations/add', 'ProfileController@addInspirationProfile');
    Route::post('profile/inspirations/remove', 'ProfileController@removeInspirationProfile');
    Route::post('homePage','MainController@getHomePosts');
    Route::get('countries','MainController@countries');
    Route::get('cities/{country_id}','MainController@cities');



    Route::group([
        'prefix'=>'home',
        'namespace'=>'Groups'
    ],function() {
        Route::post('/','GroupController@index');
    });


});

