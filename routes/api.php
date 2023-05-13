<?php

use Illuminate\Http\Request;

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
 

Route::get('/statusSchedule', 'Api\Cron@statusSchedule');

Route::post('/uplode_teams_img', 'Api\StripPayment@uploded_img');

Route::get('/info', 'Api\Controller@getApiInfo');

Route::get('/Test_all', 'Api\TestController@index');

Route::post('/addmoney', 'Api\StripPayment@StripPayment')->name('StripPayment');

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', 'Api\RegisterController@login');
    Route::post('/reset', 'Api\RegisterController@reset');
    Route::post('/reset_v2', 'Api\RegisterController@reset_v2');
    Route::post('/save_key', 'Api\RegisterController@save_key');
    Route::post('/registration', 'Api\RegisterController@registration');
});


Route::group(['middleware' => 'api_auth', 'prefix' => 'user'], function () {
    Route::get('/', 'Api\UserController@index');
    Route::get('/stats', 'Api\UserController@getStats');
    Route::get('/invite', 'Api\UserController@getInviteCount');
    Route::get('/notification', 'Api\UserController@getNotifications');
    Route::get('/search', 'Api\UserController@filterAction');
    Route::post('/push_token', 'Api\UserController@pushToken');
    Route::post('/', 'Api\UserController@update');
    Route::get('/{id}', 'Api\UserController@getOne');
    Route::delete('/cover', 'Api\UserController@deleteCover');
    //TODO NEED delete aften be a new version
    Route::get('/game/{id}/schedule', 'Api\GameController@getUserGameSchedule');
});

Route::group(['middleware' => 'api_auth', 'prefix' => 'league'], function () {
    Route::get('/', 'Api\StandingController@leagues');
    Route::get('/{id}', 'Api\StandingController@oneLeague');
    Route::get('/{id}/schedule', 'Api\StandingController@getLeagueSchedule');
    Route::get('/{league_id}/{section_id}/leaguePaginate', 'Api\StandingController@leaguePaginate');
//Route::get('/schedule/{id}', 'Api\StandingController@schedule_detail');
//Route::get('/{id}/all_schedule', 'Api\StandingController@getallLeagueSchedule');
    Route::get('/{id}/team/{team_id}/history', 'Api\StandingController@getTeamHistorySchedule');
    Route::get('/team/{id}', 'Api\StandingController@getPlayers');
    Route::get('/team/{id}/news', 'Api\StandingController@getNews');
    Route::get('/team/{id}/schedule', 'Api\StandingController@getSchedule');
    Route::post('/schedule/{id}', 'Api\StandingController@updateSchedule');
    Route::get('/over_schedule/{sch_id}', 'Api\StandingController@over_schedule');
});

Route::group(['middleware' => 'api_auth', 'prefix' => 'game'], function () {
    Route::get('/', 'Api\GameController@index');
    Route::get('{league_id}/{matchday}/{matchstage}/Round_points/', 'Api\GameController@Round_points');   
    Route::post('/bet', 'Api\GameController@createBet');
    Route::get('/show/{id}', 'Api\GameController@show');
    //Route::get('/{id}/schedule', 'Api\GameController@getUserGameSchedule');
    Route::get('/{id}/{section_id}/schedule', 'Api\GameController@getUserGameSchedule');
    Route::get('/{id}', 'Api\GameController@getOne');
    Route::delete('/{id}', 'Api\GameController@delete');
    /*Route::get('/invite/{id}', 'Api\GameController@confirm');*/
    /* change get method to post method (12-12-2018) */
    Route::post('/invite/{id}', 'Api\GameController@confirm');
    Route::post('/', 'Api\GameController@create');
});

Route::group(['middleware' => 'api_auth', 'prefix' => 'hth'], function () {
    Route::get('/', 'Api\HeadToHeadController@index');
    Route::get('/show/{id}', 'Api\HeadToHeadController@show');
    Route::get('/{id}', 'Api\HeadToHeadController@getOne');
    Route::post('/create', 'Api\HeadToHeadController@create');
    Route::post('/confirm/{id}', 'Api\HeadToHeadController@confirm');
    Route::delete('/{id}', 'Api\HeadToHeadController@delete');
});

Route::group(['middleware' => 'api_auth', 'prefix' => 'dare'], function () {
    Route::get('/', 'Api\DareController@index');
    Route::get('/show/{id}', 'Api\DareController@show');
    Route::get('/{id}', 'Api\DareController@getOne');
    Route::post('/create', 'Api\DareController@create');
    Route::post('/confirm/{id}', 'Api\DareController@confirm');
    Route::delete('/{id}', 'Api\DareController@delete');
});

Route::group(['middleware' => 'api_auth', 'prefix' => 'feedback'], function () {
    Route::post('/add', 'Api\FeedbackController@create');
});


Route::group(['middleware' => 'api_auth', 'prefix' => 'feed'], function () {
    Route::get('/feeds', 'Api\FeedsController@index');
    Route::get('/show_comment/{id}', 'Api\FeedsController@getdata');
    Route::get('/myFeeds', 'Api\FeedsController@myFeeds');
    Route::post('/create', 'Api\FeedsController@create');
    Route::post('/delete', 'Api\FeedsController@delete');
    Route::post('/update', 'Api\FeedsController@update');
 Route::get('/single_user/{id}', 'Api\FeedsController@single_user');
    Route::post('/create_follow', 'Api\FeedsController@create_follow');
     Route::post('/create_comment', 'Api\FeedsController@create_comment');
    Route::post('/create_like', 'Api\FeedsController@create_like');

	Route::post('/show_social_count', 'Api\FeedsController@show_social_count');

    Route::get('/social_count', 'Api\FeedsController@social_count');
});




//Route::middleware('api')->get('/user', function (Request $request) {
//    return response()->json(['OK'=>200]);
//});




