<?php

namespace App\Listeners;

use App\Events\UserAuthorized;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveUserAuthTime
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserAuthorized  $event
     * @return void
     */
    public function handle(UserAuthorized $event)
    {
        if (empty($event->user->auth_history)) {
            $data = [Carbon::now()->toDateTimeString()];
        } else {
            $data = (array) $event->user->auth_history;
            array_push($data, [Carbon::now()->toDateTimeString()]);
        }
        $event->user->auth_history = $data;
        $event->user->save();
    }
}
