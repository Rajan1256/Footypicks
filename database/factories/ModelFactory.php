<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Schedule;

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'nickname' => $faker->userName,
        'dt_birthday' => $faker->date('Y-m-d'),
        'favorite_team' => $faker->word,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Models\UserStat::class, function (Faker\Generator $faker) {});

$factory->define(App\Models\Article::class, function (Faker\Generator $faker) {
    $infoWords = $faker->numberBetween(50,150);
    return [
        'user_id' => 1,
        'category_id' => 1,
        'title' => $faker->name,
        'info' => $faker->text($infoWords),
        'date_from' => $faker->date(),
        'date_to' => $faker->dateTimeBetween('now', '30 years')->format('Y-m-d'),
        'time_from' => $faker->time(),
        'time_to' => $faker->time(),
        'is_private' => 0,
        'number_extra_tickets' => $faker->numberBetween(0, 20),
        'address' =>  $faker->country . ' ' . $faker->city . ' ' . $faker->address,
        'lat' => $faker->latitude(),
        'lng' => $faker->longitude(),
        'status' => 1,
    ];
});

$factory->define(App\Models\League::class, function (Faker\Generator $faker) {
    return [
        'caption' => $faker->company,
        'name' => $faker->citySuffix,
        'teams_count' => 2,
        'games_count' => $faker->numberBetween(35,50),
        'current_matchday' => 1,
        'matchdays_count' => 30,
        'league_parse_id' => 0,
        'parse_id_v2' => 0,
        'cover' => "LegaSerieA.png",
        'last_updated' => $faker->dateTime(),
    ];
});

$factory->define(App\Models\Schedule::class, function (Faker\Generator $faker) {
    static $team_home_id;
    static $team_away_id;

    return [
        'team_home_id' => (int) $team_home_id,
        'team_home_id_parse' => (int) $team_home_id,
        'team_away_id' => (int) $team_away_id,
        'team_away_id_parse' => (int) $team_away_id,
        'date' => $faker->dateTime(),
        'matchday' => 1,
        'goals_home_team' => 0,
        'goals_away_team' => 0,
        'status' => Schedule::SCHEDULED,
    ];
});

$factory->define(App\Models\Team::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
        'cover' => $faker->imageUrl('500', '500', 'sports', true, 'football'),
        'played_games' => $faker->randomNumber(1),
        'position' => $faker->randomNumber(1),
        'points' => $faker->randomNumber(2),
        'wins' => $faker->randomNumber(2),
        'draws' => $faker->randomNumber(2),
        'losses' => $faker->randomNumber(2),
        'parse_id' => 0,
        'parse_id_v2' => 0
    ];
});

$factory->define(App\Models\Game::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->words,
    ];
});