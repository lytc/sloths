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

    public function testJsonSerializeWithValid()
    {
        $validator = $this->getMock('Sloths\Application\Service\Validator', ['fails']);
        $validator->expects($this->once())->method('fails')->willReturn(false);

        $this->assertSame(json_encode(['success' => true]), json_encode($validator));
    }

    public function testJsonSerializeWithFails()
    {
        $validator = $this->getMock('Sloths\Application\Service\Validator', ['fails', 'getMessages']);
        $validator->expects($this->once())->method('fails')->willReturn(true);
        $validator->expects($this->once())->method('getMessages')->willReturn('foo');

        $this->assertSame(json_encode(['success' => false, 'messages' => 'foo']), json_encode($validator));
    }
}