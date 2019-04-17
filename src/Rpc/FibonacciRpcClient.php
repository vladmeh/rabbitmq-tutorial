<?php


namespace Rpc;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

include(__DIR__.'/../../config/config.php');

class FibonacciRpcClient extends AbstractRpcPublisher implements RpcPublisherInterface
{
    public function call(string $n)
    {
        $this->response = null;
        $this->corr_id = uniqid();

        //создаем сообщение
        $msg = new AMQPMessage(
            (string)$n,
            array(
                'correlation_id' => $this->corr_id,
                'reply_to' => $this->callback_queue
            )
        );

        // Публикуем сообщение
        $this->channel->basic_publish($msg, '', 'rpc_queue');

        while (!$this->response) {
            $this->channel->wait();
        }

        return intval($this->response);
    }
}