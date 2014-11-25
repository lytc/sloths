<?php

namespace Sloths\Application\Service;

class DateTime extends AbstractService
{
    /**
     * @var string
     */
    protected $defaultFormatDate = 'M d, Y';
    /**
     * @var string
     */
    protected $defaultFormatDateTime = 'M d, Y h:i:s A';

    /**
     * @param $format
     * @return $this
     */
    public function setDefaultFormatDate($format)
    {
        $this->defaultFormatDate = $format;
        return $this;
    }

    /**
     * @param $format
     * @return $this
     */
    public function setDefaultFormatDateTime($format)
    {
        $this->defaultFormatDateTime = $format;
        return $this;
    }

    /**
     * @param $input
     * @param null $format
     * @return string
     */
    public function formatDate($input, $format = null)
    {
        if (!$input) {
            return '';
        }

        if (!$format) {
            $format = $this->defaultFormatDate;
        }

        $dateTime = new \DateTime($input);
        return $dateTime->format($format);
    }

    /**
     * @param $input
     * @param null $format
     * @return string
     */
    public function formatDateTime($input, $format = null)
    {
        if (!$input) {
            return '';
        }

        if (!$format) {
            $format = $this->defaultFormatDateTime;
        }

        $dateTime = new \DateTime($input);
        return $dateTime->format($format);
    }
}