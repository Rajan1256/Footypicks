<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;
use App\Models\League;
use Illuminate\Http\Request;

class LeagueController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $collection = League::query()->get();
        return view('admin.leagues.index', ['collection' => $collection]);
    }

    public function create()
    {
        return view('admin.leagues.create');
    }


    public function postCreate(Request $request)
    {
        $this->getValidationFactory()->make($request->all(), [
            'caption' => 'required|string|max:32|unique:leagues',
            'name' => 'required|string|max:10|unique:leagues',
        ])->validate();

        $league = factory(\App\Models\League::class)->make([
            'caption' => $request->input('caption'),
            'name' => $request->input('name'),
            'status' => League::ACTIVE,
        ]);

        $league->save();

        return redirect(route('a:leagues'));
    }

    public function edit($id)
    {
        $model = League::query()->find($id);
        return view('admin.leagues.edit', ['model' => $model]);
    }

    public function postEdit(Request $request, $id)
    {
        $model = League::query()->find($id);

        if(!$model) {
            return abort(404, 'League not found');
        }

        $validator = $this->getValidationFactory()->make($request->all(), [
            'caption' => 'required|string|max:32',
            'name' => 'required|string|max:10',
        ]);

        if($validator->fails()) {
            return view('admin.leagues.edit', ['errors' => $validator->getMessageBag(), 'model' => $model]);
        }

        $model->fill($request->all());

        if(!$model->save()) {
            return abort(500, 'DB ERROR');
        }

        return redirect(route('a:leagues'));
    }

    public function delete($id)
    {
        $model = League::query()->find($id);

        if(!$model) {
            return abort(404, 'League not found');
        }

        $model->delete();
        return redirect(route('a:leagues'));
    }
}
