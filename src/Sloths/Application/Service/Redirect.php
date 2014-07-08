<?php

namespace Sloths\Application\Service;

class Redirect implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @param string $url
     * @param int $code
     * @return \Sloths\Http\Response
     */
    public function to($url, $code = 302)
    {
        $response = $this->application->response;
        $response
            ->setStatusCode($code)
            ->getHeaders()->set('Location', $url);

        return $response;
    }

    /**
     * @return \Sloths\Http\Response
     */
    public function back()
    {
        return $this->to($this->application->request->getReferrer());
    }
}