<?php

namespace Tests\Unit\Services;

use App\Models\Bet;
use App\Models\Game;
use App\Models\HeadToHead;
use App\Models\HeadToHeadBet;
use App\Models\League;
use App\Models\Schedule;
use App\Models\UserInGame;
use App\Models\User;
use Tests\TestCase;
use App\Services\Game as Service;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GameTest extends TestCase
{
    use DatabaseMigrations;

    private $userOwnerId;
    private $teamWinId;
    private $scheduleId;
    private $leagueId;
    private $headToHeadBetType = HeadToHeadBet::TYPE_WIN;
    private $users = [];

    public function testCreateGame()
    {
        $service = new Service();
        $owner = factory(User::class)->create();
        $invitedUser = factory(User::class)->create();
        factory(League::class)
            ->create()
            ->each(function ($u) {
                $u->teams()->saveMany(factory(\App\Models\Team::class, 2)->make());
            });

        $leagueModel = League::query()
            ->with('teams')
            ->orderBy('id', 'desc')
            ->first();

        $game = $service->createGame($owner->id, $leagueModel->id, 'FirstGame', [$invitedUser->id]);

        $game = Game::query()->with('connect')->find($game->id);
        $this->assertNotNull($game->id);
        $this->assertEquals('FirstGame', $game->name);
        $this->assertEquals(Game::ACTIVE, $game->status);
        $this->assertEquals($owner->id, $game->user_id);

        $this->assertEquals(2, $game->connect->count());

        $InvitedUserInGame = $game->connect->first();
        $this->assertNotNull($InvitedUserInGame->id);
    }

    public function testUpdateGameResults()
    {
        $owner = factory(User::class)->create();
        $invitedUser = factory(User::class)->create();
        factory(League::class)
            ->create()
            ->each(function ($u) {
                $u->teams()->saveMany(factory(\App\Models\Team::class, 2)->make());
            });

        $leagueModel = League::query()
            ->with('teams')
            ->orderBy('id', 'desc')
            ->first();

        $schedule = factory(Schedule::class)
            ->create([
                'status' => Schedule::FINISHED,
                'team_home_id' => $leagueModel->teams[0]->id,
                'team_away_id' => $leagueModel->teams[1]->id,
                'league_id' => $leagueModel->id,
                'goals_home_team' => 1,
                'goals_away_team' => 2,
            ]);

        $service = new Service();
        $service->setScheduleModel($schedule);

        $game = $service->createGame($owner->id, $leagueModel->id, 'FirstGame', [$invitedUser->id]);

        $bet = new Bet();
        $bet->fill([
            'user_id' => $owner->id,
            'game_id' => $game->id,
            'schedule_id' => $schedule->id,
            'team_id' => $leagueModel->teams[0]->id,
            'type' => 0
        ]);
        $bet->save();


        $betOpponent = new Bet();
        $betOpponent->fill([
            'user_id' => $invitedUser->id,
            'game_id' => $game->id,
            'schedule_id' => $schedule->id,
            'team_id' => $leagueModel->teams[0]->id,
            'type' => 2
        ]);
        $betOpponent->save();

        $userInGame = UserInGame::query()
            ->where('user_id', $owner->id)
            ->where('game_id', $game->id)->first();

        $this->assertEquals(0, $userInGame->points);
        $service->updateGameResults();

        $userInGame = UserInGame::query()
            ->where('user_id', $owner->id)
            ->where('game_id', $game->id)->first();
        $this->assertEquals(1, $userInGame->points);

        $userInGame = UserInGame::query()
            ->where('user_id', $invitedUser->id)
            ->where('game_id', $game->id)->first();
        $this->assertEquals(0, $userInGame->points);

        $this->assertTrue(true);
    }

    public function testUpdateHeadToHeadResults()
    {
        $owner = factory(User::class)->create();
        $this->userOwnerId = $owner->id;
        $invitedUser = factory(User::class)->create();
        $this->users = [
            $owner->id => $invitedUser->id,
            $invitedUser->id => $owner->id,
        ];
        factory(League::class)
            ->create()
            ->each(function ($u) {
                $u->teams()->saveMany(factory(\App\Models\Team::class, 2)->make());
            });

        $leagueModel = League::query()
            ->with('teams')
            ->orderBy('id', 'desc')
            ->first();
        $this->leagueId = $leagueModel->id;

        $schedule = factory(Schedule::class)
            ->create([
                'status' => Schedule::FINISHED,
                'team_home_id' => $leagueModel->teams[0]->id,
                'team_away_id' => $leagueModel->teams[1]->id,
                'league_id' => $leagueModel->id,
                'goals_home_team' => 1,
                'goals_away_team' => 2,
            ]);
        $this->scheduleId = $schedule->id;
        $this->teamWinId = $leagueModel->teams[1]->id;

        $service = new Service();
        $service->setScheduleModel($schedule);

        $this->createHth('Test headToHead game', HeadToHead::GAME_TYPE_SINGLE);
        $this->createHth('Test headToHead game', HeadToHead::GAME_TYPE_SINGLE);
        $this->createHth('Test headToHead game Tie', HeadToHead::GAME_TYPE_SINGLE);

        $this->createHth('Test dare game',HeadToHead::GAME_TYPE_DARE, 'Wish');
        $this->createHth('Test dare game', HeadToHead::GAME_TYPE_DARE, 'Wish');
        $this->createHth('Test dare game Tie', HeadToHead::GAME_TYPE_DARE,  'Wish');

        $service->updateHeadToHeadResults();
        $service->updateHeadToHeadResults();
        $service->updateHeadToHeadResults();

        $user = User::query()->with('userStat')->where('id', $this->userOwnerId)->first();
        $this->assertEquals(3, $user->userStat->hth_win);
        $this->assertEquals(3, $user->userStat->dares_win);
        $this->assertEquals(0, $user->userStat->hth_lose);
        $this->assertEquals(0, $user->userStat->dares_lose);

        $user = User::query()->with('userStat')->where('id', $invitedUser->id)->first();
        $this->assertEquals(0, $user->userStat->hth_win);
        $this->assertEquals(0, $user->userStat->dares_win);
        $this->assertEquals(3, $user->userStat->hth_lose);
        $this->assertEquals(3, $user->userStat->dares_lose);

        $this->assertEquals('true', 'true');
    }

    private function createHth($name, $gameType, $wish = '')
    {
        $headToHeadModel = new HeadToHead();
        $data = [
            'user_id' => $this->userOwnerId,
            'schedule_id' => $this->scheduleId,
            'name' => $name,
            'wish' => $wish,
            'is_pick' => true,
            'game_type' => $gameType,
            'status' => HeadToHead::ACTIVE
        ];

        $headToHeadModel->fill($data);

        if (!$headToHeadModel->save()) {
            $this->error('Your Hth not save. Db Error');
            die();
        }

        $this->createHthBet($headToHeadModel->id, $this->userOwnerId);
        $this->createHthBet($headToHeadModel->id, $this->users[$this->userOwnerId]);
    }

    private function createHthBet($hthId, $uid)
    {
        $headToHeadBetModel = new HeadToHeadBet();
        $headToHeadBetModel->head_to_head_id = $hthId;
        $headToHeadBetModel->user_id = $uid;
        $headToHeadBetModel->team_id = $this->teamWinId;
        $headToHeadBetModel->type = ($uid == $this->userOwnerId) ? $this->headToHeadBetType : HeadToHeadBet::TYPE_LOSE;
        if (!$headToHeadBetModel->save()) {
            $this->error('Your Bet not save. Db Error');
            die();
        }
    }
}
