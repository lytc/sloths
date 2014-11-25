<?php

namespace SlothsTest\Db\Model\Relation;

class TestCase extends \SlothsTest\TestCase
{
    public function setUp()
    {
        parent::setUp();

        require_once __DIR__ . '/stub-relation.php';
    }
}