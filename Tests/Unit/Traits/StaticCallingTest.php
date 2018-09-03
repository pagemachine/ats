<?php
namespace PAGEmachine\Ats\Tests\Unit;

/*
 * This file is part of the PAGEmachine Ats project.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Tests\Unit\Traits\Fixtures\DerivedClass;

class StaticCallingTest extends UnitTestCase
{
    /**
     * @test
    */
    public function callStaticPassesParametersAndReturnsValue()
    {

        $object = new DerivedClass();

        $this->assertEquals('Hello World', $object->sayHello('World'));
        $this->assertEquals('Test', $object->returnValue('Test'));
    }
}
