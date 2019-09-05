<?php
namespace PAGEmachine\Ats\Application;

use PAGEmachine\Ats\Domain\Model\Job;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class ApplicationFilter
{

    /**
     * @var string
     */
    protected $orderField = null;

    /**
     * @return string
     */
    public function getOrderField()
    {
        return $this->orderField;
    }

    /**
     * @param string $orderField
     * @return void
     */
    public function setOrderField($orderField)
    {
        $this->orderField = $orderField;
    }


    /**
     * @var string
     */
    protected $direction = "ASC";

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     * @return void
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    public function __construct($orderField = null, $orderDirection = "ASC")
    {
        $this->orderField = $orderField;
        $this->orderDirection = $orderDirection;
    }
}
