<?php

namespace App\Console\Commands;

use App\Actions\SendNotificationToUserAction;
use App\Http\Controllers\API\Order\OrderController;
use App\Models\Customer\Customer;
use App\Models\Optimization;
use App\Models\Order\MatchingOrder;
use App\Models\Order\MatchingOrderDetail;
use app\Services\ImageHandleService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MeetingReminderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meeting:reminder-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '30 in before meeting notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentTime = Carbon::now();
        $timeMinus30Minutes = $currentTime->subMinutes(30);

        //Get Confirm Order
        $matchingOrder = MatchingOrder::where('planned_meeting_date_time' ,'<=', $timeMinus30Minutes)
            ->where('planned_meeting_date_time', '>=', $currentTime)
            ->where('status', 601)
            ->get();

        (new SendNotificationToUserAction())->sendBeforeMeetingNotification(
            'customer',
            $matchingOrder,
            [
                'title' => 'Meeting Reminder.',
                'body' => 'The meeting is scheduled to start in 30 minutes.'
            ]
        );

        foreach ($matchingOrder as $order) {
            $matchingOrderDetails = MatchingOrderDetail::where('order_id' , $order->id)->where('order_acception_status', 701)->get();
            (new SendNotificationToUserAction())->sendBeforeMeetingNotification(
                'cast',
                $matchingOrderDetails,
                [
                    'title' => 'Meeting Reminder.',
                    'body' => 'The meeting is scheduled to start in 30 minutes.'
                ]
            );
        }

    }
}
