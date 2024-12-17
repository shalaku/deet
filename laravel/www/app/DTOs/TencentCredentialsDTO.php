<?php

namespace App\DTOs;
class TencentCredentialsDTO
{
    public string $userId;
    public string $sdkAppId;
    public string $identifier;
    public string $userSig;
    public string $random;
    public string $status;


    public function __construct(
        string $userId,
        string $sdkAppId,
        string $identifier,
        string $userSig,
        string $random,
        string $status
    ) {
        $this->userId = $userId;
        $this->sdkAppId = $sdkAppId;
        $this->identifier = $identifier;
        $this->userSig = $userSig;
        $this->random = $random;
        $this->status = $status;
    }
}
