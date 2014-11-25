<?php

namespace SlothsTest\Application\Service;

use Sloths\Application\Service\ServiceTrait;
use Sloths\Application\Service\Validator;
use Sloths\Validation\Validator\Chain;

/**
 * @covers Sloths\Application\Service\Validator
 */
class ValidatorTest extends TestCase
{
    public function testCreate()
    {
        $translator = $this->getMock('Sloths\Translation\TranslatorInterface');

        $validator = new Validator();
        $validator->setTranslator($translator);

        $chains = ['foo' => new Chain()];
        $newValidator = $validator->create($chains);

        $this->assertSame($translator, $newValidator->getTranslator());
        $this->assertSame($chains, $newValidator->getChains());

    }
}