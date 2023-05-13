<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;
use App\Models\League;
use App\Models\Team;
use Illuminate\Http\Request;
use DB;

class SettingController extends BaseController {

    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        $sql="select * from crontime";
        $data['data']=DB::select($sql);
        // $collection = Team::query()->with('league')->get();
        return view('admin.settings.index',$data);
    }

    public function create() {
        $model = new Team();
        $leagues = League::query()->get();
        return view('admin.teams.create', ['model' => $model, 'leagues' => $leagues]);
    }

    public function postCreate(Request $request) {
        $model = factory(\App\Models\Team::class)->make([
            'name' => '',
            'league_id' => 0,
        ]);
        $leagues = League::query()->get();
        $validator = $this->getValidationFactory()->make($request->all(), [
            'name' => 'required|string|max:32',
            'league_id' => 'required|exists:leagues,id',
        ]);

        $model->fill($request->all());

        if ($validator->fails()) {
            return view('admin.teams.create', ['errors' => $validator->getMessageBag(), 'model' => $model, 'leagues' => $leagues]);
        }


        if (!$model->save()) {
            return abort(500, 'DB ERROR');
        }

        return redirect(route('a:teams'));
    }

    public function edit($id) {
        $model = Team::query()->find($id);

        if (!$model) {
            return abort(404, 'Team not found');
        }

        $leagues = League::query()->get();
        return view('admin.teams.create', ['model' => $model, 'leagues' => $leagues]);
    }

    public function postEdit(Request $request, $id) {
        $model = Team::query()->find($id);

        if (!$model) {
            return abort(404, 'Team not found');
        }

        $leagues = League::query()->get();
        $validator = $this->getValidationFactory()->make($request->all(), [
            'name' => 'required|string|max:32',
            'league_id' => 'required|exists:leagues,id',
        ]);

        if ($validator->fails()) {
            return view('admin.teams.create', ['errors' => $validator->getMessageBag(), 'model' => $model, 'leagues' => $leagues]);
        }

        $model->fill($request->all());

        if (!$model->save()) {
            return abort(500, 'DB ERROR');
        }

        return redirect(route('a:teams'));
    }

    public function delete($id) {
        $model = Team::query()->find($id);

        if (!$model) {
            return abort(404, 'Team not found');
        }

        $model->delete();
        return redirect(route('a:teams'));
    }

}
