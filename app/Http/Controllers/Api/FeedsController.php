<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Carbon\Carbon;
use App\Models\Feeds;
use App\Post_count;
use App\User_follow;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\PostComment;
use App\PostLike;
use App\User_Feed_Count;
use Davibennun\LaravelPushNotification\Facades\PushNotification;
use App\Models\PushToken;
use DB;
use App\Models\HeadToHead;
use App\Models\HeadToHeadInvite;
use App\Models\NotificationModel;
use App\Models\UserInGame;
use App\Events\CreateNotification;

class FeedsController extends Controller
{

    /**
     * @SWG\Get(
     *      path="/feedback",
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
            // 'feed' => Feeds::orderBy("created_at", "desc")->with(['user'])->paginate()
            'feed' =>[


                "data"=> Feeds::orderBy("created_at", "desc")->with('user')->with('like_current')->withCount('post')->withCount('like')->get()
//                 "comment_count"=>$datasave,
//                "like_count"=>$datasave1,

                 //return response()->json(['data'=>$data]);
                    ]
        ]);
      
    }

    /**
     * @SWG\Get(
     *      path="/feedback",
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
    public function myfeeds(Request $request)
    {
        $user = $request->user();
        return $this->sendJson([
            // for pagination
            // 'feed' => Feeds::orderBy("created_at", "desc")->where("user_id", $user->id)->with(['user'])->paginate()
            
            'feed' =>[
                "data" => Feeds::orderBy("created_at", "desc")->where("user_id", $user->id)->with(['user'])->get()
            ]
        ]);
    }

    public function feed_count(Request $request)
    {

        $temp= User_Feed_Count::where('user_id','=',$request->user()->id)->count();

        $dt = User::where('id',$request->user()->id)->first();
                $st = array('userid'=>$request->user()->id,'feed_count'=>$temp);
                return $this->sendJson($st);

    }

    /**
     * @SWG\Get(
     *      path="/feedback/add",
     *      operationId="getUserInfo",
     *      tags={"user"},
     *      summary="feedback information",
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
    public function create(Request $request)
    {

        // print_r($_FILES); die;
        $validator = $this->getValidationFactory()->make($request->all(), [
            'feed_content' => 'nullable|string|max:500|min:0',
            'feed_type' => 'required|numeric',
	        'feed_height' => 'required',
            'feed_width'=>'required'
        ]);

        if( $request->feed_type == 1 ) {
            // $validator = $this->getValidationFactory()->make($request->all(), [
            //     'feed_file' => 'nullable|file|mimes:jpeg,bmp,png,jpg',
            // ]);
        }

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

         /** @var User $user */
         $user = $request->user();
         
         $feeds = new Feeds();
         if ($request->hasFile('feed_file')) {
             $feeds->saveFeedByFile($request->file('feed_file'));
         }

        if ($request->feed_content) {
            $feeds->feed_content = $request->feed_content;
        }
        date_default_timezone_set('UTC');
        $feeds->user_id = $user->id;
        $feeds->feed_type = $request->feed_type;
	    $feeds->feed_height = $request->feed_height;
        $feeds->feed_width = $request->feed_width;
        $feeds->created_at = Carbon::now();
        $feeds->updated_at = Carbon::now();
        

        if (!$feeds->save()) {
            return $this->sendJsonErrors('Feeds not save');
        }


//        User_Feed_Count::create([
//            'user_id'=>$request->user()->id,
//            'feed_post_id'=> $feeds->id,
//        ]);

       $asp = User::where('id','!=',$user->id)->get();

