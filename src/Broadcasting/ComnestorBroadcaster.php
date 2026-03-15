<?php

namespace Ankurjha\Comnestor\Broadcasting;

use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        $channels = array_map(function ($channel) {
            return is_object($channel) ? $channel->name : $channel;
        }, $channels);

        $data = $payload['data'] ?? $payload;

        $dataJson = json_encode($data);

        $stringToSign = $this->appKey . $timestamp . $channels[0] . $event . $dataJson;

        $signature = hash_hmac('sha256', $stringToSign, $this->appSecret);

        try {

            $response = Http::post($this->baseUrl . '/api/events', [
                'app_key' => $this->appKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
                'channel' => $channels[0],
                'event' => $event,
                'data' => $data,
            ]);

            Log::info('Comnestor response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

        } catch (\Exception $e) {

            Log::error('Comnestor broadcast failed', [
                'error' => $e->getMessage()
            ]);

        }
    }
}