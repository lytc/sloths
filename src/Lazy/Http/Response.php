<?php

namespace Lazy\Http;

class Response
{
    protected $status = 200;
    protected $body = '';
    protected $headers = [];

    public function status($status = null)
    {
        if (!func_num_args()) {
            return $this->status;
        }

        $this->status = (int) $status;
        return $this;
    }

    public function body($body = null)
    {
        if (!func_num_args()) {
            return $this->body;
        }

        $this->body = $body;
        return $this;
    }

    public function headers($name = null, $value = null)
    {
        switch (func_num_args()) {
            case 0: return $this->headers;

            case 1:
                if (is_array($name)) {
                    $this->headers = array_merge(['Content-Type' => 'text/html'], $name);
                    return $this;
                }
                return isset($this->headers[$name])? $this->headers[$name] : null;

            case 2:
                $this->headers[$name] = $value;
        }

        return $this;
    }

    public function header()
    {
        return call_user_func_array([$this, 'headers'], func_get_args());
    }

    public function contentType($contentType = null)
    {
        if (!func_num_args()) {
            return $this->header('Content-Type');
        }

        return $this->header('Content-Type', $contentType);
    }

    public function redirect($url, $status = 302)
    {
        $this->status($status);
        $this->header('Location', $url);
        return $this;
    }

    public function isInvalid()
    {
        return $this->status < 100 || $this->status >= 600;
    }

    public function isInformational()
    {
        return $this->status >= 100 && $this->status < 200;
    }

    public function isSuccessful()
    {
        return $this->status >= 200 && $this->status < 300;
    }

    public function isRedirection()
    {
        return $this->status >= 300 && $this->status < 400;
    }

    public function isClientError()
    {
        return $this->status >= 400 && $this->status < 500;
    }

    public function isServerError()
    {
        return $this->status >= 500 && $this->status < 600;
    }

    public function isOk()
    {
        return $this->status == 200;
    }

    public function isBadRequest()
    {
        return $this->status == 400;
    }

    public function isForbidden()
    {
        return $this->status == 403;
    }

    public function isNotFound()
    {
        return $this->status == 404;
    }

    public function isMethodNotAllowed()
    {
        return $this->status == 405;
    }

    public function isUnprocessable()
    {
        return $this->status == 422;
    }

    public function send($status = null, $body = null)
    {
        if (!is_numeric($status)) {
            $body = $status;
            $status = null;
        }

        if (null !== $status) {
            $this->status($status);
        }

        if (null !== $body) {
            $this->body($body);
        }

        if (!is_string($this->body)) {
            $this->contentType('application/json');
        }

        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            if (null !== $value) {
                header("$name: $value");
                continue;
            }
            header("$name");
        }

        if ($this->isRedirection()) {
            return $this;
        }

        echo (is_string($this->body)? $this->body : json_encode($this->body));
    }
}