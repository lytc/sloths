<?php

namespace Sloths\Application\Exception;

use Sloths\Http\Exception as HttpException;

class AccessDenied extends HttpException
{
    protected $code = 403;
    protected $message = 'Access denied';
}