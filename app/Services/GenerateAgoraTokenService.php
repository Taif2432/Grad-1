<?php

namespace App\Services;

use Carbon\Carbon;

class GenerateAgoraTokenService
{
    protected $appId;
    protected $appCertificate;
    protected $expireTimeInSeconds;

    public function __construct()
    {
        $this->appId = config('agora.app_id');
        $this->appCertificate = config('agora.app_certificate');
        $this->expireTimeInSeconds = 3600; // 1 hour
    }

    public function generateToken($channelName, $uid)
    {
        // Dummy fallback if credentials are missing
        if (!$this->appId || !$this->appCertificate) {
            return 'dummy_token_' . $channelName;
        }

        $currentTimestamp = Carbon::now()->timestamp;
        $expireTimestamp = $currentTimestamp + $this->expireTimeInSeconds;

        // You would normally use Agora's SDK here, for now we'll simulate:
        return hash('sha256', $this->appId . $this->appCertificate . $channelName . $uid . $expireTimestamp);
    }
}