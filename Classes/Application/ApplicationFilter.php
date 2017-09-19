<?php
namespace PAGEmachine\Ats\Application;

use PAGEmachine\Ats\Domain\Model\Job;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class ApplicationFilter {


    /**
     * @var Job $job
     */
    protected $job = null;

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param Job $job
     * @return void
     */
    public function setJob(Job $job = null)
    {
        $this->job = $job;
    }


    /**
     * @var string $searchword
     */
    protected $searchword = "";

    /**
     * @return string
     */
    public function getSearchword()
    {
        return $this->searchword;
    }

    /**
     * @param string $searchword
     * @return void
     */
    public function setSearchword($searchword = "")
    {
        $this->searchword = $searchword;
    }


    /**
     * @var array $possibleSearchfields
     */
    protected $possibleSearchfields = [
        'uid',
        'firstname',
        'surname'
    ];

    /**
     * @return array
     */
    public function getPossibleSearchfields()
    {
        return $this->possibleSearchfields;
    }

    /**
     * @param array $possibleSearchfields
     * @return void
     */
    public function setPossibleSearchfields($possibleSearchfields)
    {
        $this->possibleSearchfields = $possibleSearchfields;
    }


    /**
     * @var array $searchfields
     */
    protected $searchfields = [];

    /**
     * @return array
     */
    public function getSearchfields()
    {
        return $this->searchfields;
    }

    /**
     * @param array $searchfields
     * @return void
     */
    public function setSearchfields($searchfields = [])
    {
        $this->searchfields = $searchfields;
    }

    /**
     * @return void
     */
    public function __construct()
    {
        $this->setSearchfields(
            $this->getPossibleSearchfields()
        );

    }

}
