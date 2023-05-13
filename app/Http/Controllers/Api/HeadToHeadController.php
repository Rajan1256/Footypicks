    <?php

    namespace App\Http\Controllers\Api;

    use App\Events\CreateNotification;
    use App\Models\HeadToHead;
    use App\Models\HeadToHeadBet;
    use App\Models\HeadToHeadInvite;
    use App\Models\NotificationModel;
    use App\Models\Schedule;
    use Illuminate\Http\Request;
    use DB;

    class HeadToHeadController extends Controller
    {
        /**
         * @SWG\Get(
         *      path="/hth/{schedule_id}",
         *      operationId="hthGetOne",
         *      tags={"headToHead"},
         *      summary="Get HeadToHead game",
         *      description="Get HeadToHead game",
         *      security={{"X-Api-Token":{}}},
         *      @SWG\Parameter(
         *         description="ID of hth game",
         *         in="path",
         *         name="schedule_id",
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

        public function show($id)
        {
            $collectionsGAME = DB::table('bets')->where('schedule_id',$id)->where('status',3)
                ->orderBy('id', 'asc')
                ->get();

            /*if(!$collectionsGAME) {
                return $this->sendJsonErrors(['Game not found'], 404);
            }*/

            if(count($collectionsGAME) > 0)
            {
                $wGAME=0;
                $lGAME=0;
                $dGAME=0;
                $totalGAME=0;

                foreach ($collectionsGAME as $rwGAME)
                {

                    if($rwGAME->type=='0')
                    {
                        $lGAME++;
                    }

                    if($rwGAME->type=='1')
                    {
                        $dGAME++;
                    }

                    if($rwGAME->type=='2')
                    {
                        $wGAME++;
                    }
                    $totalGAME++;
                }

                $collections = DB::table('head_to_heads')
                ->select('head_to_heads.*', 'head_to_head_invites.*', 'head_to_head_bets.*', 'head_to_heads.id as head_id')
                ->join('head_to_head_invites', 'head_to_heads.id', '=', 'head_to_head_invites.head_to_head_id')
                ->join('head_to_head_bets', 'head_to_heads.id', '=', 'head_to_head_bets.head_to_head_id')
                ->where('head_to_heads.status', '!=', 3)
                ->where('head_to_heads.schedule_id', $id)
                ->get();

                if(count($collections) > 0)
                {
                    $w=0;
                    $l=0;
                    $d=0;
                    $total=0;

                    foreach ($collections as $rs)
                    {
                        if($rs->type=='0')
                        {
                            $l++;
                        }

                        if($rs->type=='1')
                        {
                            $d++;
                        }

                        if($rs->type=='2')
                        {
                            $w++;
                        }
                        $total++;
                    }

                    $totalWin = $w+$wGAME;
                    $totalLoss = $l+$lGAME;
                    $totalDraw = $d+$dGAME;

                    $totalRecordCount = $total+$totalGAME;

                    $win = $totalWin/$totalRecordCount*100;
                    $loss = $totalLoss/$totalRecordCount*100;
                    $draw = $totalDraw/$totalRecordCount*100;

                    $st = array('schedule_id'=>$rs->schedule_id,'Team_id'=>$rs->team_id,'Loss'=>$loss,'Win'=>$win,'Draw'=>$draw);

                    return $this->sendJson($st);
                }
                else
                {
                    $win = $wGAME/$totalGAME*100;
                    $loss = $lGAME/$totalGAME*100;
                    $draw = $dGAME/$totalGAME*100;
                    $st = array('schedule_id'=>$rwGAME->schedule_id,'Team_id'=>$rwGAME->team_id,'Loss'=>$loss,'Win'=>$win,'Draw'=>$draw);
                    return $this->sendJson($st);
                }
            }
            else
            {
                $collections = DB::table('head_to_heads')
                ->select('head_to_heads.*', 'head_to_head_invites.*', 'head_to_head_bets.*', 'head_to_heads.id as head_id')
                ->join('head_to_head_invites', 'head_to_heads.id', '=', 'head_to_head_invites.head_to_head_id')
                ->join('head_to_head_bets', 'head_to_heads.id', '=', 'head_to_head_bets.head_to_head_id')
                ->where('head_to_heads.status', '!=', 3)
                ->where('head_to_heads.schedule_id', $id)
                ->get();

                if(count($collections) > 0)
                {
                    $w=0;
                    $l=0;
                    $d=0;
                    $total=0;

                    foreach ($collections as $rs)
                    {
                        if($rs->type=='0')
                        {
                            $l++;
                        }

                        if($rs->type=='1')
                        {
                            $d++;
                        }

                        if($rs->type=='2')
                        {
                            $w++;
                        }
                        $total++;
                    }

                    $win = $w/$total*100;
                    $loss = $l/$total*100;
                    $draw = $d/$total*100;

                    $st = array('schedule_id'=>$rs->schedule_id,'Team_id'=>$rs->team_id,'Loss'=>$loss,'Win'=>$win,'Draw'=>$draw);

                    return $this->sendJson($st);
                }
                else
                {
                    return $this->sendJsonErrors(['Game not found'], 404);
                }
            }
        }

        public function show_27_12(Request $request,$id)
        {
            $collectionsGAME = DB::table('bets')->where('schedule_id',$id)->where('status',3)
                ->orderBy('id', 'asc')
                ->get();

            if(!$collectionsGAME) {
                return $this->sendJsonErrors(['Game not found'], 404);
            }

            $wGAME=0;
            $lGAME=0;
            $dGAME=0;
            $totalGAME=0;

            foreach ($collectionsGAME as $rwGAME)
            {

                if($rwGAME->type=='0')
                {
                    $lGAME++;
                }

                if($rwGAME->type=='1')
                {
                    $dGAME++;
                }

                if($rwGAME->type=='2')
                {
                    $wGAME++;
                }
                $totalGAME++;
            }

            $collections = DB::table('head_to_heads')
                ->select('head_to_heads.*', 'head_to_head_invites.*', 'head_to_head_bets.*', 'head_to_heads.id as head_id')
                ->join('head_to_head_invites', 'head_to_heads.id', '=', 'head_to_head_invites.head_to_head_id')
                ->join('head_to_head_bets', 'head_to_heads.id', '=', 'head_to_head_bets.head_to_head_id')
                ->where('head_to_heads.status', '!=', 3)
                ->where('head_to_heads.schedule_id', $id)
                ->get();

            $w=0;
            $l=0;
            $d=0;
            $total=0;

             foreach ($collections as $rs)
             {
                if($rs->type=='0')
                {
                    $l++;
                }

                if($rs->type=='1')
                {
                    $d++;
                }

                if($rs->type=='2')
                {
                    $w++;
                }
                $total++;
             }

            $totalWin = $w+$wGAME;
            $totalLoss = $l+$lGAME;
            $totalDraw = $d+$dGAME;

            $totalRecordCount = $total+$totalGAME;

            $win = $totalWin/$totalRecordCount*100;
            $loss = $totalLoss/$totalRecordCount*100;
            $draw = $totalDraw/$totalRecordCount*100;

            $st = array('schedule_id'=>$rs->schedule_id,'Team_id'=>$rs->team_id,'Loss'=>$loss,'Win'=>$win,'Draw'=>$draw);

            return $this->sendJson($st);
        }

        public function show_OLD(Request $request,$id)
        {
            $collections = HeadToHead::query()
                ->where(function ($query) use ($request,$id){
                    $query->where('schedule_id','=', $id)->
                    where('game_type', HeadToHead::GAME_TYPE_SINGLE);
                })
                ->orWhere(function ($query) use ($request,$id){
                    $query
                        ->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
                    $query->whereHas('invite', function ($query) use ($request,$id) {
                        $query->where('schedule_id','=', $id)
                            ->where('status', HeadToHeadInvite::ACTIVE);
                    });
                })
                ->orWhere(function ($query) use ($request,$id){
                    $query->where('schedule_id', $id)
                        ->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
                    $query->whereHas('invite', function ($query) use ($request) {
                    });
                })

                ->with('schedule.league', 'bets')
                ->get();

            //return $collections;
            if(count($collections)==0)
            {
                $st = 'Scehdule not found';
            }
            else
            {
                $w=0;
                $l=0;
                $d=0;
            $total=0;

            foreach ($collections as $rs)
            {
                foreach ($rs['bets'] as $rw)
                {
                    if($rw->type=='3')
                    {
                        if($rw->goals_home_team==$rw->goals_away_team)
                        {

                            $d++;
                            $total++;
                        }

                        else if($rw->goals_home_team>$rw->goals_away_team)
                        {
                            $w++;
                            $total++;
                        }

                        else
                        {
                            $l++;
                            $total++;
                        }

                        $win = $w/$total*100;
                        $loss = $l/$total*100;
                        $draw = $d/$total*100;
                        $st = array('schedule_id'=>$collections[0]['schedule_id'],'Team_id'=>'','Loss'=>$loss,'Win'=>$win,'Draw'=>$draw);
                    }
                    else
                    {
                        if($rw->type=='0')
                        {

                            $l++;
                            $total++;
                        }

                        if($rw->type=='1')
                        {
                            $d++;
                            $total++;
                        }

                        if($rw->type=='2')
                        {
                            $w++;
                            $total++;
                        }

                        $win = $w/$total*100;
                        $loss = $l/$total*100;
                        $draw = $d/$total*100;
                        $st = array('schedule_id'=>$collections[0]['schedule_id'],'Team_id'=>$rw->team_id,'Loss'=>$loss,'Win'=>$win,'Draw'=>$draw);
                    }


                }

            }


            }

            return $this->sendJson($st);

        }

        /**
         * @SWG\Get(
         *      path="/hth",
         *      operationId="hths",
         *      tags={"headToHead"},
         *      summary="Get HeadToHead games",
         *      description="Get HeadToHead games",
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
            $collections = HeadToHead::query()
                ->where(function ($query) use ($request){
                    $query->where('user_id', $request->user()->id)
                        ->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
                })
                ->orWhere(function ($query) use ($request){
                    $query
                        ->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
                    $query->whereHas('invite', function ($query) use ($request) {
                        $query->where('user_id', $request->user()->id)
                            ->where('status', HeadToHeadInvite::ACTIVE);
                    });
                })
                ->orWhere(function ($query) use ($request){
                    $query
                        ->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
                    $query->whereHas('bet', function ($query) use ($request) {
                        $query->where('user_id', $request->user()->id);
                    });
                })
                ->where('status', '!=', HeadToHead::DELETE)
                ->with('schedule.league', 'bets')
                ->get();

            $collectionsData = [
                'active' => [],
                'invite' => [],
                'past' => [],
                'not_active' => [],
            ];

            foreach ($collections as $model) {
                switch ($model->status){
                    case HeadToHead::ACTIVE:
                    case HeadToHead::STATUS_INVITED:
                        $collectionsData['active'][] = $model->getInfoWithOpponentBet($request->user()->id);
                        break;
                    case HeadToHead::STATUS_FINISH:
                        $collectionsData['past'][] = $model->getInfoWithOpponentBet($request->user()->id);
                        break;
                    case HeadToHead::NOT_ACTIVE:
                    default:
                        $collectionsData['not_active'][] = $model->getInfo();
                        break;
                }
            }

            $invites = HeadToHeadInvite::query()
                ->where('user_id', $request->user()->id)
                ->where('status', HeadToHeadInvite::STATUS_INVITED)
                ->whereHas('headToHead', function ($query) {
                    $query->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
                })
                ->with('headToHead.schedule.league')
                ->get();

            $collectionsData['invite'] = $invites->map(function ($modelInvite) {
                return $modelInvite->getInfo();
            });
            return $this->sendJson($collectionsData);
        }

        /**
         * @SWG\Get(
         *      path="/hth/{hth_id}",
         *      operationId="hthGetOne",
         *      tags={"headToHead"},
         *      summary="Get HeadToHead game",
         *      description="Get HeadToHead game",
         *      security={{"X-Api-Token":{}}},
         *      @SWG\Parameter(
         *         description="ID of hth game",
         *         in="path",
         *         name="hth_id",
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
            $model = HeadToHead::query()
                ->where(function ($query) use ($request, $id){
                    $query->where('user_id', $request->user()->id)
                        ->where('id', $id)
                        ->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
                })
                ->orWhere(function ($query) use ($request, $id){
                    $query
                        ->where('id', $id)
                        ->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
                    $query->whereHas('invite', function ($query) use ($request) {
                        $query->where('user_id', $request->user()->id);
                    });
                })
                ->orWhere(function ($query) use ($request, $id){
                    $query
                        ->where('id', $id)
                        ->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
                    $query->whereHas('bet', function ($query) use ($request) {
                        $query->where('user_id', $request->user()->id);
                    });
                })
                ->with(['user', 'schedule.teamHome', 'schedule.teamAway', 'schedule.league', 'winUser', 'bets'])
                ->first();

            if(!$model) {
                return $this->sendJsonErrors(['Not found'], 404);
            }

            $playerInvite = HeadToHeadInvite::query()
                ->with('user')
                ->where('head_to_head_id', $model->id)
                ->first();

            if($playerInvite) {
                $players[] = array(
                    'id' => $playerInvite->user->id,
                    'name' => $playerInvite->user->name,
                    'nickname' => $playerInvite->user->nickname,
                    'is_wager' => $playerInvite->is_wager == "" ? 'false' : $playerInvite->is_wager
                );
            }

    //        $players = [$model->user->getShortInfo()];
    //        if($playerInvite) {
    //            $players[] = $playerInvite->user->getShortInfo();
    //        }


            $pl_data= HeadToHead::where('id',$id)
                ->where('game_type', HeadToHead::GAME_TYPE_SINGLE)->first();

            $players[] = array(
                'id'=>$pl_data->user_id,
                'name'=>$model->user->name,
                'nickname'=>$model->user->nickname,
                'is_wager'=>$pl_data->is_wager
            );


            if($model->user_id==$request->user()->id)
            {
                $data1 = $model->is_wager;
            }
            else
            {
                $dt = HeadToHeadInvite::where('user_id',$request->user()->id)->where('head_to_head_id',$id)->first();
                $data1 = $dt->is_wager==''?'false':$dt->is_wager;
            }


           return $this->sendJson([
               //'game' => $model->getFullInfo($request->user()->id),
               'game'=>array(

                   'id' => $model->id,
                   'win_user' => $model->win_user,
                   'win_user_id' => $model->win_user_id,
                   'wish' => $model->wish,
                   'created_at' => $model->created_at->timestamp,
                   'name' => $model->name,
                   'is_wager'=>$data1,
                   'wager_amount'=>$model->wager_amount,
                   'wager_currency'=>$model->wager_currency,
                   'is_pick' => $model->is_pick,
                   'is_finish' => $model->is_finish,
                   'user' => $model->user,
                   'schedule' => $model->schedule,
                   'bet' => $model->getUserBet($request->user()->id),
               ),
               'players' => $players,
               'opponent_bet' => $model->getOpponentBet($request->user()->id),
               'invite_id' => ($request->user()->id == $model->user_id || $playerInvite->status == HeadToHeadInvite::ACTIVE) ? 0 : $playerInvite->id
           ]);
        }

        /**
         * @SWG\Definition(
         *            definition="HeadToHeadCreate",
         * 			@SWG\Property(property="invited_user_id", type="number"),
         * 			@SWG\Property(property="schedule_id", type="number"),
         * 			@SWG\Property(property="name", type="string"),
         * 			@SWG\Property(property="type", type="string", enum={0,1,2}, description="Types : 0 - LOSE, 1 - draw, 2 - win"),
         * 			@SWG\Property(property="goals_home_team", type="number"),
         * 			@SWG\Property(property="goals_away_team", type="number"),
         *  )
         */

        /**
         * @SWG\Post(
         *      path="/hth/create",
         *      operationId="headToHead",
         *      tags={"headToHead"},
         *      summary="Create HeadToHead Game bet",
         *      description="Create HeadToHead Game bet. Bet set on home team id.",
         *      security={{"X-Api-Token":{}}},
         *     @SWG\Parameter(
         *         name="body", in="body", required=true, description="Post Data",
         *         @SWG\Schema(ref="#/definitions/HeadToHeadCreate"),
         *      ),
         *      @SWG\Response(
         *          response=200,
         *          description="successful operation"
         *       ),
         *       @SWG\Response(response=400, description="Bad request"),
         *     )
         *
         */
        public function create(Request $request)
        {
            $validator = $this->getValidationFactory()->make($request->all(), [
                'invited_user_id' => 'required|numeric|exists:users,id',
                'schedule_id' => 'required|exists:schedules,id',
                'name' => 'required|min:0|max:250|string',
                'type' => 'min:0|max:2|numeric',
                'goals_home_team' => 'required_with:goals_away_team|min:0|max:250|numeric',
                'goals_away_team' => 'required_with:goals_home_team|min:0|max:250|numeric',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }
            if($request->input('invited_user_id') == $request->user()->id) {
                return $this->sendJsonErrors(['Error. You invite self'], 400);
            }
            /** @var Schedule $scheduleModel */
            $scheduleModel = Schedule::query()->find($request->input('schedule_id'));

            // if(!$scheduleModel->isNotPlayed()) {
            //     return $this->sendJsonErrors(['Schedule played or finish'], 400);
            // }

            // if($scheduleModel->date <= time()) {
            //     return $this->sendJsonErrors(['Schedule played or finish'], 400);
            // }

            $betType = $request->input('type', HeadToHead::TYPE_SCORE);

            $headToHeadModel = new HeadToHead();
            $headToHeadModel->fill([
                'user_id' => $request->user()->id,
                'schedule_id' => $request->input('schedule_id'),
                'name' => $request->input('name'),
                'wish' => '',
                'is_pick' => ($betType == HeadToHead::TYPE_SCORE) ? HeadToHead::BET_STATUS_SCORE : HeadToHead::BET_STATUS_PICK,
                'game_type' => HeadToHead::GAME_TYPE_SINGLE,
                'status' => HeadToHead::STATUS_INVITED,
            'wager_amount'=>$request->input('wager_amount'),
            'wager_currency'=>$request->input('wager_currency'),
            'is_wager'=>$request->input('is_wager')
            ]);

            if (!$headToHeadModel->save()) {
                return $this->sendJsonErrors(['Your Bet not save. Db Error'], 500);
            }

            $headToHeadBetModel = new HeadToHeadBet();
            $headToHeadBetModel->head_to_head_id = $headToHeadModel->id;
            $headToHeadBetModel->user_id = $request->user()->id;
            if($request->has('goals_away_team')) {
                $headToHeadBetModel->goals_home_team = $request->input('goals_home_team');
                $headToHeadBetModel->goals_away_team = $request->input('goals_away_team');
            }

            $headToHeadBetModel->type = $betType;
            if($headToHeadBetModel->type != HeadToHead::TYPE_SCORE) {
                $headToHeadBetModel->team_id = $scheduleModel->team_home_id;
            }

            if (!$headToHeadBetModel->save()) {
                return $this->sendJsonErrors(['Your Bet not save. Db Error'], 500);
            }

            $invite = new HeadToHeadInvite();
            $invitedUserId = $request->input('invited_user_id');
            $invite->fill([
                'user_id' => $invitedUserId,
                'head_to_head_id' => $headToHeadModel->id,
            'is_wager'=>"false",
            ]);


            if (!$invite->save()) {
                return $this->sendJsonErrors(['Invite not save. Db Error'], 500);
            }

            $createEvent = new CreateNotification($request->user());
            $createEvent->hthNotificationCreate($headToHeadModel, $request->user()->id);
            $NotificationType = 2;
            $createEvent->hthNotificationInvite($headToHeadModel, $invitedUserId, $NotificationType);
            event($createEvent);

            return $this->sendJson([
                'game' => $headToHeadModel
            ]);
        }


        /**
         * @SWG\Definition(
         *            definition="HeadToHeadConfirm",
         * 			@SWG\Property(property="team_id", type="number", description="Team Id"),
         * 			@SWG\Property(property="type", type="string", enum={0,1,2}, description="Types : 1 - draw, 2 - win"),
         * 			@SWG\Property(property="goals_home_team", type="number"),
         * 			@SWG\Property(property="goals_away_team", type="number"),
         *  )
         */
        /**
         * @SWG\Post(
         *      path="/hth/confirm/{invite_id}",
         *      operationId="hthInvite",
         *      tags={"headToHead"},
         *      summary="Football hth game invite confirm",
         *      description="Football hth game invite confirm",
         *      security={{"X-Api-Token":{}}},
         *      @SWG\Parameter(
         *         description="Invite ID to Head To Head game",
         *         in="path",
         *         name="invite_id",
         *         required=true,
         *         type="integer",
         *         format="int64"
         *     ),
         *      @SWG\Parameter(
         *         name="body", in="body", required=true, description="Post Data",
         *         @SWG\Schema(ref="#/definitions/HeadToHeadConfirm"),
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
                'type' => 'min:0|max:2|numeric',
                'goals_home_team' => 'required_with:goals_away_team|min:0|max:250|numeric',
                'goals_away_team' => 'required_with:goals_home_team|min:0|max:250|numeric',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $model = HeadToHeadInvite::query()
                ->where('id', $id)
                ->where('user_id', $request->user()->id)
                ->where('status', HeadToHeadInvite::STATUS_INVITED)
                ->whereHas('headToHead', function ($query) {
                    $query->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
                })
                ->with('headToHead.schedule')
                ->first();

            if(!$model) {
                return $this->sendJsonErrors(['Invite not Found or confirmed'], 404);
            }

            $headToHeadBetModel = new HeadToHeadBet();
            $headToHeadBetModel->head_to_head_id = $model->headToHead->id;
            $headToHeadBetModel->user_id = $request->user()->id;
            $headToHeadBetModel->type = $request->input('type', HeadToHead::TYPE_SCORE);
            if($headToHeadBetModel->type != HeadToHead::TYPE_SCORE) {
                $headToHeadBetModel->team_id = $model->headToHead->schedule->team_home_id;
            } else {
                $headToHeadBetModel->goals_home_team = $request->input('goals_home_team');
                $headToHeadBetModel->goals_away_team = $request->input('goals_away_team');
            }

            if (!$headToHeadBetModel->save()) {
                return $this->sendJsonErrors(['Your Bet not save. Db Error'], 500);
            }

            $model->status = HeadToHeadInvite::ACTIVE;
        $model->is_wager = $request->input('is_wager');
            $model->headToHead->status = HeadToHead::ACTIVE;

            $model->headToHead->save();

            if (!$model->save()) {
                return $this->sendJsonErrors(['Game not save. Db Error'], 500);
            }

            return $this->sendJson();
        }

        /**
         * @SWG\Delete(
         *      path="/hth/{head_to_head_id}",
         *      operationId="delete_hth",
         *      tags={"headToHead"},
         *      summary="Delete Football headToHead game",
         *      description="Delete headToHead game",
         *      security={{"X-Api-Token":{}}},
         *      @SWG\Parameter(
         *         description="HeadToHead Id",
         *         in="path",
         *         name="head_to_head_id",
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
            $model = HeadToHead::query()
                ->with('invite')
                ->find($id);

            if(!$model) {
                return $this->sendJsonErrors(['Not found or canceled'], 404);
            }

            if(!$model || ($model->user_id != $request->user()->id && $model->status == HeadToHead::NOT_ACTIVE)) {
                if(isset($model->invite->user_id) && $model->invite->user_id == $request->user()->id) {
                    $model->invite->delete();
                    HeadToHeadBet::where('head_to_head_id',$id)->where('user_id',$model->user_id)->delete();
                    HeadToHeadInvite::where('head_to_head_id',$id)->where('user_id',$model->user_id)->delete();
                    return $this->sendJson();
                }

                return $this->sendJsonErrors(['Not found or canceled'], 404);
            }

            if($model->user_id != $request->user()->id) {

                HeadToHeadBet::where('head_to_head_id',$id)->where('user_id',$request->user()->id)->delete();
                HeadToHeadInvite::where('head_to_head_id',$id)->where('user_id',$request->user()->id)->delete();
                    return $this->sendJson();
                    // return $this->sendJsonErrors(['You not permissions to delete this game'], 403);

                if (!$model->save()) {
                    return $this->sendJsonErrors(['Game not save. Db Error'], 500);
                }

                return $this->sendJson();

            }

            if (!$model->delete()) {
                return $this->sendJsonErrors(['Game not delete. Db Error'], 500);
            }

            return $this->sendJson();
        }
    }
