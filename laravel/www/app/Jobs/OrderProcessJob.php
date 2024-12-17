<?php

namespace App\Jobs;

use App\Actions\MatchingOrderProcessAction;
use App\Actions\SendNotificationToUserAction;
use App\Models\Order\RequestMatching;
use App\Models\Order\RequestMatchingDetail;
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

class OrderProcessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $requestMatchingId;
    public function __construct($requestMatchingId)
    {
        $this->requestMatchingId = $requestMatchingId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Request matching id: ' . $this->requestMatchingId . ' => Processing Started...');

//        $this->testRequestDetailsCreate();

        DB::beginTransaction();
        try {
            $requestMatchingData = RequestMatching::where('id', $this->requestMatchingId)->first();

            if (empty($requestMatchingData)) {
                Log::info('Request matching id ' . $this->requestMatchingId . ' not found');
            }
            $requestMatchingDetails = RequestMatchingDetail::where('matching_id', $this->requestMatchingId)->where('status', 510)->get();

            $requestCastCount  = $requestMatchingData->number_of_people;

            $castCount = count($requestMatchingDetails);

            if ($requestCastCount == $castCount) {
                $requestMatchingData->update([
                    'status' => 502,
                ]);

                $requestMatchingDetails->each(function ($detail) {
                    $detail->update(['status' => 512]);
                });

                (new MatchingOrderProcessAction())->execute($requestMatchingData, $requestMatchingDetails);

                SendNotificationToUserAction::sendNotificationToCustomerFromRequestMatching($requestMatchingData);
                SendNotificationToUserAction::sendNotificationToCastFormRequestMatchingDetails($requestMatchingDetails);

            } else {
                $requestMatchingData->update([
                    'status' => 504,
                ]);
            }

            DB::commit();

            if ($requestCastCount !== $castCount) {

                $customer = Customer::find($requestMatchingData->customer_id);
                // $cast = Cast::find($checkRequest->cast_id);
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $requestMatchingData->requested_start_time);
                list($hours, $minutes) = explode(':', $requestMatchingData->requested_matching_term);
                $endDateTime = (clone $startDateTime)->addHours($hours)->addMinutes($minutes);
                MailHelper::sendEmail(
                    config('notify.mail_address'),
                    '要対応コール案件あり', 
                    '人数調整が必要なコール案件があります : ID'.$requestMatchingData->id."\n".
                    '開始予定時刻：'.$startDateTime."\n".
                    '終了予定時刻：'.$endDateTime."\n".
                    '時間：'.$requestMatchingData->requested_matching_term."\n".
                    '希望女性会員人数：'.$requestMatchingData->number_of_people."\n".
                    '現在の応募人数：'.$castCount."\n".
                    '男性会員：'.$customer->name_ja."(".$customer->nickname.")"."\n".
                    '場所：'.$requestMatchingData->area_name."\n"
                );
                    
            }
            
            Log::info('Request matching id: ' . $this->requestMatchingId . ' => Processed...');

        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error('Request matching id: ' . $this->requestMatchingId . '. Error Message: '. $exception->getMessage());
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
