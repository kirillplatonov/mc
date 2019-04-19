<?php

class Mail
{
    protected $to;
    protected $subject;
    protected $message;
    protected $headers;
    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'From':
                $this->headers = "From: $arguments[0] \r\n";
            break;
            case 'To':
                $this->to = (string)$arguments[0];
            break;
            case 'Subject':
                $this->subject = (string)$arguments[0];
            break;
            case 'Body':
                $this->message = (string)$arguments[0];
            break;
            case 'Send': 
                return mail(
                    $this->to, $this->subject, $this->message, $this->headers
                );
            default:
                throw new LogicException(sprintf('Вызов неизвестного метода: "%s"', $name));
        }
    }
}
