<?php

namespace Sloths\Http;
use Sloths\Misc\Parameters;

class Request extends AbstractRequest
{
    /**
     * @var Parameters
     */
    protected $serverVars;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @return Parameters
     */
    public function getServerVars()
    {
        if (!$this->serverVars) {
            $this->serverVars = new Parameters($_SERVER?: []);
        }

        return $this->serverVars;
    }

    /**
     * @return Headers
     */
    public function getHeaders()
    {
        if (!$this->headers) {
            $headers = [];
            foreach ($this->getServerVars() as $name => $value) {
                if ('HTTP_' == substr($name, 0, 5)) {
                    $headers[substr($name, 5)] = $value;
                }
            }
            $this->headers = new Headers($headers);
        }

        return $this->headers;
    }

    /**
     * @return string
     */
    public function getOriginalMethod()
    {
        return $this->getServerVars()->get('REQUEST_METHOD');
    }

    /**
     * @return mixed|string
     */
    public function getMethod()
    {
        if (!$this->method) {
            $originalMethod = $this->getOriginalMethod();

            if ($originalMethod == self::METHOD_POST) {
                $this->method = $this->getHeaders()->get('X_HTTP_METHOD_OVERRIDE')?: $this->getParams()->get('_method');
            }

            $this->method = $this->method?: $originalMethod;
        }

        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if (!$this->path) {
            $serverVars = $this->getServerVars();
            $path = $serverVars->get('PATH_INFO')?: parse_url($serverVars->get('REQUEST_URI'), PHP_URL_PATH);
            $this->setPath($path);
        }

        return $this->path;
    }

    /**
     * @return \Sloths\Misc\Parameters
     */
    public function getParamsQuery()
    {
        if (!$this->paramsQuery) {
            $this->paramsQuery = new Parameters($_GET?: []);
        }

        return $this->paramsQuery;
    }

    /**
     * @return \Sloths\Misc\Parameters
     */
    public function getParamsPost()
    {
        if (!$this->paramsPost) {
            $params = $_POST?: [];
            if ($this->getOriginalMethod() == 'PUT') {
                parse_str(file_get_contents('php://input'), $params);
            }
            $this->paramsPost = new Parameters($params);
        }

        return $this->paramsPost;
    }

    /**
     * @return \Sloths\Misc\Parameters
     */
    public function getParamsFile()
    {
        if (!$this->paramsFile) {
            $this->paramsFile = new Parameters($_FILES? $this->mapFiles($_FILES) : []);
        }

        return $this->paramsFile;
    }

    /**
     * @param array $files
     * @return array
     */
    protected function mapFiles(array $files)
    {
        $result = array();
        foreach ($files as $name => $data) {
            foreach ($data as $param => $v) {
                $this->restructureFiles($result,
                    $name,
                    $files[$name][$param],
                    $param);
            }
        }

        return $result;
    }

    /**
     * @param $result
     * @param $name
     * @param $value
     * @param $param
     */
    protected function restructureFiles(&$result, $name, $value, $param)
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $this->restructureFiles($result[$name],
                    $k,
                    $v,
                    $param);
            }
        } else {
            $result[$name][$param] = $value;
        }
    }

    /**
     * @return string
     */
    public function getReferrer()
    {
        return $this->getHeaders()->get('REFERER');
    }

    /**
     * @return string
     */
    public function getServerName()
    {
        return $this->getServerVars()->get('SERVER_NAME');
    }

    /**
     * @param bool $withPort
     * @return string
     */
    public function getHost($withPort = false)
    {
        if (!$this->host) {
            $host = $this->getHeaders()->get('HOST');

            if ($host) {
                $parts = explode(':', $host);
                $host = $parts[0];
            } else {
                $host = $this->getServerName();
            }

            $this->host = $host;
        }

        if ($withPort) {
            return $this->host . ':' . $this->getPort();
        }

        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        if (null === $this->port) {
            $this->port = $this->getServerVars()->get('SERVER_PORT');
        }

        return $this->port;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        $url = $this->getScheme() . '://' . $this->getHost();
        $port = $this->getPort();

        if ($port != 80 && $port != 443) {
            $url .= ':' . $port;
        }

        return $url;
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getUrl($full = true)
    {
        $url = '';

        if ($full) {
            $url = $this->getBaseUrl();
        }


        $url .= $this->getServerVars()->get('REQUEST_URI');
        $url = rtrim($url, '/')?: '/';

        return $url;
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        return $this->getServerVars()->get('REMOTE_ADDR');
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->getServerVars()->get('USER_AGENT');
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return $this->getServerVars()->get('HTTPS') == 'on';
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->isSecure()? 'https' : 'http';
    }

    /**
     * @return bool
     */
    public function isXhr()
    {
        return 'XMLHttpRequest' == $this->getHeaders()->get('X_REQUESTED_WITH');
    }
}