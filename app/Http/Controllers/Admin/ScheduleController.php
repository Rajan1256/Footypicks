<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Schedule;
use App\Services\Schedule as Service;
use App\Models\Team;
use Illuminate\Http\Request;

class ScheduleController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $collection = Schedule::query()->with(['league', 'teamHome', 'teamAway'])->get();
        return view('admin.schedules.index', ['collection' => $collection]);
    }

    public function create($leagueId)
    {
        $model = new Schedule();
        $model->date = date('Y-m-d h:i');
        $teams = Team::query()->where('league_id', $leagueId)->get();
        return view('admin.schedules.create', ['model' => $model, 'teams' => $teams]);
    }

    public function postCreate(Request $request, $leagueId)
    {
        $model = factory(\App\Models\Schedule::class)->make([
            'date' => date('Y-m-d h:i'),
            'status' => Schedule::SCHEDULED
        ]);
        $teams = Team::query()->where('league_id', $leagueId)->get();
        $validator = $this->getValidationFactory()->make($request->all(), [
            'date' => 'required',
            'team_home_id' => 'required|exists:teams,id',
            'team_away_id' => 'required|exists:teams,id',
        ]);

        $model->fill($request->all());

        if($validator->fails()) {
            return view('admin.schedules.create', ['errors' => $validator->getMessageBag(), 'model' => $model, 'teams' => $teams]);
        }

        $model->league_id = $leagueId;

        if(!$model->save()) {
            return abort(500, 'DB ERROR');
        }

        return redirect(route('a:schedules'));
    }

    public function edit($id)
    {
        $model = Schedule::query()->find($id);

        if(!$model) {
            return abort(404, 'Schedule not found');
        }

        $teams = Team::query()->where('league_id', $model->league_id)->get();
        return view('admin.schedules.create', ['model' => $model, 'teams' => $teams]);
    }

    public function postEdit(Request $request, $id)
    {
        $model = Schedule::query()->find($id);

        if(!$model) {
            return abort(404, 'Schedule not found');
        }

        $teams = Team::query()->where('league_id', $model->league_id)->get();
        $validator = $this->getValidationFactory()->make($request->all(), [
            'date' => 'required',
            'team_home_id' => 'required|exists:teams,id',
            'team_away_id' => 'required|exists:teams,id',
        ]);

        if($validator->fails()) {
            return view('admin.schedules.create', ['errors' => $validator->getMessageBag(), 'model' => $model, 'teams' => $teams]);
        }

        $model->fill($request->all());

        if(!$model->save()) {
            return abort(500, 'DB ERROR');
        }

        return redirect(route('a:schedules'));
    }

    public function delete($id)
    {
        $model = Schedule::query()->find($id);

        if(!$model) {
            return abort(404, 'Schedule not found');
        }

        $model->delete();
        return redirect(route('a:schedules'));
    }

    public function finish($id)
    {
        $model = Schedule::query()
            ->where('status', Schedule::SCHEDULED)
	    //->orwhere('status',Schedule::FINISHED)
            ->with(['teamHome', 'teamAway'])
            ->find($id);

        //if(!$model) {
        //    return abort(403, 'Schedule not found or already finish');
        //}
	
	if(!$model)
	{
		return redirect('fadmin/schedules')->with('message', 'Schedule not found or already finish!');;
	}
	else
	{
	  return view('admin.schedules.finish', ['model' => $model]);
	}
        
    }

    public function postFinish(Request $request, $id)
    {
        $model = Schedule::query()
            ->where('status', Schedule::SCHEDULED)
            ->with(['teamHome', 'teamAway'])
            ->find($id);

        if(!$model) {
            return abort(403, 'Schedule not found or already finish');
        }

        $validator = $this->getValidationFactory()->make($request->all(), [
            'goals_home_team' => 'required|min:0',
            'goals_away_team' => 'required|min:0',
        ]);

        if($validator->fails()) {
            return view('admin.schedules.finish', ['errors' => $validator->getMessageBag(), 'model' => $model]);
        }

        $service = new Service();
        $service->getOne($model->id);
        $service->scheduleFinish($request->only(['goals_home_team', 'goals_away_team']));
        return redirect(route('a:schedules'));
    }
}
