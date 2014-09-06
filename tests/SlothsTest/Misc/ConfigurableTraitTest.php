<?php

namespace SlothsTest\Misc;

use Sloths\Misc\ConfigurableTrait;
use SlothsTest\TestCase;


/**
 * @covers Sloths\Misc\ConfigurableTrait
 */
class ConfigurableTraitTest extends TestCase
{
    public function testLoadConfigFromFile()
    {
        $file = __DIR__ . '/fixtures/configurabletrait-test.php';
        $foo = new Foo();
        $foo->loadConfigFromFile($file);
        $this->assertTrue(in_array($file, get_included_files()));
    }
}

class Foo
{
    use ConfigurableTrait;
}