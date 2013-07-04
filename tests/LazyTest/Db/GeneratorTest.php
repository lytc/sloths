<?php

namespace LazyTest\Db;

use Lazy\Db\Generator;

class GeneratorTest extends TestCase
{
    public function test()
    {
        $generator = new Generator($this->connection);
        $generator->setDirectory(__DIR__ . '/../../')
            ->setNamespace('LazyTest\\Db\\Model\\Generate')
            ->setRootAbstractModelClassName('AbstractAppModel')
            ->setGenerateAbstractModel(true);

        $generator->generate();

        $this->assertTrue(class_exists('LazyTest\\Db\\Model\\Generate\\AbstractModel\\AbstractAppModel'));
        $this->assertTrue(class_exists('LazyTest\\Db\\Model\\Generate\\AbstractModel\\AbstractUser'));
        $this->assertTrue(class_exists('LazyTest\\Db\\Model\\Generate\\User'));
    }
}