<?php

namespace App\Http\Controllers\Api;
use App\Models\Goal;
use App\Models\League;
use App\Models\Player;
use App\Models\Schedule;
use App\Models\Team;
use App\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StandingController extends Controller
{
    /**
     * @SWG\Get(
     *      path="/league",
     *      operationId="leagues",
     *      tags={"standing"},
     *      summary="Football leagues information",
     *      description="Get all Football leagues",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     * Returns Auth User
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function leagues(Request $request)
    {
        $collections = League::query()->active()->get();

        return $this->sendJson($this->prepareCollection($collections, 'leagues'));
    }

    /**
     * @SWG\Get(
     *      path="/league/{league_id}",
     *      operationId="league",
     *      tags={"standing"},
     *      summary="Football league information",
     *      description="Get Football league",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *         description="ID of league",
     *         in="path",
     *         name="league_id",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *      @SWG\Parameter(
     *          name="with", in="query", type="array", collectionFormat="csv",
     *          @SWG\Items(type="string", enum={"teams"}),
     *          description="Select league types.",
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function oneLeague(Request $request, $id)
    {
//        $with = $request->input('with', []);
//        $query = League::query();
//
//        if(isset($with['teams']) || $with == 'teams') {
//            $query->with(['teams' => function($q) { $q->orderBy('position');}]);
//        }
//        $leagueModel = $query->find($id);
//        if(!$leagueModel) {
//            return $this->sendJsonErrors('League not found');
//        }
//
//        return $this->sendJson([
//            'league' => $leagueModel
//        ]);




    //$with = $request->input('with', []);
$query = League::query();
$leagueModel = $query->find($id);

//$group = Team::distinct()->where('league_id',$leagueModel->id)->pluck('team_group');
        $group_check1 = Team::orderBy("team_group", "asc")->select('team_group')->distinct()->where('league_id',$leagueModel->id)->where('team_group','!=',null)->count();
        if($group_check1==0)
        {
            $group_check = Team::orderBy("team_group", "asc")->select('team_group')->distinct()->where('league_id',$leagueModel->id)->get();
        }
        else
        {
            $group_check = Team::orderBy("team_group", "asc")->select('team_group')->distinct()->where('league_id',$leagueModel->id)->where('team_group','!=',null)->get();
        }

                $temp = [];
                foreach ($group_check as $row)
                {
                   // $mydata = array('team_group'=>$row->team_group);
                    $var_data =Team::orderBy("position", "asc")->where('team_group','=', $row->team_group)->where('league_id','=', $leagueModel->id)->distinct()->get();


                    $ab = array('team_group'=>$row->team_group,'team'=>$var_data);
                    //$ab = array('team'=>$var_data);
                    array_push($temp,$ab);
                }
        //return response()->json([$all_data]);

//$team1 = Team::orderBy("position", "asc")->where('league_id',$leagueModel->id)->whereIn('team_group',$group)->get();
//
//        if(isset($with['teams']) || $with == 'teams') {
//            $query->with(['teams']);
//        }
//        $leagueModel = $query->find($id);
//        if(!$leagueModel) {
//            return $this->sendJsonErrors('League not found');
//        }

        return $this->sendJson([
        'league' => $leagueModel,
        'group'=>$temp,
        //'Group'=>$group,
        //'team'=>$team1
        ]);


    }





    /**
     * @SWG\Get(
     *      path="/league/team/{team_id}",
     *      operationId="team",
     *      tags={"standing"},
     *      summary="Football team information",
     *      description="Get Football team",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *         description="ID of team",
     *         in="path",
     *         name="team_id",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *      @SWG\Parameter(
     *          name="with", in="query", type="array", collectionFormat="csv",
     *          @SWG\Items(type="string", enum={"players"}),
     *          description="Select team with.",
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function getPlayers(Request $request, $id)
    {
//        $with = $request->input('with', []);
//        $model = Team::query()->with($with)->find($id);
//        if(!$model) {
//            return $this->sendJsonErrors('Team not found');
//        }

        $model = Team::query()->find($id);

        $tm = Team::where('id',$id)->first();
        $dt = Team::where('parse_id_v2',$tm->parse_id_v2)->pluck('id');

            $pl = Player::whereIn('team_id',$dt)->get();

            $array = array(
                "team" => array(
                    'id' => $model->id,
                    'name' => $model->name,
                    'cover' => $model->cover,
                    "played_games"=>$model->played_games==null?0:$model->played_games,
                "position"=> $model->position==null?0:$model->position,
                "points"=> $model->points==null?0:$model->points,
                "wins"=>$model->wins==null?0:$model->wins,
                "draws"=>$model->draws==null?0:$model->draws,
                "losses"=> $model->losses==null?0:$model->losses,
                "team_group"=>$model->team_group==null?0:$model->team_group,
                "league_id"=>$model->league_id,
                "recentform"=>$model->recentform,
                "country"=>$model->country,
                    "players"=>$pl

                ),

            );

        return $this->sendJson($array);


//        return $this->sendJson([
//            'team'=>$model,
//        ]);
    }
	


	 public function over_schedule(Request $request,$sch_id)
    {
        $tm = Schedule::where('sch_id',$sch_id)->first();


       // $tb = Goal::where('sch_id',$sch_id)->distinct('player_id')->select('player_id')->get();

        $tb1 = DB::table('goals')
            ->select('player_id', DB::raw('count(*) as total_goal'))
            ->groupBy('player_id')
            ->where('sch_id',$sch_id)
            ->where('status','')
            ->get();
        $tb2 = DB::table('goals')
            ->select('player_id', DB::raw('count(*) as total_goal'))
            ->groupBy('player_id')
            ->where('sch_id',$sch_id)
            ->where('status','PK')
            ->get();
        $tb3 = DB::table('goals')
            ->select('player_id', DB::raw('count(*) as total_goal'))
            ->groupBy('player_id')
            ->where('sch_id',$sch_id)
            ->where('status','OG')
            ->get();

        if($tb1)
        {
            foreach ($tb1 as $upd_data)
            {

                Goal::where('sch_id',$sch_id)->where('player_id',$upd_data->player_id)
                    ->where('status','')
                ->update([
                    'gol'=>$upd_data->total_goal
                ]);
            }
        }

        if($tb2)
        {
            foreach ($tb2 as $upd_data)
            {

                Goal::where('sch_id',$sch_id)->where('player_id',$upd_data->player_id)
                    ->where('status','PK')
                    ->update([
                        'gol'=>$upd_data->total_goal
                    ]);
            }
        }

        if($tb3)
        {
            foreach ($tb3 as $upd_data)
            {

                Goal::where('sch_id',$sch_id)->where('player_id',$upd_data->player_id)
                    ->where('status','OG')
                    ->update([
                        'gol'=>$upd_data->total_goal
                    ]);
            }
        }

                $home = Team::where('id',$tm->team_home_id)->first();
                $away = Team::where('id',$tm->team_away_id)->first();



        $home1 = Team::where('parse_id_v2',$tm->team_home_id_parse)->first();
        $away2 = Team::where('parse_id_v2',$tm->team_away_id_parse)->first();
		 
		
                $home = Team::where('id',$tm->team_home_id)->first();
                $away = Team::where('id',$tm->team_away_id)->first();



        $home1 = Team::where('parse_id_v2',$tm->team_home_id_parse)->first();
        $away2 = Team::where('parse_id_v2',$tm->team_away_id_parse)->first();

        $shares = DB::table('players')
            ->join('goals', 'goals.player_id', '=', 'players.parse_id')
            ->where('goals.sch_id', '=', $sch_id)
            ->where('goals.team_id',$home->id)
	    ->select('players.name','players.jersey_number','goals.gol','goals.status')->distinct()
            ->get();

	$s1 = DB::table('players')
            ->join('goals', 'goals.player_id', '=', 'players.parse_id')
            ->where('goals.sch_id', '=', $sch_id)
            ->where('goals.team_id',$home1->id)
	    ->select('players.name','players.jersey_number','goals.gol','goals.status')->distinct()
            ->get();

        $shares2 = DB::table('players')
            ->join('goals', 'goals.player_id', '=', 'players.parse_id')
            ->where('goals.sch_id', '=', $sch_id)
            ->where('goals.team_id',$away->id)
	    ->select('players.name','players.jersey_number','goals.gol','goals.status')->distinct()
            ->get();

	$s2 = DB::table('players')
            ->join('goals', 'goals.player_id', '=', 'players.parse_id')
            ->where('goals.sch_id', '=', $sch_id)
            ->where('goals.team_id',$away2->id)
	    ->select('players.name','players.jersey_number','goals.gol','goals.status')->distinct()
            ->get();


        return $this->sendJson([
            'home_team_name'=>$home->name,
            'home_team_id'=>$tm->team_home_id,
            'home_total'=>$tm->goals_home_team,
            'home_cover'=>$home->cover,
             'home_team'=>array('player'=>count($shares)==0?$s1:$shares),
             'away_team_name'=>$away->name,
            'away_team_id'=>$tm->team_away_id,
            'away_total'=>$tm->goals_away_team,
            'away_cover'=>$away->cover,
            'away_team'=>array('player'=>count($shares2)==0?$s2:$shares2),
        ]);
	

	}
	
    /**
     * @SWG\Get(
     *      path="/league/team/{team_id}/news",
     *      operationId="team",
     *      tags={"standing"},
     *      summary="Football team news",
     *      description="Get Football team news",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *         description="ID of team",
     *         in="path",
     *         name="team_id",
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
    public function getNews(Request $request, $id)
    {
        return $this->sendJson([
            'news' => [
                [
                    'id' => 1,
                    'title' => 'Football news 1'
                ],
            ]
        ]);
    }

    /**
     * @SWG\Get(
     *      path="/league/team/{team_id}/schedule",
     *      operationId="team",
     *      tags={"standing"},
     *      summary="Football team schedule",
     *      description="Get Football team schedule",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *         description="ID of team",
     *         in="path",
     *         name="team_id",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *      @SWG\Parameter(
     *          name="status", in="query", type="array",
     *          @SWG\Items(type="string", enum={"FINISHED", "CANCELED", "TIMED", "IN_PLAY", "POSTPONED", "SCHEDULED"}),
     *          description="Set Status of schedules",
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function getSchedule(Request $request, $id)
    {
        $query = Schedule::query()->with(['teamHome', 'teamAway']);

        $statusCollection = null;
        if($request->has('status')) {
            $statusCollection = $request->input('status');
            if (!is_array($statusCollection)) {
                $statusCollection = explode(',', $statusCollection);
            }
        }

        $query->orWhere(function ($query) use ($statusCollection, $id) {
            $query->where('team_away_id', $id);
            if(isset($statusCollection[0])) {
                $query->whereIn('status', Schedule::prepareArrayStatus($statusCollection));
            }
        });

        $query->orWhere(function ($query) use ($statusCollection, $id) {
            $query->where('team_home_id', $id);
            if(isset($statusCollection[0])) {
                $query->whereIn('status', Schedule::prepareArrayStatus($statusCollection));
            }
        });

        $collections = $query->orderBy('date')->get();
        // $collections = $query->orderBy('start_game_time')->get();

        // echo "<pre>";
        // print_r($collections->toArray());
        // die;

        return $this->sendJson([
            'schedule' => $collections->map(function($model) use ($id) { return $model->getInfoForTeam($id);})
        ]);
    }

    /**
     * @SWG\Get(
     *      path="/league/{league_id}/team/{team_id}/history",
     *      operationId="team",
     *      tags={"standing"},
     *      summary="Football team history schedule",
     *      description="Get Football team history schedule",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *         description="ID of league",
     *         in="path",
     *         name="league_id",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *  @SWG\Parameter(
     *         description="ID of team",
     *         in="path",
     *         name="team_id",
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
    public function getTeamHistorySchedule(Request $request, $id, $teamId)
    {
        $query = Schedule::query()->orderBy('date', 'DESC');
        $query->orWhere(function ($query) use ($teamId, $id) {
            $query->where('team_away_id', $teamId)
                ->where('league_id', $id)
                ->where('status', Schedule::FINISHED);
        });

        $query->orWhere(function ($query) use ($teamId, $id) {
            $query->where('team_home_id', $teamId)
                ->where('league_id', $id)
                ->where('status', Schedule::FINISHED);
        });

        $collections = $query->limit(6)->orderBy('start_game_time')->get();

        return $this->sendJson([
            'team_id' => $teamId,
            'history' => $collections->map(function($model) use ($teamId) { return [
                'id' => $model->id,
                'result_type' => $model->getResultByTeamId($teamId),
                'is_home_team' => $model->isHomeTeam($teamId),
                'goals_home_team' => $model->goals_home_team,
                'goals_away_team' => $model->goals_away_team,
                'date' => date('Y-m-d', $model->date)
            ];})
        ]);
    }

    /**
     * @SWG\Get(
     *      path="/league/{league_id}/schedule",
     *      operationId="leagueSchedule",
     *      tags={"standing"},
     *      summary="Football league schedule",
     *      description="Get Football league schedule",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *         description="ID of team",
     *         in="path",
     *         name="league_id",
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
    public function getLeagueSchedule(Request $request, $id)
    {
        $leagueModel = League::query()->active()->find($id);
        if(!$leagueModel) {
            return $this->sendJsonErrors('League not found', Response::HTTP_NOT_FOUND);
        }

       if($leagueModel->current_matchday)
        {
            $query = Schedule::query()->with(['teamHome', 'teamAway'])
                ->where('league_id', $id)
                ->where('matchday', $leagueModel->current_matchday);
            // ->orWhere('schedule_stage', $leagueModel->match_stage);
            $collection = $query->orderBy('start_game_time')->get();
        }
        else
        {
            $query = Schedule::query()->with(['teamHome', 'teamAway'])
                ->where('league_id', $id)
               // ->where('matchday', $leagueModel->current_matchday);
             ->Where('schedule_stage', $leagueModel->match_stage);
            $collection = $query->orderBy('start_game_time')->get();
        }
        return $this->sendJson([
            'schedule' => $collection
        ]);
    }




     public function leaguePaginate($league_id,$section_id)
    {
        $leagueModel = League::query()->active()->find($league_id);
        if(!$leagueModel) {
            return $this->sendJsonErrors('League not found', Response::HTTP_NOT_FOUND);
        }

        if($leagueModel->current_matchday)
        {
            $query = Schedule::query()->with(['teamHome', 'teamAway'])
                ->where('league_id', $league_id)
                ->where('matchday', $leagueModel->current_matchday+$section_id);
            // ->orWhere('schedule_stage', $leagueModel->match_stage);
            $collection = $query->orderBy('start_game_time')->get();


            $match1 = Schedule::orderBy("matchday", "desc")->where('league_id',$league_id)->first();
            $match2 = Schedule::orderBy("matchday", "asc")->where('league_id',$league_id)->first();

                if($leagueModel->current_matchday+$section_id==$match1->matchday)
                {
                    $btn_msg='false';
                }
                elseif($leagueModel->current_matchday+$section_id==$match2->matchday)
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
            if($section_id==0)
            {
                $query = Schedule::query()->with(['teamHome', 'teamAway'])
                    ->where('league_id', $league_id)
                    // ->where('matchday', $leagueModel->current_matchday);
                    ->Where('schedule_stage', $leagueModel->match_stage);
                $collection = $query->orderBy('start_game_time')->get();
                $only_single = Schedule::where('league_id',$league_id) ->select('schedule_stage')->distinct()->get();
                //  $st = Schedule::where('league_id',$model->league_id)->select('schedule_stage')->distinct()->skip($section_id)->take($section_id)->first();
                $chk_sch2 = Schedule::select('date','schedule_stage')->where('league_id', $league_id)->orderBy('date', 'desc')->first();

                if($only_single->count()==1)
                {
                    $btn_msg = 'one';
                }
                else if($chk_sch2->schedule_stage==$leagueModel->match_stage)
                {
                    $btn_msg = 'false';
                }
                else
                {
                    $btn_msg = '';
                }
                //$st = Schedule::where('league_id',$league_id)->select('schedule_stage')->distinct()->skip(0)->take(0)->first();
            }
            else
            {
                $d1 = Schedule::orderBy("id", "desc")->
                where('league_id',$league_id)->first();
                
                if($leagueModel->match_stage==$d1->schedule_stage)
                {
                    $st = Schedule::where('league_id',$league_id)
                        ->where('schedule_stage','!=',null)
                        ->where('schedule_stage','!=',$leagueModel->match_stage)
                        ->select('schedule_stage')->distinct()->get();
                }
                else if($leagueModel->match_stage!=$d1->schedule_stage)
                {
                    $st = Schedule::where('league_id',$league_id)
                        ->where('schedule_stage','!=',null)
                        ->where('schedule_stage','<=',$leagueModel->match_stage)
                        ->select('schedule_stage')->distinct()->get();
                }

                $chk_sch1 = Schedule::orderBy("id", "asc")->where('league_id',$league_id)->first();
               // $chk_sch2 = Schedule::orderBy("id", "desc")->where('league_id',$league_id)->first();

                $chk_sch2 = Schedule::select('date','schedule_stage')->where('league_id', $league_id)->orderBy('date', 'desc')->first();
                if($league_id == 7)
                {
                    if($section_id == '-1')
                    {
                        $var = $st->count();
                    }
                    else {
                        $var = $st->count()+1 + $section_id;
                    }
                }
                else
                {
                    $var =$st->count() + $section_id;
                }

                $st_dt = Schedule::where('league_id',$league_id)->select('schedule_stage')->distinct()->skip($var)->take($var)->first();


                $query = Schedule::query()->with(['teamHome', 'teamAway'])
                    ->where('league_id', $league_id)
                    // ->where('matchday', $leagueModel->current_matchday);
                    ->Where('schedule_stage', $st_dt->schedule_stage);
                $collection = $query->orderBy('start_game_time')->get();

                if($chk_sch1->schedule_stage== $st_dt->schedule_stage)
                {
                    $btn_msg = 'true';
                }
                else if($chk_sch2->schedule_stage== $st_dt->schedule_stage)
                {
                    $btn_msg = 'false';
                }
                else
                {
                    $btn_msg = '';
                }
            }

        }



            return $this->sendJson([
               'schedule' =>$collection,
                'match'=>$btn_msg,
            ]);

//        else if((int)$section_id>0)
//        {
//            return $this->sendJson([
//                'schedule' => 'false'
//            ]);
//        }
//        else if((int)$section_id<0)
//        {
//            return $this->sendJson([
//                'schedule' => 'true'
//            ]);
//        }

    }



    public function getallLeagueSchedule(Request $request,$id)
    {
        $leagueModel = League::query()->active()->find($id);
        if(!$leagueModel) {
            return $this->sendJsonErrors('League not found', Response::HTTP_NOT_FOUND);
        }

        if($leagueModel->current_matchday)
        {
            $query = Schedule::query()->with(['teamHome', 'teamAway'])
                ->where('league_id', $id);
                //->where('matchday', $leagueModel->current_matchday);
            // ->orWhere('schedule_stage', $leagueModel->match_stage);
            $collection = $query->orderBy('start_game_time')->get();
        }
        else
        {
            $query = Schedule::query()->with(['teamHome', 'teamAway'])
                ->where('league_id', $id);
                // ->where('matchday', $leagueModel->current_matchday);
               // ->Where('schedule_stage', $leagueModel->match_stage);
            $collection = $query->orderBy('start_game_time')->get();
        }
        return $this->sendJson([
            'schedule' => $collection
        ]);
    }


    public function schedule_detail($id)
    {
        $ScheduleModel = Schedule::query()->active()->find($id);
        if(!$ScheduleModel) {
            return $this->sendJsonErrors('secedule not found', Response::HTTP_NOT_FOUND);
        }

            $query = Schedule::query()->with(['teamHome', 'teamAway'])
                ->where('id', $id)->get();
            //->where('matchday', $leagueModel->current_matchday);
            // ->orWhere('schedule_stage', $leagueModel->match_stage);
           // $collection = $query->orderBy('start_game_time')->get();


        return $this->sendJson([
            'schedule' => $query
        ]);
    }

    /**
     * @SWG\Definition(
     *            definition="SetScheduleResult",
     *            required={"goals_home_team", "goals_away_team"},
     * 			@SWG\Property(property="goals_home_team", type="number", minimum=0, maximum=255),
     * 			@SWG\Property(property="goals_away_team", type="number", minimum=0, maximum=255),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/league/schedule/{schedule_id}",
     *      operationId="setSchedule",
     *      tags={"standing"},
     *      summary="Set Football schedule result",
     *      description="Set Football schedule result",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *         description="ID of schedule",
     *         in="path",
     *         name="schedule_id",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *   @SWG\Parameter(
     *     name="body", in="body", required=true, description="Post Data",
     *     @SWG\Schema(ref="#/definitions/SetScheduleResult"),
     *   ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function updateSchedule(Request $request, $id)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'goals_home_team' => 'required|max:255|min:0',
            'goals_away_team' => 'required|max:255|min:0',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $service = new \App\Services\Schedule();
        $service->setWhere([
            'status' => Schedule::SCHEDULED
        ]);
        $model = $service->getOne($id);

        if(!$model) {
            return $this->sendJsonErrors('Schedule not found', 404);
        }

        $updateData = $request->only(['goals_home_team', 'goals_away_team']);
        $service->scheduleFinish($updateData);


        if ($service->isErrors()) {
            return $this->sendJsonErrors($service->getErrors());
        }

        return $this->sendJson([
            'schedule' => $service->getModel()
        ]);
    }
}
