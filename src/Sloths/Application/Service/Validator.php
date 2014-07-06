<?php

namespace Sloths\Application\Service;

use Sloths\Translation\Translator;
use Sloths\Validation\Group;
use Sloths\Validation\Validator as V;

class Validator implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @var
     */
    protected $translator;

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return mixed
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param $name
     * @param $args
     * @return \Sloths\Validation\Rule\AbstractRule
     */
    public function __call($name, $args)
    {
        $rule = V::createRule($name, $args);

        if ($this->translator) {
            $rule->setTranslator($this->translator);
        }

        return $rule;
    }

    /**
     * @param array $rules
     * @return Group
     */
    public function create(array $rules)
    {
        $group = new Group($rules);

        if ($this->translator) {
            $group->setTranslator($this->translator);
        }

        return $group;
    }
}