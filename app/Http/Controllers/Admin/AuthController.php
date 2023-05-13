<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/fadmin';


    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function resetPage()
    {
        return view('admin.auth.reset');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = env('ADMIN_BASE_PATH', 'fadmin');
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function username()
    {
        return 'email';
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $user = User::query()->where('email', $request->input($this->username()))->first();
        if (!isset($user->id)) {
            return false;
        }

        if (!$user->validatePassword($request->input('password'))) {
            return false;
        }

        $this->guard()->login($user, $request->has('remember'));
        return true;
    }

    public function resetPassword(Request $request)
    {
        $validator = $this->validate($request, [
            'confpassword' => 'same:Newpassword',
        ],
        ['confpassword.same' => 'New password and confirm password must be same.']);
        $email = $request->input('email');
        $password = $request->input('Newpassword');
        $model = User::where('email', $email)->get();

        if(!$model) {
            return $this->sendJsonErrors(['Not found'], 404);
        }
        foreach ($model as $team) {
            User::query()
                ->where('email', $team->email)
                ->update(['password' => $password]);
        }
        return redirect('login')->with('message', 'Your password was changed successfully.');
    }
}
