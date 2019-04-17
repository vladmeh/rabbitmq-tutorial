<?php


namespace Rpc;


use PhpAmqpLib\Message\AMQPMessage;

interface RpcPublisherInterface
{
    public function onResponse(AMQPMessage $rep);

    public function call(string $value);
}