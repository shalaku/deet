<?php

namespace App\Actions;

use App\DTOs\TencentCredentialsDTO;
use App\Helpers\TencentHelper;
use GuzzleHttp\Client;
use http\Client\Curl\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateUserForChatAction
{
    public function createChatUser($userId, $nick = '', $faceUrl = ''): TencentCredentialsDTO
    {
        $tencentCredentials = config('tencent.chat');
        $sdkAppId = $tencentCredentials['sdkAppId'];
        $identifier = $tencentCredentials['identifier'];
        $userSig = TencentHelper::generateUserSig($identifier);
        $random = random_int(11111111, 99999999);

        $url = $tencentCredentials['base_url'] . "/v4/im_open_login_svc/account_import";
        $queryString = "?sdkappid=" . $sdkAppId . "&identifier=" . $identifier . "&usersig=" . $userSig . "&random=" . $random . "&contenttype=json";

        $data = [
            "UserID" => $userId,
        ];

        if ($nick) {
            $data['Nick'] = $nick;
        }

        if ($faceUrl) {
            $data['FaceUrl'] = $faceUrl;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url . $queryString, $data);

            $responseBody = $response->body();
            \Log::debug('Tencent API Response: ' . $responseBody);

            $responseBody = json_decode($responseBody, true);

            if ($responseBody['ActionStatus'] == 'OK') {
                $userSig = TencentHelper::generateUserSig($userId);
                $this->sendMessageToUser($userId, 'Deetへようこそ。<br>こちらは運営直通チャットです。<br>質問や問い合わせはご気軽にメッセージください！');

                return new TencentCredentialsDTO(
                    userId: $userId,
                    sdkAppId: $sdkAppId,
                    identifier: $identifier,
                    userSig: $userSig,
                    random: $random,
                    status: "SUCCESS"
                );
            }

            return new TencentCredentialsDTO(
                userId: $userId,
                sdkAppId: $sdkAppId,
                identifier: $identifier,
                userSig: $userSig,
                random: $random,
                status: "FAILED"
            );
        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Error creating Tencent chat user: ' . $e->getMessage());

            return new TencentCredentialsDTO(
                userId: $userId,
                sdkAppId: $sdkAppId,
                identifier: $identifier,
                userSig: $userSig,
                random: $random,
                status: "FAILED"
            );
        }
    }

    public function updateUser($data)
    {
        $user = Auth::user();

        $tencentCredentials = config('tencent.chat');
        $sdkAppId = $tencentCredentials['sdkAppId'];
        $identifier = $tencentCredentials['identifier'];
        $userSig = TencentHelper::generateUserSig($identifier);
        $random = random_int(11111111, 99999999);

        $url = $tencentCredentials['base_url'] . "/v4/profile/portrait_set";
        $queryString = "?sdkappid=" . $sdkAppId . "&identifier=" . $identifier . "&usersig=" . $userSig . "&random=" . $random . "&contenttype=json";

        $requestBody = [];
        $requestBody['From_Account'] = $user['user_id'];
        $requestBody['ProfileItem'] = [];

        if (isset($data['nickname'])) {
            $requestBody['ProfileItem'][] = [
                "Tag" => "Tag_Profile_IM_Nick",
                "Value" => $data['nickname']
            ];
        }

        if (isset($data['avatar'])) {
            $requestBody['ProfileItem'][] = [
                "Tag" => "Tag_Profile_IM_Image",
                "Value" => $data['avatar']
            ];
        }

        $client = new Client();

        $response = $client->post($url . $queryString, [
            'json' => $requestBody,
        ]);

        $result = json_decode($response->getBody(), true);
        Log::debug('Tencent API Response: ', $result);

        return $result;
    }


    public function generateNewToken(string $userId): string
    {

    }

    public function sendMessageToUser($userId, $message)
    {
        $tencentCredentials = config('tencent.chat');
        $sdkAppId = $tencentCredentials['sdkAppId'];
        $random = random_int(0, 4294967295);
        $managerUser = $tencentCredentials['manager_user'];
        $userSig = TencentHelper::generateUserSig($managerUser);

        $url = $tencentCredentials['base_url'] . "/v4/openim/sendmsg";
        $queryString = "?sdkappid=" . $sdkAppId . "&identifier=" . $managerUser . "&usersig=" . $userSig . "&random=" . $random . "&contenttype=json";

        $client = new Client();

        $response = $client->post($url . $queryString, [
            'json' => [
                'To_Account' => $userId,
                'MsgRandom' => $random,
                'MsgBody' => [
                    [
                        'MsgType' => 'TIMTextElem',
                        'MsgContent' => [
                            'Text' => $message,
                        ],
                    ],
                ],
            ],
        ]);

        $result = json_decode($response->getBody(), true);
        Log::debug('Send Message to User: ' . $userId . ' Response: ' , $result);

        return $result;
    }
}
