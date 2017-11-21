<?php
namespace PAGEmachine\Ats\ViewHelpers\Tool;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 *  Returns the value of the array key
 */
class ArrayKeyToValueViewHelper extends AbstractViewHelper
{
  /**
   *  @param array  $array
   *  @param string $key
   *  @param boolean $flipArray
   */
    protected function render($array, $key, $flipArray = false)
    {
        if($flipArray) $array = array_flip( $array );
        return $array[$key];
    }
}
