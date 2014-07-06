<?php

namespace Sloths\Validation\Rule;

class After extends AbstractExpectedRule
{
    /**
     * @var \DateTime
     */
    protected $expectedDate;

    /**
     * @var string
     */
    protected $format;

    /**
     * @param $expected
     * @param string $format
     * @throws \InvalidArgumentException
     */
    public function __construct($expected, $format = null)
    {
        parent::__construct($expected);

        if ($format) {
            $this->format = $format;
        }

        $this->expectedDate = Date::createDateTime($this->expected, $format);

        if (!$this->expectedDate) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 1 must be a valid date, %s given', gettype($expected))
            );
        }
    }

    public function getDataForMessage()
    {
        return [$this->expectedDate->format($this->format?: 'Y-m-d')];
    }

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        $input = Date::createDateTime($input, $this->format);
        return $input && $input > $this->expectedDate;
    }
}