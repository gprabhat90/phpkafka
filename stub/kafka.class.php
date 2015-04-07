<?php

final class Kafka
{
    const OFFSET_BEGIN = 'beginning';
    const OFFSET_END = 'end';
    const LOG_ON = 1;
    const LOG_OFF = 0;

    /**
     * This property does not exist, connection status
     * Is obtained directly from C kafka client
     * @var bool
     */
    private $connected = false;

    /**
     * @var int
     */
    private $partition = 0;

    public function __construct($brokers = 'localhost:9092')
    {}

    /**
     * @param int $partition
     * @deprecated use setPartition instead
     * @return $this
     */
    public function set_partition($partition)
    {
        $this->partition = $partition;
        return $this;
    }

    /**
     * @param int $partition
     * @return $this
     * @throws \Exception
     */
    public function setPartition($partition)
    {
        if (!is_int($partition)) {
            throw new \Exception(
                sprintf(
                    '%s expects argument to be an int',
                    __CLASS__
                )
            );
        }
        $this->partition = $partition;
        return $this;
    }

    /**
     * @param int $level
     * @return $this
     * @throws \Exception (invalid argument)
     */
    public function setLogLevel($level)
    {
        if (!is_int($level)) {
            throw new Exception(
                sprintf(
                    '%s expects argument to be an int',
                    __METHOD__
                )
            );
        }
        if ($level != self::LOG_ON && $level != self::LOG_OFF) {
            throw new Exception(
                sprintf(
                    '%s argument invalid, use %s::LOG_* constants',
                    __METHOD__,
                    __CLASS__
                )
            );
        }
        //level is passed to kafka backend
        return $this;
    }

    /**
     * @param string $brokers
     * @return $this
     * @throws \Exception
     */
    public function setBrokers($brokers)
    {
        if (!is_string($brokers)) {
            throw new \Exception(
                sprintf(
                    '%s expects argument to be a string',
                    __CLASS__
                )
            );
        }
        $this->brokers = $brokers;
        return $this;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * produce message on topic
     * @param string $topic
     * @param string $message
     * @return $this
     */ 
    public function produce($topic, $message)
    {
        $this->connected = true;
        //internal call, produce message on topic
        return $this;
    }

    /**
     * @param string $topic
     * @param string|int $offset
     * @param string|int $count
     * @return array
     */
    public function consume($topic, $offset = self::OFFSET_BEGIN, $count = self::OFFSET_END)
    {
        $this->connected = true;
        $return = [];
        if (!is_numeric($offset)) {
            //0 or last message (whatever its offset might be)
            $start = $offset == self::OFFSET_BEGIN ? 0 : 100;
        } else {
            $start = $offset;
        }
        if (!is_numeric($count)) {
            //depending on amount of messages in topic
            $count = 100;
        }
        return array_fill_keys(
            range($start, $start + $count),
            'the message at the offset $key'
        );
    }

    /**
     * Returns an assoc array of topic names
     * The value is the partition count
     * @return array
     */
    public function getTopics()
    {
        return [
            'topicName' => 1
        ];
    }

    /**
     * @return bool
     */
    public function disconnect()
    {
        $this->connected = false;
        return true;
    }

    /**
     * Returns an array of ints (available partitions for topic)
     * @param string $topic
     * @return array
     */
    public function getPartitionsForTopic($topic)
    {
        return [];
    }

    /**
     * Returns an array where keys are partition
     * values are their respective beginning offsets
     * if a partition has offset -1, the consume call failed
     * @param string $topic
     * @return array
     * @throws \Exception when meta call failed or no partitions available
     */
    public function getPartitionOffsets($topic)
    {
        return [];
    }

    public function __destruct()
    {
        $this->connected = false;
    }
}
