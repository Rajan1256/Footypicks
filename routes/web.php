<?php

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


Route::get('/', function () {
    return view('welcome');
});
Route::get('/phptest', function () {
    echo phpinfo();
});

Route::get('/testme', function () {
    // return date('Y-m-d H:i:s');
    // date_default_timezone_set('Asia/Calcutta');
    // date_default_timezone_set('America/Los_Angeles');
    return date('Y-m-d H:i:s');
    return date('Y-m-d H:i:s', strtotime("20-07-2018 20:20" . "-0:00"));
});

Route::get('/login', 'Admin\AuthController@showLoginForm');
Route::get('/reset', 'Admin\AuthController@resetPage')->name('a:resetPassPage');
Route::post('/resetPassword', 'Admin\AuthController@resetPassword')->name('resetPassword');
Route::get('/reminderNotification', 'CronController@reminderNotification')->name('reminderNotification');
Route::get('/sendalluserNotification', 'CronController@matchcron')->name('matchcron');
Route::post('/addcron', 'Admin\AddCronJob@addcron')->name('addcron');
Route::post('/updatecron', 'Admin\AddCronJob@update')->name('update');

Route::get('/scriptMigrateTeamImages', function() {
    $teams = App\Models\Team::all();
    // echo $teams;
    $im = new \Imagick(); 
    $im->setBackgroundColor(new ImagickPixel('transparent')); 

    $im->readImage('http://ec2-35-159-53-49.eu-central-1.compute.amazonaws.com/storage/svg_team/Tottenham_Hotspur_FC_logo.svg'); 

    $im->setImageFormat("png32");

    header('Content-type: image/png'); 
    echo $im->getImageBlob();
    $im->writeImage('storage/teams/Tottenham_Hotspur123.png');
    // echo $im;
   
    /*foreach ($teams as $eachTeam) {
        $info = pathinfo($eachTeam->cover);
        echo "/var/www/html/footypicks/storage/app/public/teams/".$info['basename']."<br/>";
    }*/

    /*$BackgroundColor = "rgb(255, 255, 203)";
    $imagick = new \Imagick("http://ec2-35-159-5-64.eu-central-1.compute.amazonaws.com/storage/teamsCURR/Liverpool_FC_logo123.png");
    
    $imagick->transparentPaintImage(
        $BackgroundColor, 0, 0.6 * \Imagick::getQuantum(), false
    );
    
    //Not required, but helps tidy up left over pixels
    $imagick->despeckleimage();
    
    //Need to be in a format that supports transparency
    $imagick->setimageformat('png32');
    
    header("Content-Type: image/png");
    echo $imagick->getImageBlob();
    // $imagick->writeImage('storage/teams/FC_Liverpool1234.png');
    die;*/

    foreach ($teams as $eachTeam) {

        if ($eachTeam->cover) {
           

            $info = pathinfo($eachTeam->cover);

            $string = str_replace(' ', '-', $info['filename']); // Replaces all spaces with hyphens.

            $imgName = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

            $imageName = $imgName . ".png";

            $coverPath = env('APP_URL') . '/storage/teams';

            if ($info['dirname'] !== $coverPath) {
                if ($info["extension"] == "svg") {
                    /*$image = new Imagick();*/
                    try {
                        /*$image->readImageBlob(file_get_contents($eachTeam->cover));
                        $image->setImageFormat("png24");
                        $image->setBackgroundColor(new ImagickPixel('transparent'));
                        $image->writeImage('storage/teams/' . $imageName);*/

                        $BackgroundColor = "rgb(255, 255, 203)";
                        $imagick = new \Imagick("/var/www/html/footypicks/storage/app/public/teams/".$info['basename']);

                        //Need to be in a format that supports transparency
                        $imagick->setimageformat('png32');

                        $imagick->transparentPaintImage(
                            $BackgroundColor, 0, 0.6 * \Imagick::getQuantum(), false
                        );

                        //Not required, but helps tidy up left over pixels
                        $imagick->despeckleimage();

                        header("Content-Type: image/png");
                        $imagick->getImageBlob();
                        
                        $eachTeam->cover = $coverPath . "/" . $imageName;
                        $eachTeam->save();
                    } catch (ImagickException $e) {
                        echo "ERROR converting image";
                    }
                } else {
                    $content = @file_get_contents($eachTeam->cover);
                    if ($content) {
                        file_put_contents('storage/teams/' . $imageName, $content);
                        $eachTeam->cover = $coverPath . "/" . $imageName;
                        $eachTeam->save();
                    }
                }
                print_r($info);
            }
        }
    }
});
Route::group(['prefix' => env('ADMIN_BASE_PATH', 'fadmin')], function () {
    Route::get('/', 'Admin\MainController@users')->name('a:index');
    Route::get('/users', 'Admin\MainController@users')->name('a:users');
    Route::get('/showcron', 'Admin\AddCronJob@matchcron')->name('matchcron');

    //Route::post('/delete1', 'Admin\MainController@delete')->name('deleteUers');
    Route::post('/delete_user', array("as" => "deleteUsers", 'uses' => 'Admin\MainController@delete'));

    Route::get('/leagues', 'Admin\LeagueController@index')->name('a:leagues');
    Route::get('/league/create', 'Admin\LeagueController@create')->name('a:league:create');
    Route::post('/league/create', 'Admin\LeagueController@postCreate');
    Route::get('/league/{id}', 'Admin\LeagueController@edit')->name('a:league:edit');
    Route::post('/league/{id}', 'Admin\LeagueController@postEdit');
    Route::get('/league/delete/{id}', 'Admin\LeagueController@delete')->name('a:league:delete');
    Route::get('/league/finish/{id}', 'Admin\LeagueController@delete')->name('a:league:finish');

    Route::get('/teams', 'Admin\TeamController@index')->name('a:teams');
    Route::get('/teams/create', 'Admin\TeamController@create')->name('a:teams:create');
    Route::post('/teams/create', 'Admin\TeamController@postCreate');
    Route::get('/teams/{id}', 'Admin\TeamController@edit')->name('a:teams:edit');
    Route::post('/teams/{id}', 'Admin\TeamController@postEdit');
    Route::get('/teams/delete/{id}', 'Admin\TeamController@delete')->name('a:teams:delete');

    Route::get('/schedules', 'Admin\ScheduleController@index')->name('a:schedules');
    Route::get('/schedules/{leagueId}/create', 'Admin\ScheduleController@create')->name('a:schedules:create');
    Route::post('/schedules/{leagueId}/create', 'Admin\ScheduleController@postCreate');
    Route::get('/schedules/{id}', 'Admin\ScheduleController@edit')->name('a:schedules:edit');
    Route::post('/schedules/{id}', 'Admin\ScheduleController@postEdit');
    Route::get('/schedules/delete/{id}', 'Admin\ScheduleController@delete')->name('a:schedules:delete');
    Route::get('/schedules/finish/{id}', 'Admin\ScheduleController@finish')->name('a:schedules:finish');
    Route::post('/schedules/finish/{id}', 'Admin\ScheduleController@postFinish');

    Route::get('/games', 'Admin\GameController@index')->name('a:games');
    Route::get('/games/delete/{id}', 'Admin\GameController@delete')->name('a:games:delete');

    Route::get('/settings', 'Admin\SettingController@index')->name('a:settings');
    Route::post('/stopcronjob', 'Admin\AddCronJob@stopcronjob')->name('stopcronjob');
//    Route::post('/GetData', 'Admin\AddCronJob@GetData')->name('GetData');
    Route::post('/getdata', 'Admin\AddCronJob@getdata')->name('getdata');
    Route::post('/delete', 'Admin\AddCronJob@delete')->name('delete');

    Route::get('/login', 'Admin\AuthController@showLoginForm');
    Route::post('/login', 'Admin\AuthController@login')->name('login');
    Route::any('/logout', 'Admin\AuthController@logout')->name('logout');
});

