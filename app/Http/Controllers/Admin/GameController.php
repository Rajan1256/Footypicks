<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Game;

class GameController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $collection = Game::query()->with(['user', 'league'])->get();
        return view('admin.games.index', ['collection' => $collection]);
    }

    public function delete(\Request $request, $id) {
        $model = Game::query()->with('connect')->find($id);

        if(!$model) {
            return redirect(route('a:games'));
        }

        $players = $model->connect;
        foreach($players as $player) {
            $player->delete();
        }

        if (!$model->delete()) {
            return redirect(route('a:games'));
        }

        return redirect(route('a:games'));
    }
}
