<?php

namespace App\Helpers;

use Tencent\TLSSigAPIv2;

class TencentHelper
{
    public static function generateUserSig($identifier, $expire = 86400 * 180)
    {
        $tencentCredentials = config('tencent.chat');
        $sdkAppId = $tencentCredentials['sdkAppId'];
        $secretKey = $tencentCredentials['secretKey'];

        $api = new TLSSigAPIv2($sdkAppId, $secretKey);

        $sig = $api->genSig($identifier, $expire);

        return $sig;
    }
}
