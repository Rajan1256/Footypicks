<?php

namespace App\Listeners;

use App\Models\NotificationModel;
use Log;
use App\Events\CreateNotification as Event;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateNotification
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  Event  $event
     * @return void
     */
    public function handle(Event $event)
    {
        if($event->isHasError()) {
            return;
        }

        foreach ($event->getCollection() as $model) {
            if(!$model->save()) {
                \Log::error('ERROR: model not save ' . json_encode($model));
            }
        }
    }
}
