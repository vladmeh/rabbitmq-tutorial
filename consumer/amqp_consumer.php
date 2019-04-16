<?php

include(__DIR__ . '/../config/config.php');

use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;

$exchange = 'router';
$queue = 'msgs';
$consumerTag = 'consumer';

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
$channel = $connection->channel();

/*
    Следующий код одинаков как для потребителя, так и для производителя.
    Таким образом, мы уверены, что у нас всегда есть очередь для потребления и
        обмен где публиковать сообщения.
*/
/*
 * Создаем (объявляем) очередь
 *
    name: $queue
    passive: false
    durable: true // очередь переживет перезапуск сервера
    exclusive: false // очередь может быть доступна в других каналах
    auto_delete: false // очередь не будет удалена после закрытия канала.
*/
$channel->queue_declare($queue, false, true, false, false);

/*
 * Создаем (объявляем) обменник
 *
    name: $exchange
    type: direct
    passive: false
    durable: true // обмен переживет перезапуск сервера
    auto_delete: false //обмен не будет удален после закрытия канала.
*/
$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

/*
    Привязываем очередь к обменнику
*/
$channel->queue_bind($queue, $exchange);

/**
 * @param AMQPMessage $message
 */
function process_message(AMQPMessage $message)
{
    echo "\n--------\n";
    echo $message->body;
    echo "\n--------\n";

    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);

    // Отправить сообщение со строкой «quit» для отмены потребителя.
    if ($message->body === 'quit') {
        $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
    }
}

/*
    queue: очередь, откуда получать сообщения
    consumer_tag: идентификатор потребителя
    no_local: не получать сообщения, опубликованные этим потребителем.
    no_ack: если установлено значение true, этот потребитель будет использовать режим автоматического подтверждения. См. https://www.rabbitmq.com/confirms.html для получения подробной информации.
    exclusive: Запрос эксклюзивного доступа потребителя, то есть только этот потребитель может получить доступ к очереди
    nowait:
    callback: обратный вызов PHP функции
*/
$channel->basic_consume($queue, $consumerTag, false, false, false, false, 'process_message');

/**
 * @param AMQPChannel $channel
 * @param AbstractConnection $connection
 */
function shutdown(AMQPChannel $channel, AbstractConnection $connection)
{
    $channel->close();
    $connection->close();
}
register_shutdown_function('shutdown', $channel, $connection);

// Цикл, пока на канале зарегистрированы обратные вызовы
while (count($channel->callbacks)) {
    $channel->wait();
}