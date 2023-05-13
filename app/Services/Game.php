<?php

namespace App\Services;

use App\Models\Bet;
use App\Models\HeadToHead;
use App\Models\Schedule;
use App\Models\Game as Model;
use App\Models\UserInGame;
use App\Models\UserStat;
use Illuminate\Support\Facades\Log;

class Game extends Base
{

    protected $orderBy = [];

    protected $modelName = Model::class;

    /** @var Schedule */
    private $scheduleModel;

    /**
     * @return Schedule
     */
    public function getScheduleModel(): Schedule
    {
        return $this->scheduleModel;
    }

    /**
     * @param Schedule $scheduleModel
     */
    public function setScheduleModel(Schedule $scheduleModel)
    {
        $this->scheduleModel = $scheduleModel;
    }

    /**
     * @param $userId int
     * @param $name string
     * @param $leagueId int
     * @param array $opponentsUserIds
     * @return Model
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws ExceptionService
     */
    public function createGame($userId, $leagueId, $name, array $opponentsUserIds)
    {
        if(!isset($opponentsUserIds[0])) {
            Log::critical('Invalid Invited User array');
            throw new ExceptionService('Invalid Invited User array');
        }

        $model = new Model();
        $model->fill([
            'name' => $name,
            'user_id' => $userId,
            'league_id' => $leagueId,
        ]);

        if (!$model->save()) {
            Log::critical('Game not save. Db Error');
            throw new ExceptionService('Game not save. Db Error');
        }

        $data = [];
        foreach ($opponentsUserIds as $playerId) {
            $data[] = [
                'game_id' => $model->id,
                'user_id' => $playerId
            ];
        }

        $data[] = [
            'game_id' => $model->id,
            'user_id' => $userId,
            'status'  => UserInGame::ACTIVE
        ];

        $model->connect()->createMany($data);
        return $model;
    }

    public function updateGameResults()
    {
        $gamesBets = $this->getScheduleModel()->bets()->where('status', Bet::IN_GAME)->get();
        if(!$gamesBets->count()) {
            Log::info('Service:Game ' . __FUNCTION__ . $this->getScheduleModel()->id . ' no games bets');
            return;
        }
        foreach ($gamesBets as $gamesBet) {
            if ($this->getScheduleModel()->getResultByTeamId($gamesBet->team_id) != $gamesBet->type) {
                $gamesBet->status = Bet::NOT_ACTIVE;
                $gamesBet->save();
                continue;
            }

            UserInGame::query()
                ->where('game_id', $gamesBet->game_id)
                ->where('user_id', $gamesBet->user_id)
                ->where('status', UserInGame::ACTIVE)
                ->increment('points');

            $gamesBet->status = Bet::ACTIVE;
            $gamesBet->save();
        }
    }

    public function updateHeadToHeadResults()
    {
        HeadToHead::query()->where('status', HeadToHead::STATUS_INVITED)->update(['status' => HeadToHead::NOT_ACTIVE]);
        $collections = HeadToHead::query()
            ->where('schedule_id', $this->getScheduleModel()->id)
            ->where('status', HeadToHead::ACTIVE)
            ->with('bets')
            ->get();

        if(!$collections->count()) {
            Log::info('Service:Game ' . __FUNCTION__ . ' scId ' .  $this->getScheduleModel()->id . ' no hth bets');
            return;
        }

        foreach ($collections as $headToHeadModel) {
            $winCount = 0;
            $winUserId = 0;

            $users = [];
            $type = ($headToHeadModel->game_type === HeadToHead::GAME_TYPE_SINGLE) ? UserStat::TYPE_HTH : UserStat::TYPE_DARE;
            foreach ($headToHeadModel->bets as $bet) {
                $users[] = $bet->user_id;
                if($bet->team_id) {
                    if ($this->getScheduleModel()->getResultByTeamId($bet->team_id) == $bet->type) {
                        $winCount++;
                        $winUserId = $bet->user_id;
                    }
                    continue;
                }

                if($this->getScheduleModel()->goals_home_team == $bet->goals_home_team && $this->getScheduleModel()->goals_away_team == $bet->goals_away_team) {
                    $winUserId = $bet->user_id;
                    $winCount++;
                }
            }

            if($winCount == 1) {
                $headToHeadModel->win_user_id = $winUserId;
                UserStat::incUserStats($winUserId, $type, true);
                $loseUserId = ($users[0] === $winUserId) ? $users[1] : $users[0];
                UserStat::incUserStats($loseUserId, $type);
            } else {
                foreach ($users as $userId) {
                    UserStat::incUserStats($userId, $type);
                }
            }

            $headToHeadModel->status = HeadToHead::STATUS_FINISH;
            $headToHeadModel->save();
        }
    }
}
