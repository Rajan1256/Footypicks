<?php

namespace App\Http\Controllers\Api;

use App\Models\League;
use Exception;
use App\Events\CreateNotification;
use App\Models\Bet;
use App\Models\Game;
use App\Models\NotificationModel;
use App\Models\UserInGame;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Models\User;
use DB;
use App\Models\HeadToHead;
use App\Models\HeadToHeadBet;
use App\Models\HeadToHeadInvite;

class GameController extends Controller
{
    /**
     * @SWG\Delete(
     *      path="/game/{schedule_id}",
     *      operationId="getid",
     *      tags={"game"},
     *      summary="show predection of my league in Football game",
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

    public function show_OLD($id)
    {
        //echo $request->user()->id;
        /** @var Game $model */

        // $check_data = DB::table('bets')->where('schedule_id',$id)->where('status',3)->where('user_id',$request->user()->id)->count();


//         $check_data=DB::table('bets')
//            ->select('bets.game_id','bets.user_id','bets.schedule_id','bets.team_id','bets.type','bets.status')
//            ->join('games','games.id','=','bets.game_id')
//            ->where(['bets.schedule_id'=>$id,'bets.status'=>3,'bets.user_id'=>$request->user()->id])
//            ->count();

        // print_r($check_data);
        $collections = DB::table('bets')->where('schedule_id',$id)->where('status',3)
            ->orderBy('id', 'asc')
            ->get();

        if(!$collections) {
            return $this->sendJsonErrors(['Game not found'], 404);
        }

        $w=0;
        $l=0;
        $d=0;
        $total=0;

