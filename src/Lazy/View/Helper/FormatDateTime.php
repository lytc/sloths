<?php

namespace Lazy\View\Helper;

class FormatDateTime extends AbstractHelper
{
    /**
     * @var string
     */
    protected static $defaultInputFormat = 'Y-m-d H:i:s';

    /**
     * @var string
     */
    protected static $defaultOutputFormat = 'F d, Y h:i:s A';

    /**
     * @var string
     */
    protected $inputFormat;

    /**
     * @var string
     */
    protected $outputFormat;

    /**
     * @var string|int|\DateTime
     */
    protected $value;

    /**
     * @param string $format
     */
    public static function setDefaultInputFormat($format)
    {
        static::$defaultInputFormat = $format;
    }

    /**
     * @return string
     */
    public static function getDefaultInputFormat()
    {
        return static::$defaultInputFormat;
    }

    /**
     * @param string $format
     */
    public static function setDefaultOutputFormat($format)
    {
        static::$defaultOutputFormat = $format;
    }

    /**
     * @return string
     */
    public static function getDefaultOutputFormat()
    {
        return static::$defaultOutputFormat;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function setInputFormat($format)
    {
        $this->inputFormat = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getInputFormat()
    {
        return $this->inputFormat?: static::$defaultInputFormat;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function setOutputFormat($format)
    {
        $this->outputFormat = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getOutputFormat()
    {
        return $this->outputFormat?: static::$defaultOutputFormat;
    }

    /**
     * @param string|number|\DateTime $value
     * @return $this
     */
    public function formatDateTime($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        return call_user_func_array([$this, 'formatDateTime'], func_get_args());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $value = $this->value;

        if (!$value instanceof \DateTime) {
            if (is_numeric($value)) {
                return date($this->getOutputFormat(), $value);
            }

            $value = \DateTime::createFromFormat($this->getInputFormat(), $value);
        }

        if (!$value) {
            return '';
        }

        return $value->format($this->getOutputFormat());
    }
}