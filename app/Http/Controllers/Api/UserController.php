<?php

namespace App\Http\Controllers\Api;

use App\Models\HeadToHead;
use App\Models\HeadToHeadInvite;
use App\Models\NotificationModel;
use App\Models\PushToken;
use App\Models\UserStat;
use App\Models\User;
use App\Post_count;
use App\User_Feed_Count;
use App\Models\UserInGame;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * @SWG\Get(
     *      path="/user",
     *      operationId="getUserInfo",
     *      tags={"user"},
     *      summary="User information",
     *      description="Get user",
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
    public function index(Request $request)
    {
        return $this->sendJson([
            'user' => $request->user()->getFullInfo()
        ]);
    }

    /**
     * @SWG\Get(
     *      path="/user/{id}",
     *      operationId="getUser",
     *      tags={"user"},
     *      summary="User information",
     *      description="Get user information",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *         description="User ID",
     *         in="path",
     *         name="id",
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
        if($id == $request->user()->id) {
            $request->user()->load('userStat');
            return $this->sendJson([
                'user' => $request->user()->getInfoWithoutCheck()
            ]);
        }
        $model = User::query()->find($id);

        if(!$model) {
            return $this->sendJsonErrors(['Not found'], 404);
        }
        if($model->show_profile) {
            $model->load('userStat');
        }
        return $this->sendJson([
            'user' => $model->getInfoWithCheck()
        ]);
    }

    /**
     * @SWG\Definition(
     *            definition="UserUpdate",
     * 			@SWG\Property(property="name", type="string"),
     * 			@SWG\Property(property="nickname", type="string"),
     * 			@SWG\Property(property="dt_birthday", type="string"),
     * 			@SWG\Property(property="favorite_team", type="string"),
     * 			@SWG\Property(property="push_notification", type="boolean"),
     * 			@SWG\Property(property="show_profile", type="boolean"),
     * 			@SWG\Property(property="cover", type="file"),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/user",
     *      operationId="updateUserInfo",
     *      tags={"user"},
     *      summary="Update User information",
     *      description="Update user",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *          name="userUpdate", in="body", required=true, description="User Post Data",
     *          @SWG\Schema(ref="#/definitions/UserUpdate"),
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function update(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'name' => 'string|string|max:25|min:3',
            'nickname' => 'string|min:3|unique:users',
            'cover' => 'file|mimes:jpeg,bmp,png,jpg',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        /** @var User $user */
        $user = $request->user();
        if ($request->hasFile('cover')) {
            $user->saveCoverByFile($request->file('cover'));
        }

        if ($request->input('name')) {
            $user->name = $request->input('name');
        }

        if ($request->input('dt_birthday')) {
            $user->dt_birthday = $request->input('dt_birthday');
        }

        if ($request->input('favorite_team')) {
            $user->favorite_team = $request->input('favorite_team');
        }

        if ($request->has('push_notification')) {
            $user->push_notification = (bool)$request->input('push_notification');
        }


        if ($request->has('show_profile')) {
            $user->show_profile = (bool)$request->input('show_profile');
        }

        if ($request->input('nickname')) {
            $user->nickname = $request->input('nickname');
        }

        if (!$user->save()) {
            return $this->sendJsonErrors('Account not save. DB error');
        }

        return $this->sendJson([
            'user' => $user->getFullInfo()
        ]);
    }



    /**
     * @SWG\Definition(
     *            definition="pushTokenData",
     * 			@SWG\Property(property="push_token", type="string"),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/user/push_token",
     *      operationId="updatePushToken",
     *      tags={"user"},
     *      summary="Update User Push Token",
     *      description="Update Push Token",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *          name="body", in="body", required=true, description="User Post Data",
     *          @SWG\Schema(ref="#/definitions/pushTokenData"),
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function pushToken(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'push_token' => 'string|string|max:255|min:3',
            'deviceType' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $data = [
            'user_id' => $request->user()->id,
            'token' => $request->input('push_token'),
            'device_type' => $request->input('deviceType')
        ];
        $model = PushToken::query()->updateOrCreate(['user_id' => $request->user()->id], $data);
        // $model = PushToken::where('user_id', $request->user()->id)->update($data);
	//$model = PushToken::query()->updateOrCreate(['user_id' => $request->user()->id], $data);
        return $this->sendJson([
            'push_token' => $model
        ]);
    }

    /**
     * @SWG\Definition(
     *            definition="UserInfo",
     * 			@SWG\Property(property="id", type="integer"),
     * 			@SWG\Property(property="name", type="string"),
     * 			@SWG\Property(property="sex", type="string"),
     * 			@SWG\Property(property="age", type="integer"),
     * 			@SWG\Property(property="cover", type="string"),
     *        )
     */

    /**
     * @SWG\Delete(
     *      path="/user/cover",
     *      operationId="deleteCoverUser",
     *      tags={"user"},
     *      summary="Delete User cover",
     *      description="Delete User user",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function deleteCover(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->cover = "";

        if (!$user->save()) {
            return $this->sendJsonErrors('User not save. DB error');
        }

        return $this->sendJson([]);
    }


    /**
     * @SWG\Definition(
     *            definition="UserInfoShort",
     * 			@SWG\Property(property="id", type="integer"),
     * 			@SWG\Property(property="name", type="string"),
     * 			@SWG\Property(property="nickname", type="string"),
     *        )
     */

    /**
     * @SWG\Get(
     *      path="/user/search",
     *      operationId="searchUser",
     *      tags={"user"},
     *      summary="Search users by name or/and age or/and sex",
     *      description="Search users",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *          name="name", in="query", type="string",
     *          description="Users nickname min 3 letters",
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          @SWG\Schema(ref="#/definitions/UserInfoShort"),
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     */
    public function filterAction(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'name' => 'required|string|max:25|min:3',
        ]);
        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }
       // $request->user()->id
        $builder = User::query();
        $builder->orWhere('nickname', 'like', "%" . $request->get("name") . "%")
            ->orWhere('name', 'like', "%" . $request->get("name") . "%");


        $collection = $builder->get();

        foreach ( $collection as $rs)
            {
                if($request->user()->id == $rs->id)
                {
                    return $this->sendJson('That User is logged-in');
                }
                else
                {   
                    return $this->sendJson($this->prepareCollection(new Collection($collection->map(function ($user) {
                        return $user->getShortInfo();
                    })), 'users'));
                }
            }
