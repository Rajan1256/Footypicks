<?php

namespace App\Services;


use Throwable;

class ExceptionService extends \Exception
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        $message .= 'Service Exception:' . $message;
        parent::__construct($message, $code, $previous);
    }
}