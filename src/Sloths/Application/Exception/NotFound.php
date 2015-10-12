<?php

namespace Sloths\Application\Exception;

use Sloths\Http\Exception as HttpException;

class NotFound extends HttpException
{
    protected $code = 404;
    protected $message = 'Page not found';
}