//        return $this->sendJson($this->prepareCollection(new Collection($collection->map(function ($user) {
//            return $user->getShortInfo();
//        })), 'users'));
    }

    /**
     * @SWG\Get(
     *      path="/user/invite",
     *      operationId="getUserInvite",
     *      tags={"user"},
     *      summary="User invite count",
     *      description="Get user invite count",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function getInviteCount(Request $request)
    {
        $dareInviteCount =  HeadToHeadInvite::query()
            ->where('user_id', $request->user()->id)
            ->where('status', HeadToHeadInvite::STATUS_INVITED)
            ->whereHas('headToHead', function ($query) {
                $query->where('game_type', HeadToHead::GAME_TYPE_DARE);
            })
            ->count('id');

        $hthInviteCount =  HeadToHeadInvite::query()
            ->where('user_id', $request->user()->id)
            ->where('status', HeadToHeadInvite::STATUS_INVITED)
            ->whereHas('headToHead', function ($query) {
                $query->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
            })
            ->count('id');

        $gameInviteCount = UserInGame::query()->where('user_id', $request->user()->id)
            ->where('status', UserInGame::NOT_CONFIRM_STATUS)
            ->count('id');

	 $comment_count= User_Feed_Count::where('user_id',$request->user()->id)
            ->where('post_user_id','!=',null)
            ->where('is_read',0)
            ->count();
        $like_count= User_Feed_Count::where('user_id',$request->user()->id)
            ->where('like_user_id','!=',null)
            ->where('is_read',0)
            ->count();
        $follow_count= User_Feed_Count::where('follow_user_id',$request->user()->id)
            ->where('follow_user_id','!=',null)
            ->where('is_read',0)
            ->count();
        $feed_post_count= Post_count::where('o_user_id',$request->user()->id)
            ->where('is_read',0)
            ->count();

	$total_count = $comment_count+$like_count+$follow_count+$feed_post_count;

        return $this->sendJson([
            'invites' => [
                'dare' => $dareInviteCount,
                'head_to_head' => $hthInviteCount,
                'game' => $gameInviteCount,
		'social_count'=>$total_count,
            ]
        ]);
    }

    /**
     * @SWG\Get(
     *      path="/user/notification",
     *      operationId="getUserNotification ",
     *      tags={"user"},
     *      summary="User notification",
     *      description="Get user invite count",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function getNotifications(Request $request)
    {
        $collection = NotificationModel::query()
            ->limit(30)
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'DESC')
            ->get();

        return $this->sendJson([
            'notifications' => $collection
        ]);
    }

    /**
     * @SWG\Get(
     *      path="/user/stats",
     *      operationId="getUserStats",
     *      tags={"user"},
     *      summary="User stats",
     *      description="Get user stats",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function getStats(Request $request)
    {
	 $loss=UserInGame::where('user_id',$request->user()->id)->where('is_win','false')->count();
        $win=UserInGame::where('user_id',$request->user()->id)->where('is_win','true')->count();

            if($loss>=1)
            {
                UserStat::where('user_id',$request->user()->id)->update(['games_lose'=>$loss]);
            }
            if($win>=1)
            {
                UserStat::where('user_id',$request->user()->id)->update(['games_win'=>$win]);
            }
         $request->user()->load('userStat');

        return $this->sendJson([
            'games_win' => $request->user()->userStat->games_win,
            'games_lose' => $request->user()->userStat->games_lose,
            'hth_win' => $request->user()->userStat->hth_win,
            'hth_lose' => $request->user()->userStat->hth_lose,
            'dares_win' => $request->user()->userStat->dares_win,
            'dares_lose' => $request->user()->userStat->dares_lose
        ]);       
    }
}
