<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Ratchet\Client\WebSocket;
use Illuminate\Support\Facades\Log;

class ShellyWebSocketService
{
    protected $accessToken;

    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function connectToShellyWebSocket()
    {
        $url = "wss://shelly-119-eu.shelly.cloud:6113/shelly/wss/hk_sock?t={$this->accessToken}";

        \Ratchet\Client\connect($url)->then(
            function (WebSocket $conn) {
                print_r('Connected to Shelly WebSocket <br>');

                Log::info('Connected to Shelly WebSocket');

                $conn->on('message', function ($message) use ($conn) {
//                    print_r('On message <br>');
                    $this->handleMessage($message);
                });

                $conn->on('close', function ($code = null, $reason = null) {
                    print_r('Connection closed <br>');

                    Log::warning("Connection closed ({$code} - {$reason})");
                });
            },
            function (\Exception $e) {
                print_r("Could not connect to WebSocket: {$e->getMessage()} <br>");
                Log::error("Could not connect to WebSocket: {$e->getMessage()}");
            }
        );
    }

    protected function handleMessage($message)
    {
        $decodedMessage = json_decode($message, true);

        if (isset($decodedMessage["event"]) && $decodedMessage['event'] === 'Shelly:StatusOnChange') {
            $this->handleStatusChange($decodedMessage);
        } else {
            print_r("Received unknown event: <br>");
            Log::info('Received unknown event: ', $decodedMessage);
        }
    }

    protected function handleStatusChange($eventData)
    {
        Log::info('Device status changed: ', $eventData);

        print_r("Device status changed: <br>");
        print_r("<pre>");
        dump($eventData);
        print_r("</pre>");

        User::create([
            "name" => "name",
            "email" => Str::uuid()->toString(),
            "password" => "123",
        ]);

        // Process the status change event (e.g., update database or notify users)
    }

    protected function reconnect()
    {
        sleep(5); // Wait for 5 seconds before reconnecting
        $this->connectToShellyWebSocket();
    }
}
