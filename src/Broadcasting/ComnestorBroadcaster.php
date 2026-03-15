<?php

namespace Ankurjha\Comnestor\Broadcasting;

use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Support\Facades\Http;

class ComnestorBroadcaster extends Broadcaster
{

    protected $baseUrl;
    protected $appKey;
    protected $appSecret;

    public function __construct($config)
    {
        $this->baseUrl = rtrim($config['base_url'], '/');
        $this->appKey = $config['app_key'];
        $this->appSecret = $config['app_secret'];
    }

    public function auth($request)
    {
        return $this->validAuthenticationResponse($request, []);
    }

    public function validAuthenticationResponse($request, $result)
    {
        return response()->json($result);
    }

    public function broadcast(array $channels, $event, array $payload = [])
    {

        $timestamp = time();

        $dataJson = json_encode($payload);

        $stringToSign = $this->appKey.$timestamp.implode(',', $channels).$event.$dataJson;

        $signature = hash_hmac('sha256', $stringToSign, $this->appSecret);

        Http::post($this->baseUrl.'/api/events', [

            'app_key' => $this->appKey,
            'timestamp' => $timestamp,
            'signature' => $signature,
            'channels' => $channels,
            'event' => $event,
            'data' => $payload,

        ]);

    }

}