<?php

namespace App\Http\Controllers\Api;

use Mail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Validator;
class RegisterController extends Controller
{

    /**
     * @SWG\Definition(
     *            definition="UserAuth",
     *            required={"email", "password"},
     * 			@SWG\Property(property="email", type="string"),
     * 			@SWG\Property(property="password", type="string"),
     * 			@SWG\Property(property="cover", type="file"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(property="nickname", type="string"),
     * 			@SWG\Property(property="dt_birthday", type="string", default="2017-05-27"),
     * 			@SWG\Property(property="favorite_team", type="string"),
     * 			@SWG\Property(property="push_notification", type="boolean"),
     *        )
     */

    /**
     * @SWG\Definition(
     *            definition="UserLogin",
     *            required={"email", "password"},
     * 			@SWG\Property(property="email", type="string", default="string@string.string"),
     * 			@SWG\Property(property="password", type="string"),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/auth/registration",
     *      operationId="registration",
     *      tags={"auth"},
     *      summary="User registration",
     *      description="Register user with token",
     *   @SWG\Parameter(
     *     name="user", in="body", required=true, description="Post Data",
     *     @SWG\Schema(ref="#/definitions/UserAuth"),
     *   ),
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
    public function registration(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|max:30|min:6',
            'name' => 'required|string|min:3',
            'nickname' => 'required|string|min:3|unique:users',
            'favorite_team' => 'required',
            'dt_birthday' => 'required|date_format:"Y-m-d"',
            //'cover' => 'mimes:jpeg,bmp,png,jpg',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = User::query()->where('email', $request->input('email'))->first();
        if ($user) {
            return $this->validationError(['email' => 'Email already registered']);
        }

        $user = new User();
        $user->fill($request->only([
            'email',
            'password',
            'push_notification',
            'favorite_team',
            'dt_birthday',
            'nickname',
            'name',
	    'cover',
        ]));

        if ($request->hasFile('cover')) {
            $user->saveCoverByFile($request->file('cover'));
        }

        if (!$user->save()) {
            return $this->sendJsonErrors('User not save');
        }

        return $this->sendJson([
                'user' => $user->getFullInfo(),
                'token' => $user->createToken('auth' . $user->email)->token->id]
        );
    }

    /**
     * @SWG\Post(
     *      path="/auth/login",
     *      operationId="login",
     *      tags={"auth"},
     *      summary="User login",
     *      description="Login user by password",
     *   @SWG\Parameter(
     *     name="user", in="body", required=true, description="Post Data",
     *     @SWG\Schema(ref="#/definitions/UserLogin"),
     *   ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     */
    public function login(Request $request)
    {
        
        $validator = $this->getValidationFactory()->make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|max:30|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = User::query()->where('email', $request->input('email'))->first();
//        dd($user);
        if (!$user) {
            return $this->sendJsonErrors('User not found', 404);
        }

        if ($user->password != $request->input('password')) {
            return $this->validationError('Password is invalid', 403);
        }

        return $this->sendJson([
                'user' => $user->getFullInfo(),
                'token' => $user->createToken('auth' . $user->email)->token->id
                ]
        );
    }

    /**
     * @SWG\Definition(
     *            definition="UserReset",
     *            required={"email"},
     * 			@SWG\Property(property="email", type="string"),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/auth/reset",
     *      operationId="reset",
     *      tags={"auth"},
     *      summary="User reset password",
     *      description="User reset password by email",
     *   @SWG\Parameter(
     *     name="user", in="body", required=true, description="Post Data",
     *     @SWG\Schema(ref="#/definitions/UserReset"),
     *   ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     */
    public function reset(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'email' => 'required|email|max:255|exists:users',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = User::query()->where('email', $request->input('email'))->first();

        if (!$user) {
            return $this->sendJsonErrors('User not found', 404);
        }

        $password = str_random(8);

        $user->password = $password;

        if (!$user->save()) {
            return $this->sendJsonErrors('User not save');
        }

        Mail::send('emails.reset', ["password" => $password], function($message) use ($user) {
            $message->to($user->email)->subject('Reset password!');
            $message->from(env('MAIL_FROM_ADDRESS', 'admin@footypicks.com'), 'FootyPicks');
        });

        return $this->sendJson([]);
    }

     /**
     * @SWG\Post(
     *      path="/auth/reset",
     *      operationId="reset",
     *      tags={"auth"},
     *      summary="User reset password",
     *      description="User reset password by email",
     *   @SWG\Parameter(
     *     name="user", in="body", required=true, description="Post Data",
     *     @SWG\Schema(ref="#/definitions/UserReset"),
     *   ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     */
    public function reset_v2(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'email' => 'required|email|max:255|exists:users',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = User::query()->where('email', $request->input('email'))->first();

        if (!$user) {
            return $this->sendJsonErrors('User not found', 404);
        }

        $access_code = rand(100000, 999999);

        // $user->password = $password;

        // if (!$user->save()) {
        //     return $this->sendJsonErrors('User not save');
        // }

        Mail::send('emails.reset_v2', ["access_code" =>  $access_code], function($message) use ($user) {
            $message->to($user->email)->subject('Reset password!');
            $message->from(env('MAIL_FROM_ADDRESS', 'admin@footypicks.com'), 'FootyPicks');
        });

        return $this->sendJson([ "reset_code" => $access_code]);
    }

    public function save_key(Request $request)
    {
        
        $validator = $this->getValidationFactory()->make($request->all(), [
            'email' => 'required|email|max:255|exists:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = User::query()->where('email', $request->input('email'))->first();

        if (!$user) {
            return $this->sendJsonErrors('User not found', 404);
        }


        $user->password= $request->input('password');
        if (!$user->save()) {
            return $this->sendJsonErrors('User not save');
        }

        return $this->sendJson(["message" => "Password changed successfully"]);
    }
}
