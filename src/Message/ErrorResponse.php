<?php


namespace IqOptionTest\Message;


class ErrorResponse implements MessageInterface
{
    /**
     * @var string
     */
    private $errorText;

    public function __construct(string $errorText)
    {
        $this->errorText = $errorText;
    }

    public function toArray(): array
    {
        return [
            'error' => $this->errorText,
        ];
    }

    public static function fromArray(array $array): MessageInterface
    {
        return new self($array['error']);
    }

    /**
     * @return string
     */
    public function getErrorText(): string
    {
        return $this->errorText;
    }
}