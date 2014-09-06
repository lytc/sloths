<?php

namespace SlothsTest\Application\Service;

class TestCase extends \SlothsTest\TestCase
{
    public function getMockApplication($mockConfigLoader = true)
    {
        $application = $this->getMock('Sloths\Application\ApplicationInterface');

        if ($mockConfigLoader) {
            $mockConfigLoader = $this->getMock('configLoader', ['apply']);
            $application->expects($this->any())->method('getConfigLoader')->willReturn($mockConfigLoader);
        }

        return $application;
    }
}