        foreach ($asp as $rw)
        {
            Post_count::create([
               'post_id'=>$feeds->id,
                'o_user_id'=>$rw->id,
            ]);
        }
        return $this->sendJson($feeds);

    }


 public function create_comment(Request $request)
    {
	
        $validator = $this->getValidationFactory()->make($request->all(), [
            'u_comment' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $feedModel = Feeds::where('id',$request->post_id)->first();
        date_default_timezone_set('UTC');
        $post_data = new PostComment();
        $post_data->user_id = $request->user()->id;
        $post_data->post_id = $request->get('post_id');
        $post_data->u_comment = $request->get('u_comment');
        $post_data->created_at = Carbon::now();
        $post_data->updated_at = Carbon::now();
        $post_data->save();

        $post_data1 = new User_Feed_Count();
        $post_data1->post_user_id = $request->user()->id;
        $post_data1->user_id = $feedModel->user_id;
        $post_data1->post_id = $request->get('post_id');
       // $post_data1->u_comment = $request->get('u_comment');
        $post_data1->save();


        if($post_data)
        {
            $t2=User_Feed_Count::orderBy("id", "desc")->where('post_user_id',$request->user()->id)->where('is_read',0)->first();
            
            
            if($t2!=null)
                {
            $pushToken2 = PushToken::query()->where('user_id',$feedModel->user_id)->orderBy('id', 'desc')->first();
                if (!$pushToken2) {
                    return;
                }
                else
                {
			 $dareInviteCount =  HeadToHeadInvite::query()
            ->where('user_id', $t2->user_id)
            ->where('status', HeadToHeadInvite::STATUS_INVITED)
            ->whereHas('headToHead', function ($query) {
                $query->where('game_type', HeadToHead::GAME_TYPE_DARE);
            })
            ->count('id');

        $hthInviteCount =  HeadToHeadInvite::query()
            ->where('user_id', $t2->user_id)
            ->where('status', HeadToHeadInvite::STATUS_INVITED)
            ->whereHas('headToHead', function ($query) {
                $query->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
            })
            ->count('id');

        $gameInviteCount = UserInGame::query()->where('user_id', $t2->user_id)
            ->where('status', UserInGame::NOT_CONFIRM_STATUS)
            ->count('id');

                    $message2 = $request->user()->name.' Commented on your Post';
                     $comment_count= User_Feed_Count::where('user_id',$t2->user_id)
                        ->where('post_user_id','!=',null)
                        ->where('is_read',0)
                        ->count();
                    $like_count= User_Feed_Count::where('user_id',$t2->user_id)
                        ->where('like_user_id','!=',null)
                        ->where('is_read',0)
                        ->count();
                    $follow_count= User_Feed_Count::where('follow_user_id',$t2->user_id)
                        ->where('follow_user_id','!=',null)
                        ->where('is_read',0)
                        ->count();
                    $feed_post_count= Post_count::where('o_user_id',$t2->user_id)
                        ->where('is_read',0)
                        ->count();
            
                $total_count = $comment_count+$like_count+$follow_count+$feed_post_count+$dareInviteCount+$hthInviteCount+$gameInviteCount;                             
            if ($pushToken2->device_type === 1) {
                $NotificationType = 6;
            PushNotification::app('ios')
                        ->to($pushToken2->token)
                        ->send($message2,array(
                            'badge' =>$total_count,
                            'sound' => 'example.aiff',
                            'custom' => array('custom data' => array(
                                'notification_type' => $NotificationType
                            ))
                        ));
            }
            if ($pushToken2->device_type === 2) {
                $createEvent = new CreateNotification;
                $NotificationType = 6;
                $createEvent->android($pushToken2->token, $message2, $NotificationType);
            }

                            }
            
        }
            return $this->sendJson(["message" => "Comment Added successfully","badge"=>$total_count]);
        }
        else
        {
            return $this->sendJson(["message" => "Something want rong"]);
        }
    }


 public function getdata($id)
    {

        return $this->sendJson([
            // 'feed' => Feeds::orderBy("created_at", "desc")->with(['user'])->paginate()

            'feed' =>[
                "data" => PostComment::orderBy("created_at", "asc")->where('post_id',$id)->with(['user'])->get(),
            ]
        ]);


    }

    public function show_social_count(Request $request)
    {
       $a1 = User_Feed_Count::where('user_id',$request->user()->id)->update([
           'is_read'=>1
        ]);

        $a2 = Post_count::where('o_user_id',$request->user()->id)->update([
            'is_read'=>1
        ]);

        $a3 = User_Feed_Count::where('follow_user_id',$request->user()->id)->update([
            'is_read'=>1
        ]);

        if($a1 || $a2 || $a3)
        {
            return $this->sendJson(["message" => "show Successfully"]);
        }
        else
        {
            return $this->sendJson(["message" => "Something want rong"]);
        }
    }

    public function create_like(Request $request)
    {
        $lk_data = PostLike::where('post_id',$request->post_id)->where('user_id',$request->user()->id)->get();
        $feedModel = Feeds::where('id',$request->post_id)->first();
        if(count($lk_data)<=0)
        {
            PostLike::create([
                'user_id'=> $request->user()->id,
                'post_id'=>$request->post_id,
                'flag_like'=>$request->flag_like
            ]);

            User_Feed_Count::create([
                'user_id'=>$feedModel->user_id,
                'like_user_id'=> $request->user()->id,
                'post_id'=>$request->post_id,
            ]);

            $t1=User_Feed_Count::where('user_id',$feedModel->user_id)->where('post_id',$request->post_id)->where('is_read',0)->orderBy('id', 'desc')->limit(1)->first();

                   
                   
                    if($t1!=null)
                        {
                        
                            $pushToken1 = PushToken::query()->where('user_id',$feedModel->user_id)->orderBy('id', 'desc')->first();
            // print_r($pushToken1);
            // die("if");
                                if (!$pushToken1) {
                                    return;
                                } else {
                                    $message1 = $request->user()->name . ' liked your Post';

				 $dareInviteCount =  HeadToHeadInvite::query()
            ->where('user_id', $t1->user_id)
            ->where('status', HeadToHeadInvite::STATUS_INVITED)
            ->whereHas('headToHead', function ($query) {
                $query->where('game_type', HeadToHead::GAME_TYPE_DARE);
            })
            ->count('id');

        $hthInviteCount =  HeadToHeadInvite::query()
            ->where('user_id', $t1->user_id)
            ->where('status', HeadToHeadInvite::STATUS_INVITED)
            ->whereHas('headToHead', function ($query) {
                $query->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
            })
            ->count('id');

        $gameInviteCount = UserInGame::query()->where('user_id', $t1->user_id)
            ->where('status', UserInGame::NOT_CONFIRM_STATUS)
            ->count('id');

                                     $comment_count= User_Feed_Count::where('user_id',$t1->user_id)
                        ->where('post_user_id','!=',null)
                        ->where('is_read',0)
                        ->count();
                    $like_count= User_Feed_Count::where('user_id',$t1->user_id)
                        ->where('like_user_id','!=',null)
                        ->where('is_read',0)
                        ->count();
                    $follow_count= User_Feed_Count::where('follow_user_id',$t1->user_id)
                        ->where('follow_user_id','!=',null)
                        ->where('is_read',0)
                        ->count();
                    $feed_post_count= Post_count::where('o_user_id',$t1->user_id)
                        ->where('is_read',0)
                        ->count();
                                        $total_count = $comment_count+$like_count+$follow_count+$feed_post_count+$dareInviteCount+$hthInviteCount+$gameInviteCount;
                                    if ($pushToken1->device_type === 1) {
                                        $NotificationType = 5;
                                            PushNotification::app('ios')
                                        ->to($pushToken1->token)
                                        ->send($message1,array(
                                            'badge' =>$total_count,
                                            'sound' => 'example.aiff',
                                            'custom' => array('custom data' => array(
                                                'notification_type' => $NotificationType
                                            ))
                                        ));  
                                    }
                                    if ($pushToken1->device_type === 2) {
// die("first if");
                                        $createEvent = new CreateNotification;
                                        $NotificationType = 5;
                                        $createEvent->android($pushToken1->token, $message1, $NotificationType);
                                    }
                                }
                
                    }
        }
        else
        {
           
		// $dt = User_Feed_Count::where('post_id',$request->post_id)->where('like_user_id',$request->user()->id)->get();

//            if(count($dt)<=0)
//            {
//                User_Feed_Count::create([
//                    'user_id'=>$feedModel->user_id,
//                    'like_user_id'=> $request->user()->id,
//                    'post_id'=>$request->post_id,
//                ]);
//            }

            if($request->flag_like=='true')
            {

                User_Feed_Count::create([
                    'user_id'=>$feedModel->user_id,
                    'like_user_id'=> $request->user()->id,
                    'post_id'=>$request->post_id,
                ]);
                $t1=User_Feed_Count::orderBy("created_at", "desc")->where('like_user_id',$request->user()->id)->where('is_read',0)->first();

                if($t1!=null)
                {

                    $pushToken1 = PushToken::query()->where('user_id',$t1->user_id)->orderBy('id', 'desc')->first();

                    if (!$pushToken1) {
                        return;
                    } else {
			 $dareInviteCount =  HeadToHeadInvite::query()
            ->where('user_id', $t1->user_id)
            ->where('status', HeadToHeadInvite::STATUS_INVITED)
            ->whereHas('headToHead', function ($query) {
                $query->where('game_type', HeadToHead::GAME_TYPE_DARE);
            })
            ->count('id');

        $hthInviteCount =  HeadToHeadInvite::query()
            ->where('user_id', $t1->user_id)
            ->where('status', HeadToHeadInvite::STATUS_INVITED)
            ->whereHas('headToHead', function ($query) {
                $query->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
            })
            ->count('id');

        $gameInviteCount = UserInGame::query()->where('user_id', $t1->user_id)
            ->where('status', UserInGame::NOT_CONFIRM_STATUS)
            ->count('id');
                        $message1 = $request->user()->name . ' liked your post';
                        $comment_count= User_Feed_Count::where('user_id',$t1->user_id)
                            ->where('post_user_id','!=',null)
                            ->where('is_read',0)
                            ->count();
                        $like_count= User_Feed_Count::where('user_id',$t1->user_id)
                            ->where('like_user_id','!=',null)
                            ->where('is_read',0)
                            ->count();
                        $follow_count= User_Feed_Count::where('follow_user_id',$t1->user_id)
                            ->where('follow_user_id','!=',null)
                            ->where('is_read',0)
                            ->count();
                        $feed_post_count= Post_count::where('o_user_id',$t1->user_id)
                            ->where('is_read',0)
                            ->count();


                         $total_count = $comment_count+$like_count+$follow_count+$feed_post_count+$dareInviteCount+$hthInviteCount+$gameInviteCount;

                        if ($pushToken1->device_type === 1) {
                            $NotificationType = 5;
                            PushNotification::app('ios')
                            ->to($pushToken1->token)
                            ->send($message1,array(
                                'badge' =>$total_count,
                                'sound' => 'example.aiff',
                                'custom' => array('custom data' => array(
                                    'notification_type' => $NotificationType
                                ))
                            ));
                        }

                        if ($pushToken1->device_type === 2) {
                            $createEvent = new CreateNotification;
                            $NotificationType = 5;
                            $createEvent->android($pushToken1->token, $message1, $NotificationType);
                        }
                    }

                }
            }		
		
            PostLike::where('user_id',$request->user()->id)->where('post_id',$request->get('post_id'))->update([
                'flag_like'=>$request->flag_like
            ]);
        }

        return response()->json(['flag'=>$request->flag_like,"badge"=>$total_count]);

    }


    public function delete(Request $request)
    {

        $validator = $this->getValidationFactory()->make($request->all(), [
            'feed_id' => 'required|numeric',
        ]);
        
        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $model = Feeds::find( $request->feed_id);

        if( $model) {
            $model->delete();
     
            return $this->sendJson(["message" => "Feed deleted successfully"]);
        }

        return $this->sendJson(["error" => "Feed Not found."]);
    }



 
    public function create_follow(Request $request)
    {
        $lk_data3 = User_follow::where('user_id',$request->user()->id)->where('follow_id',$request->follow_id)->get();

        if(count($lk_data3)<=0)
        {
            User_follow::create([
                'user_id'=> $request->user()->id,
                'follow_id'=>$request->follow_id,
                'flag_follow'=>$request->flag_follow
            ]);

            User_Feed_Count::create([
                'user_id'=>$request->user()->id,
                'follow_user_id'=> $request->follow_id
            ]);


            $t3=User_Feed_Count::where('user_id',$request->user()->id)->where('is_read',0)->first();


            if($t3!=null)
            {
                $pushToken3 = PushToken::query()->where('user_id',$t3->follow_user_id)->orderBy('id', 'desc')->first();

                if (!$pushToken3) {
                    return;
                }
                else
                {
                    $message3 = $request->user()->name.' follow you';
                    $comment_count= User_Feed_Count::where('user_id',$t3->user_id)
                        ->where('post_user_id','!=',null)
                        ->where('is_read',0)
                        ->count();
                    $like_count= User_Feed_Count::where('user_id',$t3->user_id)
                        ->where('like_user_id','!=',null)
                        ->where('is_read',0)
                        ->count();
                    $follow_count= User_Feed_Count::where('follow_user_id',$t3->user_id)
                        ->where('follow_user_id','!=',null)
                        ->where('is_read',0)
                        ->count();
                    $feed_post_count= Post_count::where('o_user_id',$t3->user_id)
                        ->where('is_read',0)
                        ->count();

                        $total_count = $comment_count+$like_count+$follow_count+$feed_post_count;
                    if ($pushToken3->device_type === 1) {
                        $NotificationType = 7;
                    PushNotification::app('ios')
                        ->to($pushToken3->token)
                        ->send($message3,array(
                            'badge' =>$total_count,
                            'sound' => 'example.aiff',
                            'custom' => array('custom data' => array(
                                'notification_type' => $NotificationType
                            ))
                        ));
                    }

                    if ($pushToken3->device_type === 2) {
                        $createEvent = new CreateNotification;
                        $NotificationType = 7;
                        $createEvent->android($pushToken3->token, $message3, $NotificationType);
                    }
                }

            }

        }
        else
        {


            if($request->flag_follow=='true')
            {

                User_Feed_Count::create([
                    'user_id'=>$request->user()->id,
                    'follow_user_id'=> $request->follow_id
                ]);
                $t3=User_Feed_Count::orderBy("created_at", "desc")->where('user_id',$request->user()->id)->where('is_read',0)->first();

                if($t3!=null)
                {

                    $pushToken3 = PushToken::query()->where('user_id',$t3->follow_user_id)->orderBy('id', 'desc')->first();

                    if (!$pushToken3) {
                        return;
                    } else {
                        $message3 = $request->user()->name . ' follow your';
                        $comment_count= User_Feed_Count::where('user_id',$t3->user_id)
                            ->where('post_user_id','!=',null)
                            ->where('is_read',0)
                            ->count();
                        $like_count= User_Feed_Count::where('user_id',$t3->user_id)
                            ->where('like_user_id','!=',null)
                            ->where('is_read',0)
                            ->count();
                        $follow_count= User_Feed_Count::where('follow_user_id',$t3->user_id)
                            ->where('follow_user_id','!=',null)
                            ->where('is_read',0)
                            ->count();
                        $feed_post_count= Post_count::where('o_user_id',$t3->user_id)
                            ->where('is_read',0)
                            ->count();

                            $total_count = $comment_count+$like_count+$follow_count+$feed_post_count;

                        if ($pushToken3->device_type === 1) {
                            $NotificationType = 7;

                        PushNotification::app('ios')
                            ->to($pushToken3->token)
                            ->send($message3,array(
                                'badge' =>$total_count,
                                'sound' => 'example.aiff',
                                'custom' => array('custom data' => array(
                                    'notification_type' => $NotificationType
                                ))
                            ));
                        }
                        if ($pushToken3->device_type === 2) {
                            $createEvent = new CreateNotification;
                            $NotificationType = 7;
                            $createEvent->android($pushToken3->token, $message3, $NotificationType);
                        }
                    }

                }
            }
            else if($request->flag_follow=='false')
            {
                DB::table('user__feed__counts')->where('user_id',$request->user()->id)->delete();
            }
            User_follow::where('user_id',$request->user()->id)->where('follow_id',$request->follow_id)->update([
                'flag_follow'=>$request->flag_follow
            ]);

            $md_data3 = User_Feed_Count::where('user_id',$request->user()->id)->where('follow_user_id',$request->follow_id)->get();

            if(count($md_data3)<=0) {
                User_Feed_Count::create([
                    'user_id'=>$request->user()->id,
                    'follow_user_id'=> $request->follow_id
                ]);
            }

        }

        return response()->json(['flag'=>$request->flag_follow]);
    }



    public function single_user($id,Request $request)
    {
        $user = User::where('id',$id)->first();

        $request->user();

        $us_follow = User_follow::select('flag_follow')->where('user_id',$request->user()->id)->where('follow_id',$user->id)->first();

            if($us_follow==null)
            {
                $us_follow = ['flag_follow'=>'false'];
            }
            else
            {
                $us_follow = User_follow::select('flag_follow')->where('user_id',$request->user()->id)->where('follow_id',$user->id)->first();
            }

        return $this->sendJson([
            // 'feed' => Feeds::orderBy("created_at", "desc")->with(['user'])->paginate()

                'user'=>$user,'user_follow'=>$us_follow
        ]);


        //return response()->json(['user'=>$user,'user_follow'=>$us_follow]);

        /* For show current user follow and unfollow users */
//        $data = [];
//        $data1 = [];
//            $f_user = User_follow::where('user_id',$user->id)->where('flag_follow','true')->get();
//        $u_user = User_follow::where('user_id',$user->id)->where('flag_follow','false')->get();
//            foreach ($f_user as $row)
//            {
//                array_push($data, $row->follow_id);
//            }
//        $b_user = User::whereIn('id',$data)->get();
//        foreach ($u_user as $rw)
//        {
//            array_push($data1, $rw->follow_id);
//        }
//            //print_r($data);
//        $c_user = User::whereIn('id',$data1)->get();
//
//        return response()->json(['user'=>$user,'follow_user'=>$b_user,'unfollow_user'=>$c_user]);

    }


    public function social_count(Request $request)
    {

       // Log::useDailyFiles(storage_path('logs/debug1.log'));


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
        $st = array('total_count'=>$total_count,'follow_count'=>$follow_count);

//        $t1=User_Feed_Count::where('like_user_id',$request->user()->id)->where('is_read',0)->first();
//        $t2=User_Feed_Count::where('post_user_id',$request->user()->id)->where('is_read',0)->first();
//        $t3=User_Feed_Count::where('user_id',$request->user()->id)->where('is_read',0)->first();

//        if($t1!=null)
//        {
//            $pushToken1 = PushToken::query()->where('user_id',$t1->user_id)->orderBy('id', 'desc')->first();
//            if($t1!=null) {
//                if (!$pushToken1) {
//                    return;
//                } else {
//                    $message1 = $request->user()->name . 'is like your Post';
//
//                    $message11 = PushNotification::Message($message1,array(
//                        'badge' => $total_count,
//                        'sound' => 'example.aiff'));
//
//                    PushNotification::app('ios')
//                        ->to($pushToken1->token)
//                        ->send($message11);
//
//                }
//
//            }
//        }
//
//        if($t2!=null)
//        {
//            $pushToken2 = PushToken::query()->where('user_id',$t2->user_id)->orderBy('id', 'desc')->first();
//            if($t2!=null)
//            {
//                if (!$pushToken2) {
//                    return;
//                }
//                else
//                {
//                    $message2 = $request->user()->name.'is Comment on your Post';
//                    $message22 = PushNotification::Message($message2,array(
//                        'badge' => $total_count,
//                        'sound' => 'example.aiff'));
//
//                    PushNotification::app('ios')
//                        ->to($pushToken2->token)
//                        ->send($message22);
//                }
//            }
//        }
//
//        if($t3!=null)
//        {
//            $pushToken3 = PushToken::query()->where('user_id',$t3->follow_user_id)->orderBy('id', 'desc')->first();
//            if($t3!=null)
//            {
//                if (!$pushToken3) {
//                    return;
//                }
//                else
//                {
//                    $message3 = $request->user()->name.'is follow you';
//
//                    $message33 = PushNotification::Message($message3,array(
//                        'badge' => $total_count,
//                        'sound' => 'example.aiff'));
//
//                    PushNotification::app('ios')
//                        ->to($pushToken3->token)
//                        ->send($message33);
//                }
//            }
//        }


        return $this->sendJson($st);
    }

    public function update(Request $request)
    {


        $validator = $this->getValidationFactory()->make($request->all(), [
            'feed_content' => 'nullable|string|max:500|min:0',
            'feed_type' => 'required|numeric',
	    'feed_height' => 'required',
            'feed_width'=>'required'
        ]);

        if( $request->feed_type == 1 ) {
            // $validator = $this->getValidationFactory()->make($request->all(), [
            //     'feed_file' => 'nullable|file|mimes:jpeg,bmp,png,jpg',
            // ]);
        }

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }


        $feeds = Feeds::find( $request->feed_id);

        if( $feeds) {
            $feeds->feed_content = $request->feed_content;
            $feeds->feed_type = $request->feed_type;
	    $feeds->feed_height = $request->feed_height;
            $feeds->feed_width = $request->feed_width;

            if ($request->hasFile('feed_file')) {
                $feeds->saveFeedByFile($request->file('feed_file'));
            }

            if (!$feeds->save()) {
                return $this->sendJsonErrors('Feeds not save');
            }
            
            return $this->sendJson($feeds);
        }
        
        return $this->sendJson(["error" => "Feed Not found."]);
    }
}