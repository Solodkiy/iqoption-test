<?php


namespace IqOptionTest\Transport;


use IqOptionTest\Message\MessageInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Envelope implements \JsonSerializable
{

    /**
     * @var MessageInterface
     */
    private $message;

    /**
     * @var null|string
     */
    private $answerTo;

    public function __construct(MessageInterface $message, ?string $answerTo)
    {
        $this->message = $message;
        $this->answerTo = $answerTo;
    }

    /**
     * @param MessageInterface $message
     * @param null|string $answerTo
     * @return Envelope
     */
    public static function create(MessageInterface $message, ?string $answerTo)
    {
        return new Envelope($message, $answerTo, null);
    }

    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'message_class' => get_class($this->message),
            'message' => $this->message->toArray(),
            'answer_to' => $this->answerTo,
        ];
    }

    /**
     * @return null|string
     */
    public function getAnswerTo()
    {
        return $this->answerTo;
    }
}