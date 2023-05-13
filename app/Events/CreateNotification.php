<?php

namespace App\Events;

use App\Models\Game;
use App\Models\HeadToHead;
use App\Models\HeadToHeadInvite;
use App\Models\PushToken;
use App\Models\User;
use App\Models\UserInGame;
use Illuminate\Broadcasting\Channel;
use App\Models\NotificationModel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use DB;
use DateTime;

class CreateNotification {

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /** @var  NotificationModel[] */
    private $collection;
    private $hasError = false;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user = null) {
        $this->collection = [];
    }

    /**
     * @return bool
     */
    public function isHasError(): bool {
        return $this->hasError;
    }

    public function gameNotification(Game $gameModel, $userId, $type = NotificationModel::TYPE_GLOBAL) {
        $model = new NotificationModel();
        $model->user_id = $userId;
        if ($type == NotificationModel::TYPE_CREATE_GAME) {
            $model->message = $gameModel->prepareNotificationMessageCreate();
        }

        if ($type == NotificationModel::TYPE_DARE_WIN) {
            $model->message = $gameModel->prepareNotificationMessageWin();
        }

        if ($type == NotificationModel::TYPE_GAME_LOSE) {
            $model->message = $gameModel->prepareNotificationMessageLose();
        }

        if ($type == NotificationModel::TYPE_GAME_TIE) {
            $model->message = $gameModel->prepareNotificationMessageTie();
        }

        if (!$model->message) {
            \Log::error('ERROR: Invalid NotificationModel TYPE ' . $type);
            $this->hasError = true;
            return;
        }

        $this->collection[] = $model;
    }

    public function gameNotificationInvite(Game $gameModel, $userId) {
        $model = new NotificationModel();
        $model->user_id = $userId;
        $model->message = $gameModel->prepareInviteNotificationMessage();
        $this->collection[] = $model;


        $pushToken = PushToken::query()->where('user_id', $userId)->orderBy('id', 'desc')->first();
        if (!$pushToken) {
            return;
        }
        if ($pushToken->device_type === 1) {
            $NotificationType = 3;
            $budge = $this->getInviteCount($userId);
            \PushNotification::app('ios')
                ->to($pushToken->token)
                ->send($this->createPush($model->message, $budge, $NotificationType));
        }
        if ($pushToken->device_type === 2) {
            $NotificationType = 3;
            $this->android($pushToken->token, $model->message, $NotificationType);
        }
    }

    public function hthNotification(HeadToHead $hthModel, $userId, $type = HeadToHead::TYPE_DRAW) {
        $model = new NotificationModel();
        $model->user_id = $userId;
        if ($type == HeadToHead::TYPE_DRAW) {
            $model->message = $hthModel->prepareNotificationMessageTie();
        }

        if ($type == HeadToHead::TYPE_WIN) {
            $model->message = $hthModel->prepareNotificationMessageWin();
        }

        if ($type == HeadToHead::TYPE_LOSE) {
            $model->message = $hthModel->prepareNotificationMessageLose();
        }

        if (!$model->message) {
            \Log::error('ERROR: Invalid NotificationModel TYPE ' . $type);
            $this->hasError = true;
            return;
        }

        $this->collection[] = $model;
    }

    public function hthNotificationCreate(HeadToHead $hthModel, $userId) {
        $model = new NotificationModel();
        $model->user_id = $userId;
        $model->message = $hthModel->prepareNotificationMessageCreate();
        $this->collection[] = $model;
    }

    public function hthNotificationInvite(HeadToHead $hthModel, $userId, $NotificationType) {
        $model = new NotificationModel();
        $model->user_id = $userId;
        $model->message = $hthModel->prepareInviteNotificationMessage();
        $this->collection[] = $model;
        $pushToken = PushToken::query()->where('user_id', $userId)->orderBy('id', 'desc')->first();
        if (!$pushToken) {
            return;
        }
        if ($pushToken->device_type === 1) {
            $budge = $this->getInviteCount($userId);
            \PushNotification::app('ios')
                ->to($pushToken->token)
                ->send($this->createPush($model->message, $budge, $NotificationType));
        }
        if ($pushToken->device_type === 2) {
            $this->android($pushToken->token, $model->message, $NotificationType);
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('channel-main');
    }

    /**
     * @return NotificationModel[]
     */
    public function getCollection(): array {
        return $this->collection;
    }

    public function createPush($stringText, $countBange, $NotificationType) {
        return \PushNotification::Message($stringText, array(
            'badge' => $countBange,
            'sound' => 'example.aiff',
            'custom' => array('custom data' => array(
                'notification_type' => $NotificationType
            ))
        ));
    }

    public function getInviteCount($userId): int {
        $dareInviteCount = HeadToHeadInvite::query()
            ->where('user_id', $userId)
            ->where('status', HeadToHeadInvite::STATUS_INVITED)
            ->whereHas('headToHead', function ($query) {
                $query->where('game_type', HeadToHead::GAME_TYPE_DARE);
            })
            ->count('id');

        $hthInviteCount = HeadToHeadInvite::query()
            ->where('user_id', $userId)
            ->where('status', HeadToHeadInvite::STATUS_INVITED)
            ->whereHas('headToHead', function ($query) {
                $query->where('game_type', HeadToHead::GAME_TYPE_SINGLE);
            })
            ->count('id');

        $gameInviteCount = UserInGame::query()->where('user_id', $userId)
            ->where('status', UserInGame::NOT_CONFIRM_STATUS)
            ->count('id');

        return $dareInviteCount + $hthInviteCount + $gameInviteCount;
    }

    public function predectionReminder($userId) {
        $model = new NotificationModel();
        $model->user_id = $userId;
        $model->message = "Don’t forget to make your picks for this week’s games.";
        // $this->collection[] = $model;
        $pushToken = PushToken::query()->where('user_id', $userId)->orderBy('id', 'desc')->first();

        if (!$pushToken) {
            return;
        }

        $budge = $this->getInviteCount($userId);

        \PushNotification::app('ios')
            ->to($pushToken->token)
            ->send($this->createPush($model->message, $budge));
    }

    public function allReminder($userId) {

        //$sql = "select * from crontime";
        //$get = DB::select($sql);
        $get = DB::table('crontime')->get();
        $currentday = date('l');
        $currenttime = time();
        $date = new DateTime;
        $date->setTimestamp($currenttime);
        $forgot_valid = $date->format('H:i');

        $message="";
        foreach ($get as $dt) {

            $cronid = $dt->crontime_id;
            $crondays = $dt->days;
            $runstatus = $dt->run_status;
            $crontime = $dt->time;
            if ($runstatus == 0) {
                if ($currentday == $crondays) {
                    if ($forgot_valid === $dt->time_string) {

                        $get_data = DB::table('crontime')->where('time',$crontime)->get();
                        //$message = $dt->message;
                    }
                }
            }
        }

        $model = new NotificationModel();
        $model->user_id = $userId;

        foreach ($get_data as $rw)
        {
            $model->message = $rw->message;
            $pushToken = PushToken::query()->where('user_id', $userId)->orderBy('id', 'desc')->first();

            if (!$pushToken) {
                return;
            }

            if ($pushToken->device_type === 1) {
                $NotificationType = 8;
                $budge = $this->getInviteCount($userId);

                $date = date('Y-m-d H:i:s');
                DB::table('log')->insert(
                    array(
                        'user_id' => $userId,
                        'message' => $model->message,
                        'createtime' => $date,
                        'token' => $pushToken->token
                    )
                );

                \PushNotification::app('ios')
                    ->to($pushToken->token)
                    ->send($this->createPush($model->message, $budge, $NotificationType));
            }
            /*if ($pushToken->device_type === 2) {
                $budge = $this->getInviteCount($userId);

                $date = date('Y-m-d H:i:s');
                DB::table('log')->insert(
                    array(
                        'user_id' => $userId,
                        'message' => $model->message,
                        'createtime' => $date,
                        'token' => $pushToken->token
                    )
                );

                \PushNotification::app('appNameAndroid')
                    ->to($pushToken->token)
                    ->send($this->createPush($model->message, $budge));
            }*/
        }


        // $this->collection[] = $model;


    }

    public function android($token, $message, $NotificationType)
    {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => 'FOOTYPICKS',
            'body' => $message,
            'type' => $NotificationType,
            'sound' => true,
        ];
        
        $extraNotificationData = ["message" => $notification];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $token, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key=AAAAdL2WEtQ:APA91bENbsPOLgIGON412X4prX8qenOO5iiyL__-Dhk_Ih-JzsWh_3IQ-JVa0mNAMjSUbKYq4yDGlVZVpS1XG4TlCoobYEDjo1ZS1CJHY8mcp2hS_oevvyQlWghn921WuZ5lrlrqe44G',
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
    }
}
