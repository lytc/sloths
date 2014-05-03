<?php

namespace Lazy\Application\Exception;

class NotFound extends \Exception
{
    protected $code = 404;
}