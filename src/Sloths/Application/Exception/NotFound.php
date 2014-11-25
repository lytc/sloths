<?php

namespace Sloths\Application\Exception;

class NotFound extends \Exception
{
    protected $code = 404;
    protected $message = 'Page not found';
}