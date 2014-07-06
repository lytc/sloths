<?php

namespace Sloths\Http\Client;

use Sloths\Http\Message\AbstractResponse;
use Sloths\Http\Message\Headers;

class Response extends AbstractResponse
{
    /**
     * @var array
     */
    protected $info;

    /**
     * @param array $info
     * @param string $output
     */
    public function __construct(array $info, $output)
    {
        if ($info) {
            $this->info = $info;
        }

        $this->setStatusCode($info['http_code']);

        $headerSize = $info['header_size'];
        $this->parseHeader(trim(substr($output, 0, $headerSize)));
        $this->body = substr($output, $headerSize);
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     *
     */
    protected function parseHeader($rawHeader)
    {
        $lines = explode("\r\n", $rawHeader);
        preg_match('/^http\/(1\.0|1\.1)/i', array_shift($lines), $matches);
        $this->protocolVersion = $matches[1];

        $headers = [];
        foreach ($lines as $line) {
            $header = explode(':', $line, 2);
            $headerName = Headers::processHeaderName($header[0]);
            $headers[$headerName] = trim($header[1]);
        }

        $this->setHeaders($headers);
    }

    /**
     * @return array|object|true|false|null
     * @throws \RuntimeException
     */
    public function toJson()
    {
        $args = func_get_args();
        array_unshift($args, $this->getBody());
        $data = call_user_func_array('json_decode', $args);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException('Unable to parse JSON: ' . json_last_error());
        }

        return $data;
    }

    /**
     * @return \SimpleXMLElement
     * @throws \RuntimeException
     */
    public function toXml()
    {
        $args = func_get_args();
        array_unshift($args, $this->getBody());

        $prevUseErrors = libxml_use_internal_errors(true);
        $prevDisableEntityLoader = libxml_disable_entity_loader(true);

        $data = call_user_func_array('simplexml_load_string', $args);
        $errors = [];
        if ($xmlErrors = libxml_get_errors()) {
            foreach ($xmlErrors as $error) {
                $errors[] = $error->message;
            }
        }

        libxml_use_internal_errors($prevUseErrors);
        libxml_disable_entity_loader($prevDisableEntityLoader);

        if ($errors) {
            throw new \RuntimeException('Unable to parse XML: ' . implode(', ', $errors));
        }

        return $data;
    }
}