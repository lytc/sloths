<?php

namespace SlothsTest\Validation;
use Sloths\Translation\Translator;
use Sloths\Validation\Rule;
use Sloths\Validation\Validator;
use SlothsTest\TestCase;

class ValidatorTest extends TestCase
{
    public function testCallStatic()
    {
        $rule = Validator::email();
        $this->assertInstanceOf('Sloths\Validation\Rule\Email', $rule);

        $rule = Validator::notEmail();
        $this->assertInstanceOf('Sloths\Validation\Rule\Not', $rule);
    }

    public function testCreateRule()
    {
        $this->assertInstanceOf('Sloths\Validation\Rule\Email', Validator::createRule('email'));
    }

    public function testExtendRule()
    {
        Validator::addNamespace(__NAMESPACE__);
        $this->assertInstanceOf(__NAMESPACE__ . '\Foo', Validator::createRule('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateRuleShouldThrowAnExceptionIfHaveNoRuleDefined()
    {
        Validator::createRule('unexistingrule');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateRuleShouldThrowAnExceptionWithInvalidRuleInterface()
    {
        Validator::addNamespace(__NAMESPACE__);
        Validator::createRule('Bar');
    }

    public function testDefaultTranslator()
    {
        $this->assertInstanceOf('Sloths\Translation\Translator', Validator::getDefaultTranslator());

        $translator = new Translator();
        Validator::setDefaultTranslator($translator);
        $this->assertSame($translator, Validator::getDefaultTranslator());
    }
}

class Foo extends Rule\AbstractRule
{
    public function validate($input) {}
}

class Bar {}