<?php
namespace PAGEmachine\Ats\Domain\Model;

use PAGEmachine\Ats\Domain\Model\Application;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Beuser\Domain\Model\BackendUser;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Note
 * @codeCoverageIgnore
 */
class Note extends AbstractEntity implements CloneableInterface {


	/**
	 * @var \PAGEmachine\Ats\Domain\Model\Application $application
	 */
	protected $application;
	
	/**
	 * @return Application
	 */
	public function getApplication() {
	  return $this->application;
	}
	
	/**
	 * @param Application $application
	 * @return void
	 */
	public function setApplication(Application $application) {
	  $this->application = $application;
	}


	/**
	 * @var \TYPO3\CMS\Beuser\Domain\Model\BackendUser $user
	 */
	protected $user;
	
	/**
	 * @return BackendUser
	 */
	public function getUser() {
	  return $this->user;
	}
	
	/**
	 * @param BackendUser $user
	 * @return void
	 */
	public function setUser(BackendUser $user) {
	  $this->user = $user;
	}


	/**
	 * @var \DateTime $creationDate
	 */
	protected $creationDate;
	
	/**
	 * @return \DateTime
	 */
	public function getCreationDate() {
	  return $this->creationDate;
	}
	
	/**
	 * @param \DateTime $creationDate
	 * @return void
	 */
	public function setCreationDate(\DateTime $creationDate) {
	  $this->creationDate = $creationDate;
	}


	/**
	 * @var string $subject
	 * @validate NotEmpty
	 */
	protected $subject;
	
	/**
	 * @return string
	 */
	public function getSubject() {
	  return $this->subject;
	}
	
	/**
	 * @param string $subject
	 * @return void
	 */
	public function setSubject($subject) {
	  $this->subject = $subject;
	}


    /**
     * @var boolean $isInternal
     */
    protected $isInternal;
    
    /**
     * @return boolean
     */
    public function getIsInternal()
    {
        return $this->isInternal;
    }
    
    /**
     * @param boolean $isInternal
     * @return void
     */
    public function setIsInternal($isInternal)
    {
        $this->isInternal = $isInternal;
    }


	/**
	 * @var string $details
	 * @validate NotEmpty
	 */
	protected $details;
	
	/**
	 * @return string
	 */
	public function getDetails() {
	  return $this->details;
	}
	
	/**
	 * @param string $details
	 * @return void
	 */
	public function setDetails($details) {
	  $this->details = $details;
	}

}
