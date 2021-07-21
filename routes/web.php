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

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');

    Auth::routes(['verify' => true]);


    Route::group(['middleware' => ['auth','verified']],function() {

        Route::get('home', 'User\MainController@index')->name('home');
        Route::post('joingroup', 'User\GroupsController@enterGroup')->name('join_group');
        Route::post('likepage', 'User\PagesController@likePage')->name('like_page');
        Route::post('addfriend', 'User\FriendshipController@friendship')->name('addfriend');
        Route::post('savepost', 'User\PostController@savePost')->name('savepost');
        Route::resource('posts', 'User\PostController');
        Route::post('sponsor', 'User\PostController@sponsor')->name('sponsor');
        Route::post('report', 'User\MainController@report')->name('reports.store');
        Route::resource('comments', 'User\CommentController');
        Route::get('comments', 'User\CommentController@store');
        Route::resource('likes', 'User\LikeController');
        Route::resource('shares', 'User\ShareController');
        Route::resource('stories', 'User\StoryController');
        Route::post('viewstory', 'User\StoryController@viewStory')->name('story.view');
        Route::resource('groups', 'User\GroupsController');
        Route::resource('pages', 'User\PagesController');
        Route::resource('companies', 'User\CompaniesController');
        Route::get('saved_posts', 'User\PostController@savedPosts')->name('savedposts');
        Route::get('/notifications','User\NotificationsController@index')->name('notifications');
        Route::get('services/categories', 'User\ServiceController@getCategories')->name('service_categories');
        Route::get('services/{category_id?}', 'User\ServiceController@index')->name('services');
        Route::resource('services','User\ServiceController');
        Route::get('/mark-all-read/{user}', function (User $user) {
            $user->unreadNotifications->markAsRead();
            return response(['message'=>'done', 'notifications'=>$user->notifications]);
        })->name('read');
        Route::get('profile/{user_id}', 'User\ProfileController@edit')->name('profile');
        Route::put('profile', 'User\ProfileController@update')->name('profileupdate');
        Route::put('profile/password', 'User\ProfileController@password')->name('profilepassword');
    });


});
