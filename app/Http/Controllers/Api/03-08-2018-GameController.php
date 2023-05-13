<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Events\CreateNotification;
use App\Models\Bet;
use App\Models\Game;
use App\Models\NotificationModel;
use App\Models\UserInGame;
use App\Models\Schedule;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * @SWG\Get(
     *      path="/game",
     *      operationId="leagues",
     *      tags={"game"},
     *      summary="Football game information",
     *      description="Get game Football information",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function index(Request $request)
    {
        $collections = UserInGame::query()
            ->where('user_id', $request->user()->id)
            ->where('status', '!=', UserInGame::DELETE)
            ->with(['game.league', 'game.user'])
            ->get();

        $connections = [
            'active' => [],
            'invite' => [],
            'past' => [],
            'not_active' => [],
        ];

        foreach ($collections as $model) {
            switch ($model->status){
                case UserInGame::ACTIVE:
                    $connections['active'][] = $model;
                    break;
                case UserInGame::NOT_CONFIRM_STATUS:
                    $connections['invite'][] = $model;
                    break;
                case UserInGame::STATUS_FINISH:
                    $connections['past'][] = $model;
                    break;
                case UserInGame::NOT_ACTIVE:
                default:
                $connections['not_active'][] = $model;
                    break;
            }
        }
        return $this->sendJson($connections);
    }

    /**
     * @SWG\Get(
     *      path="/game/{game_id}",
     *      operationId="leagues",
     *      tags={"game"},
     *      summary="Football game information",
     *      description="Get game Football information",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *         description="ID of game",
     *         in="path",
     *         name="game_id",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function getOne(Request $request, $id)
    {
        $model = Game::query()->with(['user', 'league'])->find($id);

        if(!$model) {
            return $this->sendJsonErrors(['Not found'], 404);
        }

        $playersCollection = UserInGame::query()
            ->where('status', '!=', UserInGame::DELETE)
            ->with('user')
            ->where('game_id', $model->id)
            ->orderBy('points', 'DESC')
            ->get();

        $inviteId = 0;
        $status = UserInGame::NOT_ACTIVE;
        foreach ($playersCollection as $player) {
            if($player->user_id == $request->user()->id) {
                $status = $player->status;
                $inviteId = $player->id;
            }
        }
        return $this->sendJson([
            'game' => $model,
            'players' => $playersCollection,
            'invite_id' => $inviteId,
            'status' => $status
        ]);
    }

    /**
     * @SWG\Delete(
     *      path="/game/{game_id}",
     *      operationId="deleteGame",
     *      tags={"game"},
     *      summary="Delete Football game",
     *      description="Delete game Football",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *         description="ID of game",
     *         in="path",
     *         name="game_id",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *       @SWG\Response(response=403, description="You not permissions to delete this game"),
     *       @SWG\Response(response=404, description="Not found"),
     *       @SWG\Response(response=500, description="Game not delete. Db Error"),
     *     )
     *
     */
    public function delete(Request $request, $id)
    {
        $model = Game::query()->with('connect')->find($id);

        if(!$model) {
            return $this->sendJsonErrors(['Not found'], 404);
        }

        if($model->user_id != $request->user()->id) {
            $player = $model->connect->filter(function ($connect) use ($request, $model) {
                return ($connect->user_id == $request->user()->id) && ($model->user_id != $request->user()->id);
            })->first();

            if(!isset($player->id)) {
                return $this->sendJsonErrors(['You not permissions to delete this game'], 403);
            }

            $player->delete();
            $count = UserInGame::query()
                ->where('status', '!=', UserInGame::DELETE)
                ->where('game_id', $model->id)
                ->count('id');
            if($count <= 1) {
                $model->status = Game::NOT_ACTIVE;

                if (!$model->save()) {
                    return $this->sendJsonErrors(['Game not save. Db Error'], 500);
                }

                UserInGame::query()
                    ->where('game_id', $model->id)
                    ->where('status', Game::ACTIVE)
                    ->update(['status' => UserInGame::NOT_ACTIVE]);
            }

            return $this->sendJson();
        }

        if (!$model->delete()) {
            return $this->sendJsonErrors(['Game not delete. Db Error'], 500);
        }

        return $this->sendJson();
    }

    /**
     * @SWG\Get(
     *      path="/game/invite/{invite_id}",
     *      operationId="gameInvite",
     *      tags={"game"},
     *      summary="Football game invite confirm",
     *      description="Football game invite confirm",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *         description="ID of invite to game",
     *         in="path",
     *         name="invite_id",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *     @SWG\Parameter(
     *          name="confirm", in="query", type="boolean",
     *          description="Is confirm",
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function confirm(Request $request, $id)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'confirm' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $model = UserInGame::query()->where('user_id', $request->user()->id)
            ->where('status', UserInGame::NOT_CONFIRM_STATUS)
            ->find($id);

        if(!$model) {
            return $this->sendJsonErrors(['Invite not Found or confirmed'], 404);
        }

        $model->status = ($request->input('confirm') != 'false') ? UserInGame::ACTIVE : UserInGame::DELETE;

        if (!$model->save()) {
            return $this->sendJsonErrors(['Game not save. Db Error'], 500);
        }

        if($model->status == UserInGame::DELETE) {
            $count = UserInGame::query()
                ->where('status', '!=', UserInGame::DELETE)
                ->where('game_id', $model->game_id)
                ->count('id');
            if ($count <= 1) {
                Game::query()->where('id', $model->game_id)->update(['status' => Game::NOT_ACTIVE]);
                UserInGame::query()
                    ->where('game_id', $model->game_id)
                    ->where('status', Game::ACTIVE)
                    ->update(['status' => UserInGame::NOT_ACTIVE]);
            }
        }

        return $this->sendJson();
    }

    /**
     * @SWG\Definition(
     *            definition="gameCreate",
     *            required={"name", "league_id", "users_ids"},
     * 			@SWG\Property(property="name", type="string"),
     * 			@SWG\Property(property="league_id", type="number"),
     * 			@SWG\Property(property="users_ids", type="array",
     *              @SWG\Items(type="number"),
     *          ),
     * )
     * @SWG\Post(
     *      path="/game",
     *      operationId="game_create",
     *      tags={"game"},
     *      summary="Create new game",
     *      description="Create new game",
     *      security={{"X-Api-Token":{}}},
     *   @SWG\Parameter(
     *     name="request", in="body", required=true, description="Post Data",
     *     @SWG\Schema(ref="#/definitions/gameCreate"),
     *   ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     */

    public function create(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'name' => 'required|string|min:3',
            'league_id' => 'required|exists:leagues,id',
            'users_ids' => 'required|array|min:1|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $userIds = $request->input('users_ids', []);
        if (in_array($request->user()->id, $userIds, true)) {
            return $this->sendJsonErrors(['Error. You invite self'], 400);
        }

        $gameService = new \App\Services\Game();

        try{
            $model = $gameService->createGame($request->user()->id, $request->input('league_id'), $request->input('name'), $userIds);
        }catch(Exception $e){
            return $this->sendJsonErrors(['Error. You invite self'], 400);
        }

        $createEvent = new CreateNotification();
        $createEvent->gameNotification($model, $request->user()->id,NotificationModel::TYPE_CREATE_GAME);
        foreach ($userIds as $userId) {
            $createEvent->gameNotificationInvite($model, $userId);
        }
        event($createEvent);
        return $this->sendJson([
            'game' => $model
        ]);
    }

    /**
     * @SWG\Get(
     *      path="/game/{game_id}/schedule",
     *      operationId="userGameSchedule",
     *      tags={"game"},
     *      summary="Get User Game schedule",
     *      description="Get User Game schedule",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *         description="ID of team",
     *         in="path",
     *         name="game_id",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function getUserGameSchedule(Request $request, $id)
    {
        /** @var Game $model */
        $model = Game::query()->with('league')->find($id);

        if(!$model) {
            return $this->sendJsonErrors(['Game not found'], 404);
        }

        $collections = Schedule::query()
            ->where('league_id', $model->league_id)
            ->where('matchday', $model->league->current_matchday)
            ->with([
                'teamHome',
                'teamAway',
                'bet' => function($query) use ($request, $model) {
                    $query
                        ->where('game_id', $model->id)
                        ->where('user_id', $request->user()->id);
                }
            ])
            ->orderBy('start_game_time')
            ->get();

        return $this->sendJson([
            'schedule' => $collections->map(function($scModel) {
                return $scModel->getInfoWithResult();
            })
        ]);
    }

    /**
     * @SWG\Definition(
     *            definition="BetCreate",
     * 			@SWG\Property(property="game_id", type="number"),
     * 			@SWG\Property(property="schedule_id", type="number"),
     * 			@SWG\Property(property="team_id", type="number", description="Team Id"),
     * 			@SWG\Property(property="type", type="string", enum={0,1,2}, description="Types : 0 - lose, 1 - draw, 2 - win"),
     *  )
     */

    /**
     * @SWG\Post(
     *      path="/game/bet",
     *      operationId="gameBet",
     *      tags={"game"},
     *      summary="Create Game bet",
     *      description="Create Game bet",
     *      security={{"X-Api-Token":{}}},
     *     @SWG\Parameter(
     *         name="user", in="body", required=true, description="Post Data",
     *         @SWG\Schema(ref="#/definitions/BetCreate"),
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function createBet(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'game_id' => 'required|exists:games,id',
            'schedule_id' => 'required|exists:schedules,id',
            'team_id' => 'required_if:type,0,2|exists:teams,id',
            'type' => 'required|min:0|max:2|numeric'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $existGameBet = Bet::query()
            ->where('game_id', $request->input('game_id'))
            ->where('user_id', $request->input($request->user()->id))
            ->where('schedule_id', $request->input('schedule_id'));

        if ($existGameBet->count()) {
            return $this->sendJsonErrors(['You already bet on this schedule'], 403);
        }

        $args = $request->only([
            'game_id',
            'schedule_id',
            'team_id',
            'type'
        ]);
        /** @var Schedule $scheduleModel */
        $scheduleModel = Schedule::query()->find($args['schedule_id']);

        if($scheduleModel->team_home_id != $args['team_id'] && $scheduleModel->team_away_id != $args['team_id']) {
            return $this->sendJsonErrors(['Team id is invalid'], 400);
        }

        if($scheduleModel->date <= time()) {
            return $this->sendJsonErrors(['Schedule played or finish'], 400);
        }

        if(!$scheduleModel->isNotPlayed()) {
            return $this->sendJsonErrors(['Schedule played or finish'], 400);
        }

        $args['user_id'] = $request->user()->id;

        $model = new Bet();
        $model->fill($args);

        if (!$model->save()) {
            return $this->sendJsonErrors(['Your Bet not save. Db Error'], 500);
        }
        return $this->sendJson();
    }
}
