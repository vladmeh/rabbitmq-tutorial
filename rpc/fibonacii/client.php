<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Rpc\FibonacciRpcClient;

$fibonacci_rpc = new FibonacciRpcClient('rpc_queue');

$n = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 30;
try {
    $response = $fibonacci_rpc->call($n);
} catch (ErrorException $e) {
    echo $e->getMessage();
}
echo ' [.] Got ', $response, "\n";