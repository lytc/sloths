<?php

namespace Sloths\Http;

class Client
{
    /**
     * @var Client\Request
     */
    protected $request;

    /**
     * @var resource
     */
    protected $curl;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $defaultCurlOptions = [
        CURLOPT_HEADER => true,
        CURLINFO_HEADER_OUT => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
    ];

    /**
     * @param string|Client\Request $request
     */
    public function __construct($request = null)
    {
        if ($request) {
            if ($request instanceof Client\Request) {
                $this->setRequest($request);
            } else {
                $this->getRequest()->setUrl($request);
            }
        }
    }

    /**
     * @return resource
     */
    public function getCurl()
    {
        if (!$this->curl) {
            $this->curl = curl_init();
        }

        return $this->curl;
    }

    /**
     * @param $baseUrl
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * @param $url
     * @return string
     */
    protected function processUrl($url)
    {
        if (0 === strpos($url, 'http://') || 0 === strpos($url, 'https://')) {
            return $url;
        }

        return $this->baseUrl . '/' . $url;
    }

    /**
     * @param Client\Request $request
     * @return $this
     */
    public function setRequest(Client\Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return Client\Request
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = new Client\Request();
        }

        return $this->request;
    }

    protected function createPostFields(array $files, array $params)
    {
        $fields = [];
        # params
        foreach ($params as $name => $value) {
            $fields[] = 'Content-Disposition: form-data; name="' . $name . '"' . "\r\n\r\n" . $value;
        }

        #files
        $fInfo = finfo_open(FILEINFO_MIME_TYPE);
        foreach ($files as $name => $file) {
            $mimeType = finfo_file($fInfo, $file, FILEINFO_MIME_TYPE);
            $data = file_get_contents($file);

            $fields[] = 'Content-Disposition: form-data; name="' . $name . '"; filename="' . pathinfo($file, PATHINFO_BASENAME) . '"' . "\r\n"
                        . 'Content-Type: ' . $mimeType . "\r\n\r\n"
                        . $data;
        }

        finfo_close($fInfo);

        # generate boundary
        while (true) {
            $boundary = '----SlothsClientFormBoundary' . md5(mt_rand() . microtime());
            foreach ($fields as $field) {
                if (false !== strpos($field, $boundary)) {
                    continue;
                }
            }

            break;
        }

        return [
            'boundary' => $boundary,
            'fields' => '--' . $boundary . "\r\n" . implode("\r\n--" . $boundary . "\r\n", $fields) . "\r\n--" . $boundary . '--'
        ];
    }

    /**
     * @param resource $curl
     * @param array $options
     * @return Client\Response
     * @throws \RuntimeException
     */
    protected function doRequest($curl, $options)
    {
        curl_setopt_array($curl, $options);
        $output = curl_exec($curl);

        if ($errNo = curl_errno($curl)) {
            throw new \RuntimeException(curl_error($curl), $errNo);
        }

        $info = curl_getinfo($curl);

        $response = new Client\Response($info, $output);

        curl_close($this->curl);
        $this->curl = null;

        return $response;
    }

    public function send($request = null)
    {
        if ($request) {
            if ($request instanceof Client\Request) {
                $this->setRequest($request);
            } else {
                $this->getRequest()->setUrl($request);
            }
        }


        $request = $this->getRequest();
        $curl = $this->getCurl();

        $options = $this->defaultCurlOptions;

        # url opt
        curl_setopt($curl, CURLOPT_URL, $request->getUrl());

        # http version opt
        $httpVersion = $request->getProtocolVersion() == '1.1'? CURL_HTTP_VERSION_1_1 : CURL_HTTP_VERSION_1_0;
        curl_setopt($curl, CURLOPT_HTTP_VERSION, $httpVersion);

        # headers
        $headers = $request->getHeaders()->getLines();

        # method opt
        $methodValue = true;
        $body = $request->getBody();

        switch ($method = $request->getMethod()) {
            case Client\Request::METHOD_GET:
                $curlMethod = CURLOPT_HTTPGET;
                break;

            case Client\Request::METHOD_POST:
                $curlMethod = CURLOPT_POST;
                if ($body) {
                    $options[CURLOPT_POSTFIELDS] = $body;
                } else {
                    $params = $request->getParams()->toArray();
                    if ($files = $request->getParamsFile()->toArray()) {
                        $result = $this->createPostFields($files, $params);
                        $headers[] = 'Content-Type: multipart/form-data; boundary=' . $result['boundary'];
                        $options[CURLOPT_POSTFIELDS] = $result['fields'];
                    } else {
                        $options[CURLOPT_POSTFIELDS] = $params;
                    }
                }
                break;

            case Client\Request::METHOD_PUT:

                if (is_resource($body)) {
                    $options[CURLOPT_INFILE] = $body;
                    $curlMethod = CURLOPT_UPLOAD;
                } else {
                    $curlMethod = CURLOPT_CUSTOMREQUEST;
                    $options[CURLOPT_POSTFIELDS] = $body;
                    $methodValue = $method;
                }

                break;

            default:
                $curlMethod = CURLOPT_CUSTOMREQUEST;
                $methodValue = $method;
        }

        curl_setopt($curl, $curlMethod, $methodValue);
        $options[CURLOPT_HTTPHEADER] = $headers;

        return $this->doRequest($curl, $options);
    }

    public function options($url, array $query = null, array $headers = null)
    {
        $request = $this->getRequest();
        $request->setMethod(Client\Request::METHOD_OPTIONS)->setUrl($url);

        if ($query) {
            $request->setParamsQuery($query);
        }

        if ($headers) {
            $request->setHeaders($headers);
        }

        return $this;
    }

    /**
     * @param string $url
     * @param array $query
     * @param array $headers
     * @return static
     */
    public function get($url, array $query = null, $headers = null)
    {
        $request = new Client\Request();

        $request->setMethod(Client\Request::METHOD_GET)->setUrl($this->processUrl($url));

        if ($query) {
            $request->setParamsQuery($query);
        }

        if ($headers) {
            $request->setHeaders($headers);
        }

        $this->setRequest($request);

        return $this;
    }

    /**
     * @param string $url
     * @param array $query
     * @param array $headers
     * @return static
     */
    public function head($url, array $query = null, $headers = null)
    {
        $request = new Client\Request();

        $request->setMethod(Client\Request::METHOD_HEAD)->setUrl($this->processUrl($url));

        if ($query) {
            $request->setParamsQuery($query);
        }

        if ($headers) {
            $request->setHeaders($headers);
        }

        $this->setRequest($request);

        return $this;
    }

    /**
     * @param string $url
     * @param array $params
     * @param array $headers
     * @return static
     */
    public function post($url, $params = null, $headers = null)
    {
        $request = new Client\Request();

        $request->setMethod(Client\Request::METHOD_POST)->setUrl($this->processUrl($url));

        if (is_string($params)) {
            $request->setBody($params);
        } else {
            $request->setParams($params);
        }

        if ($headers) {
            $request->setHeaders($headers);
        }

        $this->setRequest($request);

        return $this;
    }

    /**
     * @param string $url
     * @param mixed $body
     * @param array $headers
     * @return Client\Request
     */
    public function put($url, $body = null, $headers = null)
    {
        $request = new Client\Request();

        $request->setMethod(Client\Request::METHOD_PUT)->setUrl($this->processUrl($url));

        if ($body) {
            $request->setBody($body);
        }

        if ($headers) {
            $request->setHeaders($headers);
        }

        $this->setRequest($request);

        return $this;
    }

    /**
     * @param string $url
     * @param mixed $body
     * @param array $headers
     * @return Client\Request
     */
    public function patch($url, $body = null, $headers = null)
    {
        $request = new Client\Request();

        $request->setMethod(Client\Request::METHOD_PATCH)->setUrl($this->processUrl($url));

        if ($body) {
            $request->setBody($body);
        }

        if ($headers) {
            $request->setHeaders($headers);
        }

        $this->setRequest($request);

        return $this;
    }

    /**
     * @param string $url
     * @param mixed $query
     * @param array $headers
     * @return Client\Request
     */
    public function delete($url, $query = null, $headers = null)
    {
        $request = new Client\Request();

        $request->setMethod(Client\Request::METHOD_DELETE)->setUrl($this->processUrl($url));

        if ($query) {
            $request->setParamsQuery($query);
        }

        if ($headers) {
            $request->setHeaders($headers);
        }

        $this->setRequest($request);

        return $this;
    }

    /**
     * @param string $url
     * @param string $files
     * @param array $headers
     * @return static
     */
    public function upload($url, $files, $headers = null)
    {
        $request = new Client\Request();

        $request->setMethod(Client\Request::METHOD_POST)->setUrl($this->processUrl($url));

        if (!is_array($files)) {
            $files = [$files];
        }

        $request->setParamsFile($files);

        if ($headers) {
            $request->setHeaders($headers);
        }

        $this->setRequest($request);

        return $this;
    }
}