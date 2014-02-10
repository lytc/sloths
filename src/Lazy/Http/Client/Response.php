<?php

namespace Lazy\Http\Client;

use Lazy\Http\Client\Exception\Exception;

class Response
{
    const TYPE_JSON = 'json';
    const TYPE_XML = 'xml';
    const TYPE_QUERY_STRING = 'query_string';

    protected $headers;
    protected $body;
    protected $dataType = 'json';
    protected $data;
    protected $dataArray;

    public function __construct($body, $headers = null)
    {
        $this->body = $body;
        $this->headers = $headers;
    }

    public function dataType($dataType = null)
    {
        if (!func_num_args()) {
            return $this->dataType;
        }

        $this->dataType = $dataType;
    }

    public function getRawBody()
    {
        return $this->body;
    }

    protected function unSerializeData()
    {
        if (null === $this->data) {
            switch ($this->dataType()) {
                case self::TYPE_JSON:
                    $this->data = json_decode($this->body);
                    break;

                case self::TYPE_XML:
                    $this->data = json_decode(json_encode(simplexml_load_string($this->body)));
                    break;

                case self::TYPE_QUERY_STRING:
                    parse_str($this->body, $data);
                    $this->data = json_decode(json_encode($data));
                    break;

                default:
                    throw new Exception(sprintf('Invalid data type %s', $this->dataType));
            }
        }

        return $this->data;
    }

    public function toArray()
    {
        if (!$this->dataArray) {
            $this->dataArray = json_decode(json_encode($this->unSerializeData()), true);
        }

        return $this->dataArray;
    }

    public function toObject()
    {
        return $this->unSerializeData();
    }

    public function __isset($name)
    {
        $this->unSerializeData();
        return property_exists($this->data, $name);
    }

    public function __get($name)
    {
        if ($this->__isset($name)) {
            return $this->data->{$name};
        }

        throw new Exception(sprintf('Call undefined property %s', $name));
    }
}