<?php

include (__DIR__. '/../config/config.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS);
$channel = $connection->channel();

$channel->queue_declare('rpc_queue', false, false, false, false);

echo " [x] Ожидание запросов RPC\n";

$callback = function ($req) {
    echo ' [x] Получил: ', $req->body, "\n";

    $msg = new AMQPMessage(
        'OK',
        [
            'correlation_id' => $req->get('correlation_id')
        ]
    );

    $req->delivery_info['channel']->basic_publish(
        $msg,
        '',
        $req->get('reply_to')
    );

    $req->delivery_info['channel']->basic_ack(
        $req->delivery_info['delivery_tag']
    );
};

$channel->basic_qos(
    null,
    1,
    null
);

$channel->basic_consume(
    'rpc_queue',
    '',
    false,
    false,
    false,
    false,
    $callback
);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
