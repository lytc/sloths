<?php

namespace Sloths\Application\Exception;

use Sloths\Exception\HttpException;

class NotFound extends HttpException
{
    protected $code = 404;
}