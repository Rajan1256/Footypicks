<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\HeadToHead;
use App\Models\HeadToHeadBet;
use App\Models\HeadToHeadInvite;
use App\Models\Schedule;
use App\Models\User;
use App\Models\League;
use App\Models\UserInGame;
use Illuminate\Console\Command;

class GenerateFinishGames extends Command
{

    private $userOwnerId;
    private $users = [];
    private $teamWinId;
    private $scheduleId;
    private $leagueId;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:games:finish {uid1} {uid2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $uid1 = $this->argument('uid1');
        $uid2 = $this->argument('uid2');
        if(!($uid1 && $uid2)) {
            $this->error('User id is empty');
            return;
        }
        $userOne = User::query()->find($uid1);
        $userTwo = User::query()->find($uid2);
        $this->userOwnerId = $userOne->id;
        $this->users = [
            $uid1 => $uid2,
            $uid2 => $uid1,
        ];

        if(!isset($userOne->id,$userTwo->id)) {
            $this->error('User not found');
            return;
        }
        $league = factory(\App\Models\League::class)->make([
            'status' => League::STATUS_FINISH
        ]);
        $league->save();
        $this->leagueId = $league->id;

        $teamOne = $this->createTeam($league);
        $teamTwo = $this->createTeam($league);
        $this->teamWinId = $teamTwo->id;


        $schedule = factory(\App\Models\Schedule::class)->make([
            'league_id' => $this->leagueId,
            'team_home_id' => $teamOne->id,
            'goals_home_team' => 0,
            'team_away_id' => $teamTwo->id,
            'goals_away_team' => 1,
            'status' => Schedule::FINISHED,
        ]);
        $schedule->save();
        $this->scheduleId = $schedule->id;

        $this->createGame($userOne->id);
        $this->createGame($userTwo->id);
        $this->createGame();

        $this->createHth($userOne->id, 'Test headToHead game', HeadToHead::GAME_TYPE_SINGLE);
        $this->createHth($userTwo->id, 'Test headToHead game', HeadToHead::GAME_TYPE_SINGLE);
        $this->createHth('', 'Test headToHead game Tie', HeadToHead::GAME_TYPE_SINGLE);

        $this->createHth($userOne->id, 'Test dare game',HeadToHead::GAME_TYPE_DARE, 'Wish');
        $this->createHth($userTwo->id, 'Test dare game', HeadToHead::GAME_TYPE_DARE, 'Wish');
        $this->createHth('', 'Test dare game Tie', HeadToHead::GAME_TYPE_DARE,  'Wish');
    }

    private function createTeam($league)
    {
        $teamModel = factory(\App\Models\Team::class)->make([
            'league_id' => $league->id,
        ]);
        $teamModel->save();
        return $teamModel;
    }

    private function createGame($winUserId = null)
    {
        $model = new Game();
        $model->fill([
            'name' => ($winUserId) ? 'Test Game' : 'Test Game Tie',
            'user_id' => $this->userOwnerId,
            'league_id' => $this->leagueId,
            'status' => Game::FINISH_STATUS
        ]);

        if (!$model->save()) {
            $this->error('Your Game not save. Db Error');
            die();
        }
        $data = [];
        foreach (array_keys($this->users) as $playerId) {
            $data[] = [
                'game_id' => $model->id,
                'user_id' => $playerId,
                'points' => ($playerId ==  $winUserId) ? 1 : 0,
                'status'  => UserInGame::STATUS_FINISH
            ];
        }

        $model->connect()->createMany($data);
    }

    private function createHth($winnerId, $name = 'Test Win hth TYPE SCORE', $gameType, $wish = '')
    {
        $userId = ($winnerId) ? $winnerId : $this->userOwnerId;
        $headToHeadModel = new HeadToHead();
        $data = [
            'user_id' => $this->userOwnerId,
            'schedule_id' => $this->scheduleId,
            'name' => $name,
            'wish' => $wish,
            'is_pick' => true,
            'game_type' => $gameType,
            'status' => HeadToHead::STATUS_FINISH
        ];
        if($winnerId){
            $data['win_user_id'] = $winnerId;
        }
        $headToHeadModel->fill($data);

        if (!$headToHeadModel->save()) {
            $this->error('Your Hth not save. Db Error');
            die();
        }

        $this->createHthBet($headToHeadModel->id, $userId, $winnerId);
        $this->createHthBet($headToHeadModel->id, $this->users[$userId], $winnerId);
    }

    private function createHthBet($hthId, $uid, $winnerId)
    {
        $headToHeadBetModel = new HeadToHeadBet();
        $headToHeadBetModel->head_to_head_id = $hthId;
        $headToHeadBetModel->user_id = $uid;
        $headToHeadBetModel->team_id = $this->teamWinId;
        $headToHeadBetModel->type = ($uid == $winnerId) ? HeadToHeadBet::TYPE_WIN : HeadToHeadBet::TYPE_LOSE;
        if (!$headToHeadBetModel->save()) {
            $this->error('Your Bet not save. Db Error');
            die();
        }
    }

}
