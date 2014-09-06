<?php

namespace Sloths\Application\Exception;

class AccessDenied extends \Exception
{
    protected $code = 403;
    protected $message = 'Access denied';
}