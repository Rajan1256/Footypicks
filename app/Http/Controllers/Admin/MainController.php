<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;

class MainController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.index');
    }

    public function users()
    {
        $collection = User::query()->get();
        // echo "<pre>";
        // print_r($collection->toArray());
        // die;
        return view('admin.users.index', ['collection' => $collection]);
    }

    public function delete(Request $request)
    {
        
        $id = $request->message;
        
        // echo $id;die;
        $model = User::query()->find($id);
        DB::delete("delete from user_follows where user_id='".$id."' and follow_id='".$id."'");
        // DB::delete("delete from bets where user_id='".$id."'");
        // DB::delete("delete from feeds where user_id='".$id."'");
        // DB::delete("delete from games where user_id='".$id."'");
        // DB::delete("delete from head_to_heads where user_id='".$id."'");
        // DB::delete("delete from head_to_head_bets where user_id='".$id."'");
        // DB::delete("delete from head_to_head_invites where user_id='".$id."'");
        // DB::delete("delete from notifications where user_id='".$id."'");
        // DB::delete("delete from oauth_access_tokens where user_id='".$id."'");
        // DB::delete("delete from post_comments where user_id='".$id."'");
        // DB::delete("delete from post_likes where user_id='".$id."'");
        // DB::delete("delete from push_tokens where user_id='".$id."'");
        // DB::delete("delete from users_in_games where user_id='".$id."'");
        
        // DB::delete("delete from user_stats where user_id='".$id."'");
        // DB::delete("delete from user__feed__counts where user_id='".$id."'");
        
        if(!$model) {
            return abort(404, 'User not found');
        }
        // User::where('id', $id)->delete();        
       $model->delete();
        // return redirect(route('a:index'));
    }
}
