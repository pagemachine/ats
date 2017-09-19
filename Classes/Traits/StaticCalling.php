<?php
namespace PAGEmachine\Ats\Traits;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Trait for static method calls
 *
 * This is useful to make static method calls mockable in tests.
 * @author Mathias Brodala
 */
trait StaticCalling {

  /**
   * Performs a static method call
   *
   * @param string $classAndMethod Name of the class
   * @param string $methodName Name of the method
   * @param mixed $parameter,... Parameters to the method
   * @return mixed
   */
  protected function callStatic($className, $methodName, ...$parameters) {

    return call_user_func_array($className . '::' . $methodName, $parameters);
  }
}