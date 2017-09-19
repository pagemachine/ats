<?php
namespace PAGEmachine\Ats\Tests\Unit\Traits\Fixtures;

/*
 * This file is part of the PAGEmachine Ats project.
 */

use PAGEmachine\Ats\Traits\StaticCalling;

class DerivedClass extends BaseClass
{
    use StaticCalling;

    /**
     * @param string $subject
     * @return string
    */
    public function sayHello($subject)
    {

        return $this->callStatic(parent::class, 'sayHello', $subject);
    }

    /**
     * @param string $value
     * @return mixed
    */
    public function returnValue($value)
    {

        return $this->callStatic(UtilityClass::class, 'returnValue', $value);
    }
}
