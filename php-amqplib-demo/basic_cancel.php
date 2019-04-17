<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Helper\Protocol\Wait091;

include(__DIR__ . '/../config/config.php');

$queue = 'msgs';
$consumerTag = 'consumer';

/*
 * Наблюдайте за выходом отладки, открывающим соединение. php-amqplib отправит таблицу возможностей на сервер
 * указывает, что он может получать и обрабатывать кадры basic.cancel, устанавливая поле
 * 'consumer_cancel_notify' к true.
 */
$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
$channel = $connection->channel();
$channel->queue_declare($queue);

$waitHelper = new Wait091();

$channel->basic_consume($queue, $consumerTag);
$channel->queue_delete($queue);

/*
 * если сервер также способен отправлять сообщения basic.cancel, этот вызов завершится исключением AMQPBasicCancelException.
 */
$channel->wait(array($waitHelper->get_wait('basic.cancel')));
