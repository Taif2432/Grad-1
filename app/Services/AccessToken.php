<?php

namespace App\Services;

class AccessToken
{
    const VERSION = "006";

    public $appID;
    public $appCertificate;
    public $channelName;
    public $uid;
    public $expireTimestamp;

    public function __construct($appID, $appCertificate, $channelName, $uid, $expireTimestamp)
    {
        $this->appID = $appID;
        $this->appCertificate = $appCertificate;
        $this->channelName = $channelName;
        $this->uid = $uid;
        $this->expireTimestamp = $expireTimestamp;
    }

    public function build()
    {
        $content = $this->appID.$this->channelName.$this->uid.$this->expireTimestamp;
        $signature = hash_hmac('sha256', $content, $this->appCertificate, true);
        $signature = base64_encode($signature);

        return self::VERSION.$this->appID.$signature.$this->channelName.$this->uid.$this->expireTimestamp;
    }
}