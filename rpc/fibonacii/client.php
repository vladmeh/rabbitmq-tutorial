<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Rpc\FibonacciRpcClient;

$fibonacci_rpc = new FibonacciRpcClient();

$n = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 30;
$response = $fibonacci_rpc->call($n);
echo ' [.] Got ', $response, "\n";