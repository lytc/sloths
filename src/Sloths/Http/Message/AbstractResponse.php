<?php

namespace Sloths\Http\Message;

class AbstractResponse extends AbstractMessage implements ResponseInterface
{
    # Informational 1xx
    const STATUS_CODE_100 = 100;
    const STATUS_CODE_101 = 101;

    # Successful 2xx
    const STATUS_CODE_200 = 200;
    const STATUS_CODE_201 = 201;
    const STATUS_CODE_202 = 202;
    const STATUS_CODE_203 = 203;
    const STATUS_CODE_204 = 204;
    const STATUS_CODE_205 = 205;
    const STATUS_CODE_206 = 206;

    # Redirection 3xx
    const STATUS_CODE_300 = 300;
    const STATUS_CODE_301 = 301;
    const STATUS_CODE_302 = 302;
    const STATUS_CODE_303 = 303;
    const STATUS_CODE_304 = 304;
    const STATUS_CODE_305 = 305;
    const STATUS_CODE_306 = 306;
    const STATUS_CODE_307 = 307;

    # Client Error 4xx
    const STATUS_CODE_400 = 400;
    const STATUS_CODE_401 = 401;
    const STATUS_CODE_402 = 402;
    const STATUS_CODE_403 = 403;
    const STATUS_CODE_404 = 404;
    const STATUS_CODE_405 = 405;
    const STATUS_CODE_406 = 406;
    const STATUS_CODE_407 = 407;
    const STATUS_CODE_408 = 408;
    const STATUS_CODE_409 = 409;
    const STATUS_CODE_410 = 410;
    const STATUS_CODE_411 = 411;
    const STATUS_CODE_412 = 412;
    const STATUS_CODE_413 = 413;
    const STATUS_CODE_414 = 414;
    const STATUS_CODE_415 = 415;
    const STATUS_CODE_416 = 416;
    const STATUS_CODE_417 = 417;

    # Server Error 5xx
    const STATUS_CODE_500 = 500;
    const STATUS_CODE_501 = 501;
    const STATUS_CODE_502 = 502;
    const STATUS_CODE_503 = 503;
    const STATUS_CODE_504 = 504;
    const STATUS_CODE_505 = 505;

    protected $reasonPhrases = [
        # Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        # Successful 2xx 
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        # Redirection 3xx 
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',

        # Client Error 4xx 
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        # Server Error 5xx 
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    ];

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @param int $code
     */
    public function setStatusCode($code)
    {
        $this->statusCode = (int) $code;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return null|string
     */
    public function getReasonPhrase()
    {
        $statusCode = $this->getStatusCode();
        return isset($this->reasonPhrases[$statusCode])? $this->reasonPhrases[$statusCode] : null;
    }

    /**
     * @return bool
     */
    public function isInformational()
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * @return bool
     */
    public function isRedirection()
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * @return bool
     */
    public function isClientError()
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * @return bool
     */
    public function isServerError()
    {
        return $this->statusCode >= 500;
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return $this->statusCode == self::STATUS_CODE_200;
    }

    /**
     * @return bool
     */
    public function isForbidden()
    {
        return $this->statusCode == self::STATUS_CODE_403;
    }

    /**
     * @return bool
     */
    public function isNotFound()
    {
        return $this->statusCode == self::STATUS_CODE_404;
    }

}