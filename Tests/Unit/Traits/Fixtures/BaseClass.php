<?php
namespace PAGEmachine\Ats\Tests\Unit\Traits\Fixtures;

/*
 * This file is part of the PAGEmachine Ats project.
 */

class BaseClass {

    /**
     * @param string $subject
     * @return string
    */
    public function sayHello($subject) {

        return 'Hello ' . $subject;
    }
}