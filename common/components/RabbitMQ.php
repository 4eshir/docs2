<?php

namespace common\components;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use yii\base\Component;

class RabbitMQ extends Component
{
    public $host;
    public $port;
    public $user;
    public $password;
    public $vhost;
    private $_connection;
    private $_channel;

    public function init()
    {
        parent::init();
        $this->_connection = new AMQPStreamConnection(
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->vhost
        );
        $this->_channel = $this->_connection->channel();
    }

    public function publish($queue, $message)
    {
        $this->_channel->queue_declare($queue, false, true, false, false);
        $msg = new AMQPMessage($message, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $this->_channel->basic_publish($msg, '', $queue);
    }

    public function consume($queue, $callback)
    {
        $this->_channel->queue_declare($queue, false, true, false, false);
        $this->_channel->basic_consume($queue, '', false, true, false, false, function ($msg) use ($callback) {
            call_user_func($callback, $msg->body);
        });
        while ($this->_channel->is_consuming()) {
            $this->_channel->wait();
        }
    }

    public function __destruct()
    {
        $this->_channel->close();
        $this->_connection->close();
    }
}