<?php
namespace PAGEmachine\Ats\Domain\Model;

use PAGEmachine\Ats\Application\Note\NoteSubject;
use PAGEmachine\Ats\Domain\Model\Application;
use TYPO3\CMS\Beuser\Domain\Model\BackendUser;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Note
 * @codeCoverageIgnore
 */
class Note extends AbstractEntity implements CloneableInterface
{
    /**
     * @var \PAGEmachine\Ats\Domain\Model\Application $application
     */
    protected $application;

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param Application $application
     * @return void
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }


    /**
     * @var \TYPO3\CMS\Beuser\Domain\Model\BackendUser $user
     */
    protected $user;

    /**
     * @return BackendUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param BackendUser $user
     * @return void
     */
    public function setUser(BackendUser $user)
    {
        $this->user = $user;
    }


    /**
     * @var \DateTime $creationDate
     */
    protected $creationDate;

    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param \DateTime $creationDate
     * @return void
     */
    public function setCreationDate(\DateTime $creationDate)
    {
        $this->creationDate = $creationDate;
    }


    /**
     * @var \PAGEmachine\Ats\Application\Note\NoteSubject $subject
     */
    protected $subject = null;

    /**
     * @return \PAGEmachine\Ats\Application\Note\NoteSubject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param \PAGEmachine\Ats\Application\Note\NoteSubject $subject
     * @return void
     */
    public function setSubject(NoteSubject $subject)
    {
        $this->subject = $subject;
    }


    /**
     * @var bool $isInternal
     */
    protected $isInternal;

    /**
     * @return bool
     */
    public function getIsInternal()
    {
        return $this->isInternal;
    }

    /**
     * @param bool $isInternal
     * @return void
     */
    public function setIsInternal($isInternal)
    {
        $this->isInternal = $isInternal;
    }


    /**
     * @var string $details
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $details;

    /**
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param string $details
     * @return void
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }
}
