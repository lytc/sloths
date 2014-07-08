<?php

namespace SlothsTest\Application\Service;

use Sloths\Application\Service\Validator;
use Sloths\Translation\Translator;
use SlothsTest\TestCase;

class ValidatorTest extends TestCase
{
    public function testCreateSimpleRule()
    {
        $translator = new Translator();
        $validator = new Validator();
        $validator->setTranslator($translator);

        $this->assertSame($translator, $validator->getTranslator());

        $rule = $validator->email();

        $this->assertInstanceOf('Sloths\Validation\Rule\Email', $rule);
        $this->assertSame($translator, $rule->getTranslator());
    }

    public function testMethodCreate()
    {
        $translator = new Translator();
        $validator = new Validator();
        $validator->setTranslator($translator);

        $group = $validator->create([]);
        $this->assertInstanceOf('Sloths\Validation\Group', $group);
        $this->assertSame($translator, $group->getTranslator());
    }
}