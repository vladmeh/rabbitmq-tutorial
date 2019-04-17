<?php

use Rpc\MessageRpcPublisher;

require_once __DIR__ . '/../vendor/autoload.php';

$rpc = new MessageRpcPublisher('rpc_queue');

$message = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'hello';

try {
    $response = $rpc->call($message);
} catch (ErrorException $e) {
    echo $e->getMessage();
}

echo ' [.] Consumer: ', $response, "\n";
