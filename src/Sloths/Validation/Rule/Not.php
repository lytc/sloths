<?php

namespace Sloths\Validation\Rule;

class Not extends AbstractRule
{
    /**
     * @var AbstractRule
     */
    protected $rule;

    /**
     * @param AbstractRule $rule
     */
    public function __construct(AbstractRule $rule)
    {
        $this->rule = $rule;
        $this->messageTemplateKey = $rule->messageTemplateKey;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return 'Not' . $this->rule->getName();
    }

    /**
     * @return \Sloths\Translation\Translator
     */
    public function getTranslator()
    {
        return $this->rule->getTranslator();
    }

    /**
     * @return array
     */
    public function getDataForMessage()
    {
        return $this->rule->getDataForMessage();
    }

    /**
     * @param $value
     * @return bool
     */
    public function validate($value)
    {
        return !$this->rule->validate($value);
    }
}