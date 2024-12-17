<?php

namespace App\Jobs;

use App\Actions\MatchingOrderProcessAction;
use App\Actions\SendNotificationToUserAction;
use App\Models\Order\MatchingOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Cast\Cast;
use App\Models\Customer\Customer;
use Carbon\Carbon;
use App\Helpers\MailHelper;

class OrderExtendedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $matchingOrderId;
    public function __construct($matchingOrderId)
    {
        $this->matchingOrderId = $matchingOrderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Matching Order Id: ' . $this->matchingOrderId . ' => Processing Started...');

//        $this->testRequestDetailsCreate();
        $nofity = false;
        DB::beginTransaction();
        try {
            $matchingOrderData = MatchingOrder::where('id', $this->matchingOrderId)->first();

            if (empty($matchingOrderData)) {
                Log::info('Matching Order Id ' . $this->matchingOrderId . ' not found');
            } else {
                if ($matchingOrderData->end_date_time !== null) {
                    $nofity = true;
                }
            }

            DB::commit();

            if ($nofity) {

                $customer = Customer::find($matchingOrderData->customer_id);
                // $cast = Cast::find($checkRequest->cast_id);
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $matchingOrderData->planned_meeting_date_time);
                list($hours, $minutes) = explode(':', $matchingOrderData->planned_meeting_time);
                $endDateTime = (clone $startDateTime)->addHours($hours)->addMinutes($minutes);
                MailHelper::sendEmail(
                    config('notify.mail_address'),
                    '延長案件あり', 
                    '延長案件案件があります : ID'.$matchingOrderData->id."\n".
                    '開始予定時刻：'.$startDateTime."\n".
                    '終了予定時刻：'.$endDateTime."\n".
                    '時間：'.$matchingOrderData->planned_meeting_time."\n".
                    '希望女性会員人数：'.$matchingOrderData->number_of_people."\n".
                    '男性会員：'.$customer->name_ja."(".$customer->nickname.")"."\n".
                    '場所：'.$matchingOrderData->place_street."\n"
                );
                    
            }
            
            Log::info('Matching Order Id: ' . $this->matchingOrderId . ' => Processed...');

        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error('Matching Order Id: ' . $this->matchingOrderId . '. Error Message: '. $exception->getMessage());
        }
    }

    public function testRequestDetailsCreate(): void
    {
        RequestMatchingDetail::create([
            'matching_id' => $this->requestMatchingId,
            'cast_id' => 3,
            'status' => 510
        ]);
    }
}
