<?php namespace Moota\SDK\Exceptions;

class MootaUnathorizedException extends \Exception
{
    // Redefine the exception so message isn't optional
    public function __construct(
        $message = null, $code = 0, Exception $previous = null
    )
    {
        $message = $message ? $message : "Moota SDK User is Not Authorized";
    
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}
