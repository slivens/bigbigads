<?php
namespace App\Exceptions;

class GenericException extends \ErrorException
{
    private $object;
    public function __construct($object, $message)
    {
        $this->object = $object;
        parent::__construct($message);
    }

    public function getObject()
    {
        return $this->object;
    }

}
