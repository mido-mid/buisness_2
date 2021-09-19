<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','mention',"web"]], function () {

    Route::get('/', 'User\MainController@index')->name('welcome');

    Auth::routes(['verify' => true]);
    Route::get('getcities/{country_id}', 'User\ServiceController@getCities');


    Route::group(['middleware' => ['auth','banned','verified']],function() {
        Route::get('home', 'User\MainController@index')->name('home');
        Route::post('joingroup', 'User\GroupsController@enterGroup')->name('join_group');
        Route::post('likepage', 'User\PagesController@likePage')->name('like_page');
        Route::post('addfriend', 'User\FriendshipController@friendship')->name('addfriend');
        Route::post('savepost', 'User\PostController@savePost')->name('savepost');
        Route::get('search/companies/{filter}','User\CompaniesController@search')->name('search_companies');
        Route::get('services/search/categories/{filter}','User\ServiceController@searchCategories')->name('search_categories');
        Route::get('search/{type}/{filter}','User\MainController@search')->name('search');
        Route::get('loadmore/{take?}/{start?}','User\MainController@index');
        Route::get('loadstories/{take?}/{start?}','User\MainController@loadStories')->name('load_stories');
        Route::get('loadcomments/{post_id}/{limit?}/{start?}','User\MainController@loadComments');
        Route::resource('posts', 'User\PostController');
        Route::post('sponsor', 'User\PostController@sponsor')->name('sponsor');
        Route::post('sponsor/payment', 'User\PostController@payment')->name('sponsor.payment');
        Route::post('userreport', 'User\MainController@report')->name('userreports.store');
        Route::resource('comments', 'User\CommentController');
        Route::get('comments', 'User\CommentController@store');
        Route::resource('likes', 'User\LikeController');
        Route::resource('shares', 'User\ShareController');
        Route::resource('stories', 'User\StoryController');
        Route::post('viewstory', 'User\StoryController@viewStory')->name('story.view');
        Route::resource('groups', 'User\GroupsController');
        Route::resource('pages', 'User\PagesController');
        Route::resource('companies', 'User\CompaniesController');
        Route::get('saved_posts', 'User\PostController@savedPosts')->name('saved_posts');
        Route::get('/notifications','User\NotificationsController@index')->name('notifications');
        Route::get('services/categories', 'User\ServiceController@getCategories')->name('service_categories');
        Route::get('services/{category_id?}', 'User\ServiceController@index')->name('services');
        Route::get('myservices/{category?}', 'User\ServiceController@show')->name('myservices.show');
        Route::resource('services','User\ServiceController');
        Route::get('/mark-all-read/{user}', function (User $user) {
            $user->unreadNotifications->markAsRead();
            return response(['message'=>'done', 'notifications'=>$user->notifications]);
        })->name('read');
        Route::get('profile/{user_id}', 'User\ProfileController@edit')->name('profile');
        Route::put('profile', 'User\ProfileController@update')->name('profileupdate');
        Route::put('profile/password', 'User\ProfileController@password')->name('profilepassword');


        //Martina
        #region groups
        Route::resource('groups', 'User\GroupsController');
        Route::any('join-group','User\GroupsController@joinGroup')->name('join-group');
        Route::get('about-group/{id}','User\GroupsController@aboutGroup')->name('about-group');
        Route::get('images-group/{id}','User\GroupsController@imagesGroup')->name('images-group');
        Route::get('videos-group/{id}','User\GroupsController@videosGroup')->name('videos-group');
        Route::get('requests-group/{id}','User\GroupsController@requestsGroup')->name('requests-group');
        Route::any('changeRequest-group','User\GroupsController@changeRequest')->name('changeRequest-group');
        Route::any('adminLeft-group/{id}','User\GroupsController@adminLeft')->name('adminLeft');
        Route::get('members-group/{id}','User\GroupsController@membersGroup')->name('members-group');
        Route::any('frientshep-group','User\GroupsController@frirndshipGroup')->name('frientshep-group');
        Route::any('following-group','User\GroupsController@followingGroup')->name('following-group');
        Route::any('asignAdmin-group','User\GroupsController@asignAdmin')->name('asignAdmin-group');
        Route::any('all-group','User\GroupsController@allGroup')->name('all-group');
        Route::any('my-group','User\GroupsController@myGroup')->name('my-group');
        Route::get('main-group/{id}','User\GroupsController@groupPosts')->name('main-group');
        Route::get('main-group/{id}/{postId}','User\GroupsController@singlePost')->name('single-post-group');


        #region pages
        Route::resource('pages', 'User\PagesController');
        Route::any('join-page','User\PagesController@joinPage')->name('join-page');
        Route::get('about-page/{id}','User\PagesController@aboutPage')->name('about-page');
        Route::get('images-page/{id}','User\PagesController@imagesPage')->name('images-page');
        Route::get('videos-page/{id}','User\PagesController@videosPage')->name('videos-page');
        Route::get('requests-page/{id}','User\PagesController@requestsPage')->name('requests-page');
        Route::any('changeRequest-page','User\PagesController@changePage')->name('changeRequest-page');
        Route::any('adminLeft-page/{id}','User\PagesController@adminLeft')->name('adminLeftPage');
        Route::get('members-page/{id}','User\PagesController@membersPage')->name('members-page');
        Route::any('frientshep-page','User\PagesController@frirndshipPage')->name('frientshep-page');
        Route::any('following-page','User\PagesController@followingPage')->name('following-page');
        Route::any('asignAdmin-page','User\PagesController@asignAdmin')->name('asignAdmin-page');
        Route::any('all-page','User\PagesController@allPage')->name('all-page');
        Route::any('my-page','User\PagesController@myPage')->name('my-page');
        Route::get('main-page/{id}','User\PagesController@pagePosts')->name('main-page');
        Route::get('main-page/{id}/{postId}','User\PagesController@singlePost')->name('single-post-page');




        //redeny profile
        Route::get('user/profile/{user_id}', 'User\ProfileController@index')->name('user.view.profile');
        Route::post('add/friend','User\ProfileController@addFriend')->name('user.add.friend');
        Route::post('profile/add/friend', 'User\ProfileController@addFriendRedeny')->name('redeny.user.add.friend');
        Route::post('profile/follow/friend', 'User\ProfileController@followFriendRedeny')->name('redeny.user.follow.friend');
        Route::post('profile/unfollow/friend', 'User\ProfileController@unfollowFriendRedeny')->name('redeny.user.unfollow.friend');


        Route::post('profile/refuse/friend', 'User\ProfileController@RefuseFriendRedeny')->name('redeny.user.refuse.friend');
        Route::post('profile/accept/friend', 'User\ProfileController@AcceptFriendRedeny')->name('redeny.user.accept.friend');

        Route::post('edit/profile', 'ProfileController@editProfile')->name('redeny.user.edit.profile');
        Route::post('add/friend', 'ProfileController@addFriendProfile')->name('redeny.user.add.friend.profile');
        Route::post('refuse/friend', 'ProfileController@RefuseFriendProfile')->name('redeny.user.refuse.friend.profile');
        Route::post('accept/friend', 'ProfileController@AcceptFriendProfile')->name('redeny.user.accept.friend.profile');
        Route::post('follow/friend', 'ProfileController@followFriendProfile')->name('redeny.user.follow.friend.profile');
        Route::post('unfollow/friend', 'ProfileController@unfollowFriendProfile')->name('redeny.user.unfollow.friend.profile');
        Route::post('add/inspiration', 'ProfileController@addInspirationProfile')->name('redeny.user.add.inspiration.profile');
        Route::post('remove/inspiration', 'ProfileController@removeInspirationProfile')->name('redeny.user.remove.inspiration.profile');
        Route::post('add/music', 'ProfileController@addMusic')->name('redeny.user.add.music');
        Route::post('list/music', 'ProfileController@MusicList')->name('redeny.user.list.music');
        Route::post('list/sport', 'ProfileController@SportList')->name('redeny.user.list.sport');
        Route::post('list/hoppy', 'ProfileController@HobbyList')->name('redeny.user.list.hoppy');
        Route::post('list/inspiration', 'ProfileController@inspirationList')->name('redeny.user.list.inspiration');
        Route::get('profile/view/{user_id}/{component}', 'ProfileController@viewComponent')->name('redeny.view.component');


    });


});