        foreach ($collections as $rw)
        {

            if($rw->type=='0')
            {
                $l++;
            }

            if($rw->type=='1')
            {
                $d++;
            }

            if($rw->type=='2')
            {
                $w++;
            }
            $total++;
        }
        $win = $w/$total*100;
        $loss = $l/$total*100;
        $draw = $d/$total*100;
        $st = array('schedule_id'=>$rw->schedule_id,'Team_id'=>$rw->team_id,'Loss'=>$loss,'Win'=>$win,'Draw'=>$draw);
        return $this->sendJson($st);
    }

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



    public function Round_points(Request $request,$league_id,$matchday='',$matchstage='')
    {

        $leagueModel = League::query()->active()->find($league_id);
        if(!$leagueModel) {
            return $this->sendJsonErrors('League not found', Response::HTTP_NOT_FOUND);
        }

        if($matchday!=0)
        {
            $query = Schedule::query()
                ->where('league_id', $league_id)
                ->where('matchday', $matchday)->first();

            $lg_name = League::where('id',$league_id)->first();

            $connections = DB::table('schedules')
                ->join('bets', 'bets.schedule_id', '=', 'schedules.id')
                ->join('users_in_games', 'users_in_games.game_id', '=', 'bets.game_id')
                ->where('schedules.matchday', '=', $query->matchday)
                ->select(['schedules.matchday', 'users_in_games.user_id', 'users_in_games.points'])
                ->where('users_in_games.user_id', '=', $request->user()->id)->distinct()->sum('points');

            // ->where('users_in_games.user_id','=',$request->user()->id)->distinct()->sum('points');


            $ary = array('points'=>(int)$connections,'name'=>$request->user()->name,'id'=>$request->user()->id);

            return $this->sendJson([
                'schedule' => array('Round'=>$query->matchday,'user_point'=>$ary,"season"=>$leagueModel->season,'Leage_name'=>$lg_name->name,'schedule_stage'=>'')
            ]);
        }
        else
        {

            $query = Schedule::query()
                ->where('league_id', $league_id)
                ->where('schedule_stage',$matchstage)->first();

            $lg_name = League::where('id',$league_id)->first();

            $connections = DB::table('schedules')
                ->join('bets', 'bets.schedule_id', '=', 'schedules.id')
                ->join('users_in_games', 'users_in_games.game_id', '=', 'bets.game_id')
                ->where('schedules.schedule_stage', '=', $query->schedule_stage)
                ->select(['schedules.schedule_stage', 'users_in_games.user_id', 'users_in_games.points'])
                ->where('users_in_games.user_id', '=', $request->user()->id)->distinct()->sum('points');

            // ->where('users_in_games.user_id','=',$request->user()->id)->distinct()->sum('points');
            $ary = array('points'=>(int)$connections,'name'=>$request->user()->name,'id'=>$request->user()->id);

            return $this->sendJson([
                'schedule' => array('Round'=>0,'user_point'=>$ary,"season"=>$leagueModel->season,'Leage_name'=>$lg_name->name,'schedule_stage'=>$query->schedule_stage)
            ]);

        }


//        return $this->sendJson($connections);
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

        $game_ava =  Bet::where('game_id',$id)->first();


        if(isset($game_ava))
        {
            $dt =  Bet::where('game_id',$id)->where('user_id',$request->user()->id)->first();

            $dt_id =  Bet::where('game_id',$id)->first();

            $gm_create =  User::where('id',$dt_id->user_id)->first();

            $bet_count =  UserInGame::where('game_id',$id)->where('status',1)->count();

            $dt_count =  Bet::where('game_id',$id)->where('user_id',$request->user()->id)->count();


            // $st = UserInGame::where('game_id',$id)->first();

            if($dt_count>0)
            {
                $st = array('is_wager'=>$dt->is_wager==''?'false':$dt->is_wager,'is_opp_wager'=>$dt_id->is_wager==''?'false':$dt_id->is_wager);
            }

            else
            {
                $st = array('is_wager'=>'false','is_opp_wager'=>$dt_id->is_wager==''?'false':$dt_id->is_wager);
            }

            $sc_data = Schedule::where('id',$dt_id->schedule_id)->first();

            $lg_data = League::where('id',$sc_data->league_id)->first();

            $match_over = Schedule::orderBy("id", "desc")->where('league_id',$sc_data->league_id)->first();

            if($sc_data->matchday==$lg_data->current_matchday)
            {
                $playersCollection = UserInGame::query()
                    ->where('status', '!=', UserInGame::DELETE)
                    ->with('user')
                    ->where('game_id', $model->id)
                    ->orderBy('points', 'DESC')
                    ->get();
                $array=[];
                $pointsdata=[];
                foreach ($playersCollection as $dt) {
                    $userid=$dt['user']->id;
                    $point=$dt->points;
                    $data=array(
                        'point'=>$point,
                        'userid'=>$userid);
                    array_push($array, $data);
                    array_push($pointsdata, $point);
                }
                $newdata=max($array);
                $numbers = $pointsdata;
                rsort($numbers);
                // echo 'Highest is -'.$numbers[0].', Second highest is -'.$numbers[1];
                // echo "<br>";
                $userid=0;
                if($numbers[0] == $numbers[1])
                {
                    $userid=0;
                }
                else{
                    $userid=$newdata['userid'];
                }

                $sc = array(
                    'isLeagueOver'=> $match_over->status=='FINISHED'?'true':'false',
                    'is_round_over'=>'false',
                    'current_logged_id'=>$request->user()->id,
                    'win_user_id'=>$userid,
                    'game_creator_name'=>$gm_create->name,
                    'amount'=>$dt_id->wager_amount==0?0:$dt_id->wager_amount,
                    'wager_currency'=>$dt_id->wager_currency==''?'':$dt_id->wager_currency,
                    'is_win'=>""
                );
            }
            else
            {
                if(!empty($sc_data->schedule_stage) && !empty($lg_data->match_stage) )
                {
                    if($sc_data->schedule_stage==$lg_data->match_stage)
                    {
                        $playersCollection = UserInGame::query()
                            ->where('status', '!=', UserInGame::DELETE)
                            ->with('user')
                            ->where('game_id', $model->id)
                            ->orderBy('points', 'DESC')
                            ->get();
                        $array=[];
                        $pointsdata=[];
                        foreach ($playersCollection as $dt) {
                            $userid=$dt['user']->id;
                            $point=$dt->points;
                            $data=array(
                                'point'=>$point,
                                'userid'=>$userid);
                            array_push($array, $data);
                            array_push($pointsdata, $point);
                        }
                        $newdata=max($array);
                        $numbers = $pointsdata;
                        rsort($numbers);
                        // echo 'Highest is -'.$numbers[0].', Second highest is -'.$numbers[1];
                        // echo "<br>";
                        $userid=0;
                        if($numbers[0] == $numbers[1])
                        {
                            $userid=0;
                        }
                        else{
                            $userid=$newdata['userid'];
                        }
                        $sc = array(
                            'isLeagueOver'=> $match_over->status=='FINISHED'?'true':'false',
                            'is_round_over'=>'false',
                            'current_logged_id'=>$request->user()->id,
                            'game_creator_name'=>$gm_create->name,
                            'amount'=>$dt_id->wager_amount==0?0:$dt_id->wager_amount,
                            'wager_currency'=>$dt_id->wager_currency==''?'':$dt_id->wager_currency,
                            'win_user_id'=>$userid,
                            'is_win'=>""
                        );
                    }
                }
                else
                {

                    //$rt_check = UserInGame::where('game_id',$model->id)->get();
                    $rt = UserInGame::where('game_id',$model->id)->max('points');

                    if($rt==0)
                    {
                        UserInGame::query()
                            ->where('game_id', $model->id)
                            ->where('points',$rt)
                            ->update(['is_win' => 'false']);
                    }
                    else
                    {
                        UserInGame::query()
                            ->where('game_id', $model->id)
                            ->where('points',$rt)
                            ->update(['is_win' => 'true']);

                        UserInGame::query()
                            ->where('game_id', $model->id)
                            ->where('points','!=',$rt)
                            ->update(['is_win' => 'false']);
                    }



                    $rt_result = UserInGame::where('game_id',$model->id)->where('user_id',$request->user()->id)->first();

                    $rt_cnt = UserInGame::where('game_id',$model->id)->where('is_win','true')->count();


                    if($rt_cnt==0)
                    {
                        $w_id = 0;
                        $w_u='';
                        $i_win='';
                    }
                    else
                    {
                        $rt_win = UserInGame::where('game_id',$model->id)->where('is_win','true')->first();
                        $win_user = User::where('id',$rt_win->user_id)->first();
                        $w_id = $rt_win->user_id;
                        $w_u = $win_user->name;
                        $i_win = $rt_result->is_win;
                    }



                    $rt_last = UserInGame::where('game_id',$model->id)
                        ->where('user_id',$request->user()->id)
                        ->where('status',1)->count();

                    if($rt_last==1)
                    {
                        $r_accept='true';
                    }
                    else
                    {
                        $r_accept='false';
                    }

                    $sc = array(
                        'isLeagueOver'=> $match_over->status=='FINISHED'?'true':'false',
                        'is_round_over'=>'true',
                        'current_logged_id'=>$request->user()->id,
                        'current_user_accept'=>$r_accept,
                        'round_over_wager'=>$dt_id->is_wager==''?'false':$dt_id->is_wager,
                        'win_user_id'=>$w_id,
                        'win_user_name'=>$w_u,
                        'game_creator_name'=>$gm_create->name,
                        'is_win'=>$i_win,
                        'amount'=>$dt_id->wager_amount==0?0:$dt_id->wager_amount,
                        'wager_currency'=>$dt_id->wager_currency==''?'':$dt_id->wager_currency,
                        'total_user'=>$bet_count,
                    );
                }

            }
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
                'user_status'=>$st,
                'round'=>$sc,
                'game' => $model,
                'players' => $playersCollection,
                'invite_id' => $inviteId,
                'status' => $status
            ]);

        }
        else
        {

            $playersCollection = UserInGame::query()
                ->where('status', '!=', UserInGame::DELETE)
                ->with('user')
                ->where('game_id', $model->id)
                ->orderBy('points', 'DESC')
                ->get();
//            $array=[];
//            $pointsdata=[];
//            foreach ($playersCollection as $dt) {
//                $userid=$dt['user']->id;
//                $point=$dt->points;
//                $data=array(
//                    'point'=>$point,
//                    'userid'=>$userid);
//                array_push($array, $data);
//                array_push($pointsdata, $point);
//            }
//            $newdata=max($array);
//            $numbers = $pointsdata;
//            rsort($numbers);
//            // echo 'Highest is -'.$numbers[0].', Second highest is -'.$numbers[1];
//            // echo "<br>";
//            $userid=0;
//            if($numbers[0] == $numbers[1])
//            {
//                $userid=0;
//            }
//            else{
//                $userid=$newdata['userid'];
//            }
            $sc = array(
                'isLeagueOver'=> 'false',
                'is_round_over'=>'false',
                'current_logged_id'=>'',
                'current_user_accept'=>'',
                'round_over_wager'=>'',
                'win_user_id'=>'',
                'win_user_name'=>'',
                'game_creator_name'=>'',
                'is_win'=>'',
                'amount'=>'',
                'wager_currency'=>'',
                'total_user'=>'',
            );

            $st = array('is_wager'=>'','is_opp_wager'=>'');

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
                'user_status'=>$st,
                'round'=>$sc,
                'game' => $model,
                'players' => $playersCollection,
                'invite_id' => $inviteId,
                'status' => $status
            ]);

        }


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




    public function getUserGameSchedule(Request $request, $id,$section_id)
    {
        /** @var Game $model */
        $model = Game::query()->with('league')->find($id);

        if(!$model) {
            return $this->sendJsonErrors(['Game not found'], 404);
        }


//        $collections = Schedule::query()
//            ->where('league_id', $model->league_id)
//            ->where('matchday', $model->league->current_matchday)
//            ->with([
//                'teamHome',
//                'teamAway',
//                'bet' => function($query) use ($request, $model) {
//                    $query
//                        ->where('game_id', $model->id)
//                        ->where('user_id', $request->user()->id);
//                }
//            ])
//            ->orderBy('start_game_time')
//            ->get();


        if($model->league->current_matchday)
        {
            $collections = Schedule::query()
                ->where('league_id', $model->league_id)
                ->where('matchday', $model->league->current_matchday+$section_id)
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

            $match1 = Schedule::orderBy("matchday", "desc")->where('matchday',$model->league->current_matchday)->where('league_id',$model->league_id)->first();
            $match2 = Schedule::orderBy("matchday", "asc")->where('league_id',$model->league_id)->first();

            if($model->league->current_matchday+$section_id==$match1->matchday)
            {
                $btn_msg='false';
            }
            elseif($model->league->current_matchday+$section_id==$match2->matchday)
            {
                $btn_msg='true';
            }
            else
            {
                $btn_msg='';
            }
        }
        else
        {

            if($section_id==0) {
                $collections = Schedule::query()
                    ->where('league_id', $model->league_id)
                    ->where('schedule_stage', $model->league->match_stage)
                    ->with([
                        'teamHome',
                        'teamAway',
                        'bet' => function ($query) use ($request, $model) {
                            $query
                                ->where('game_id', $model->id)
                                ->where('user_id', $request->user()->id);
                        },

                    ])
                    ->orderBy('start_game_time')
                    ->get();
                $only_single = Schedule::where('league_id',$model->league_id) ->select('schedule_stage')->distinct()->get();
                //  $st = Schedule::where('league_id',$model->league_id)->select('schedule_stage')->distinct()->skip($section_id)->take($section_id)->first();
                if($only_single->count()==1)
                {
                    $btn_msg = 'one';
                }
                else
                {
                    $btn_msg = 'false';
                }
            }

            else
            {

                $d1 = Schedule::orderBy("id", "desc")->
                where('league_id',$model->league_id)->first();

                if($model->league->match_stage==$d1->schedule_stage)
                {
                    $st = Schedule::where('league_id',$model->league_id)
                        ->where('schedule_stage','!=',null)
                        ->where('schedule_stage','!=',$model->league->match_stage)
                        ->select('schedule_stage')->distinct()->get();
                }
                else if($model->league->match_stage!=$d1->schedule_stage)
                {
                    $st = Schedule::where('league_id',$model->league_id)
                        ->where('schedule_stage','!=',null)
                        ->where('schedule_stage','<=',$model->league->match_stage)
                        ->select('schedule_stage')->distinct()->get();
                }


                $chk_sch = Schedule::orderBy("id", "asc")->where('league_id',$model->league_id)->first();

                $var =$st->count()+ $section_id;

                $st_dt = Schedule::where('league_id',$model->league_id)->select('schedule_stage')->where('schedule_stage','!=',null)->distinct()->skip($var)->first();

                $collections = Schedule::query()
                    ->where('league_id', $model->league_id)
                    ->where('schedule_stage', $st_dt->schedule_stage)
                    ->with([
                        'teamHome',
                        'teamAway',
                        'bet' => function ($query) use ($request, $model) {
                            $query
                                ->where('game_id', $model->id)
                                ->where('user_id', $request->user()->id);
                        },

                    ])
                    ->orderBy('start_game_time')
                    ->get();

                if($chk_sch->schedule_stage== $st_dt->schedule_stage)
                {
                    $btn_msg = 'true';
                }
                else
                {
                    $btn_msg = '';
                }

            }
        }

        return $this->sendJson([
            'schedule' => $collections->map(function($scModel) {
                return $scModel->getInfoWithResult();
            }),

            'match'=>$btn_msg,
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
            'type',
            'is_wager',
            'wager_amount',
            'wager_currency'
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
