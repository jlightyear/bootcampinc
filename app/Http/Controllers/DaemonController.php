<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Redis;

class DaemonController extends Controller
{
    /**
     * Publishes a 'stop' event to Redis Pub/Sub 'daemon-notify' channel
     *
     * @return Response
     */
    public function stopDaemon(){

        // TODO Preguntar per la configuració intrínsica de laravel: no funciona.
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->publish('daemon-notify', 'stop');

        $response = array('success' => true);
        return response()
            ->json($response)
            ->header('Content-Type', 'application/json');
    }

    /**
     * Publishes a 'reload' event to Redis Pub/Sub 'daemon-notify' channel
     *
     * @param array $trends
     * @return Response
     */
    public function updateTrends($trends = ['barcelona']){

        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->publish('daemon-notify', json_encode($trends));

        $response = array('success' => true);
        return response()
            ->json($response)
            ->header('Content-Type', 'application/json');
    }
}
