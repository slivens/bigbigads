<?php
namespace App\Exceptions;

class GenericException extends \ErrorException
{
    const ERROR_CODE_SHOULD_UPGRADE = -4100;
    const ERROR_CODE_SHOULD_SIGNIN = -4199;
    const ERROR_CODE_GENERIC_SEARCH_ERROR = -4200;
    const ERROR_CODE_SHOULD_ACTIVATE_EMAIL = -4999;
    const ERROR_CODE_USER_FREEZED = -5000;
    const ERROR_CODE_SHOULD_AGREE_TERM = -5001;

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
    static public function desc($code) {
    }
}
