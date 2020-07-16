<?php
declare(strict_types=1);

namespace Core\Mq;

use Core\Di\DiContainerInterface;
use Core\Result;
use ErrorException;
use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

/**
 * Class Rabbitmq
 * @package Core\Mq
 */
class Rabbitmq
{
    /**
     * @var DiContainerInterface
     */
    protected $di;

    /**
     * Rabbitmq constructor.
     * @param DiContainerInterface $di
     */
    public function __construct(DiContainerInterface $di)
    {
        $this->di = $di;
    }

    /**
     * @param string $queueId
     * @param array $message
     * @throws Exception
     */
    public function execute(string $queueId, array $message): void
    {
        $connection = new AMQPStreamConnection(
            getenv('RABBITMQ_HOST'),
            getenv('RABBITMQ_PORT'),
            getenv('RABBITMQ_USER'),
            getenv('RABBITMQ_PWD'),
            getenv('RABBITMQ_VHOST')
        );
        $channel = $connection->channel();
        $channel->queue_declare(
            $queueId,
            false,
            true,
            false,
            false
        );
        $msg = new AMQPMessage(
            json_encode($message),
            array('delivery_mode' => 2)
        );
        $channel->basic_publish(
            $msg,
            '',
            $queueId
        );
        $channel->close();
        $connection->close();
    }

    /**
     * @param string $queueId
     * @throws ErrorException
     * @throws Exception
     */
    public function receive(string $queueId)
    {
        $connection = new AMQPStreamConnection(
            '',
            getenv('RABBITMQ_PORT'),
            getenv('RABBITMQ_USER'),
            getenv('RABBITMQ_PWD'),
            getenv('RABBITMQ_VHOST')
        );
        $channel = $connection->channel();
        $channel->queue_declare(
            $queueId,
            false,
            true,
            false,
            false
        );
        $channel->basic_qos(
            null,
            1,
            null
        );
        $channel->basic_consume(
            $queueId,
            '',
            false,
            true,
            false,
            false,
            [$this, 'process']
        );
        while ($channel->is_consuming()) {
            echo ' [x] Wait... ', "\n";
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    /**
     * @param AMQPMessage $msg
     */
    public function process(AMQPMessage $msg)
    {
        echo ' [x] Received: ', $msg->getBody(), "\n";
        try {
            $data = json_decode($msg->getBody(), true);
            $class = $this->di->get($data['class']);
            $method = $data['method'];
            /** @var Result $result */
            $result = $class->$method($data['data']);
            if ($result->getErrorCode()) {
                echo ' [x] Error: ', $result->getErrorMessage(), "\n";
            } else {
                echo ' [x] Error: ', $result->getJsonData(), "\n";
            }
        } catch (Throwable $e) {
            echo ' [x] Error: ', $e->getMessage(), "\n";
        }
    }
}