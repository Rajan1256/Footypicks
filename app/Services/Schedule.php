<?php

namespace App\Services;

use App\Models\Schedule as Model;

class Schedule extends Base
{

    protected $orderBy = [];

    protected $modelName = Model::class;

    public function scheduleFinish(array $goals = [])
    {
        if(!isset($goals['goals_home_team'], $goals['goals_away_team'])) {
            return $this->addError('NotFound goals_home_team or goals_away_team');
        }

        $this->getModel()->fill([
            'goals_home_team' => $goals['goals_home_team'],
            'goals_away_team' => $goals['goals_away_team']
        ]);
        $this->getModel()->status = Model::FINISHED;
        if(!$this->getModel()->save()) {
            return $this->addError('Db error. Not save');
        }

        $gameService = new Game();
        $gameService->setScheduleModel($this->getModel());
        $gameService->updateGameResults();
        $gameService->updateHeadToHeadResults();
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return parent::getModel();
    }
}
