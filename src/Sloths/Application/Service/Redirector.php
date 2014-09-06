<?php

namespace Sloths\Application\Service;

class Redirector extends AbstractService
{
    /**
     * @param string $url
     * @param int $code
     * @return \Sloths\Http\ResponseInterface
     */
    public function to($url, $code = 302)
    {
        $response = $this->getApplication()->getResponse();
        $response->setStatusCode($code);
        $response->getHeaders()->set('Location', $url);

        return $response;
    }

    /**
     * @return \Sloths\Http\ResponseInterface
     */
    public function back()
    {
        return $this->to($this->getApplication()->getRequest()->getReferrer());
    }
}