<?php
namespace PAGEmachine\Ats\Domain\Model;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Domain\Model\BackendUser;
use TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Job
 *
 * @codeCoverageIgnore
 */
class Job extends AbstractEntity implements \JsonSerializable
{
    use StructuredJobDefinitionTrait;

    /**
     * @var string $jobNumber
     */
    protected $jobNumber;


    /**
     * @var \DateTime $creationDate
     */
    protected $creationDate;

    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var string $description
     */
    protected $description;


    /**
     * @var string $descriptionAfterLink
     */
    protected $descriptionAfterLink;

    /**
     * @var string $contact
     */
    protected $contact;

    /**
     * @var string $career
     */
    protected $career;

    /**
     * @var bool $internal
     */
    protected $internal;

    /**
     * @var string $location
     */
    protected $location;

    /**
     * @var bool $deadlineEmailDisabled
     */
    protected $deadlineEmailDisabled;

    /**
     * @var \DateTime $deadlineEmail
     */
    protected $deadlineEmail;

    /**
     * @var string $organizationUnit
     */
    protected $organizationUnit;

    /**
     * @var bool $enableFormLink
     */
    protected $enableFormLink;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\BackendUser>
     * @lazy
     */
    protected $userPa;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup>
     * @lazy
     */
    protected $department;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup>
     * @lazy
     */
    protected $officials;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup>
     * @lazy
     */
    protected $contributors;


    /**
     * @var \DateTime $endtime
     */
    protected $endtime;

    /**
     * @return \DateTime
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * @param \DateTime $endtime
     * @return void
     */
    public function setEndtime(\DateTime $endtime)
    {
        $this->endtime = $endtime;
    }

    /**
    * @return void
    */
    public function initializeObject()
    {

        $this->userPa = new ObjectStorage();
        $this->department = new ObjectStorage();
        $this->officials = new ObjectStorage();
        $this->contributors = new ObjectStorage();
    }



    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     * @lazy
     */
    protected $categories;

    /**
     * @return ObjectStorage
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param ObjectStorage $categories
     * @return void
     */
    public function setCategories(ObjectStorage $categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param Category $category
     * @return void
     */
    public function addCategory(Category $category)
    {
        $this->categories->attach($category);
    }

    /**
     * @param Category $category
     * @return void
     */
    public function removeCategory(Category $category)
    {
        $this->categories->detach($category);
    }

    /**
     * @return string
     */
    public function getJobNumber()
    {
        return $this->jobNumber;
    }

    /**
     * @param string $jobNumber
     * @return void
     */
    public function setJobNumber($jobNumber)
    {
        $this->jobNumber = $jobNumber;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getJobNumberAndTitle()
    {
        return $this->jobNumber." - ".$this->title;
    }

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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<PAGEmachine\Ats\Domain\Model\FileReference>
     * @lazy
     */
    protected $media;

    /**
     * @return ObjectStorage
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param ObjectStorage $media
     * @return void
     */
    public function setMedia(ObjectStorage $media)
    {
        $this->media = $media;
    }

    /**
     * @param FileReference $media
     * @return void
     */
    public function addMedia(FileReference $media)
    {
        $this->media->attach($media);
    }

    /**
     * @param FileReference $media
     * @return void
     */
    public function removeMedia(FileReference $media)
    {
        $this->media->detach($media);
    }

    /**
     * @return string
     */
    public function getDescriptionAfterLink()
    {
        return $this->descriptionAfterLink;
    }

    /**
     * @param string $descriptionAfterLink
     * @return void
     */
    public function setDescriptionAfterLink($descriptionAfterLink)
    {
        $this->descriptionAfterLink = $descriptionAfterLink;
    }

    /**
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param string $contact
     * @return void
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return string
     */
    public function getCareer()
    {
        return $this->career;
    }

    /**
     * @param string $career
     * @return void
     */
    public function setCareer($career)
    {
        $this->career = $career;
    }

    /**
     * @return bool
     */
    public function getInternal()
    {
        return $this->internal;
    }

    /**
     * @param bool $internal
     * @return void
     */
    public function setInternal($internal)
    {
        $this->internal = $internal;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     * @return void
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return bool
     */
    public function getDeadlineEmailDisabled()
    {
        return $this->deadlineEmailDisabled;
    }

    /**
     * @param bool $deadlineEmailDisabled
     * @return void
     */
    public function setDeadlineEmailDisabled($deadlineEmailDisabled)
    {
        $this->deadlineEmailDisabled = $deadlineEmailDisabled;
    }

    /**
     * @return \DateTime
     */
    public function getDeadlineEmail()
    {
        return $this->deadlineEmail;
    }

    /**
     * @param \DateTime $deadlineEmail
     * @return void
     */
    public function setDeadlineEmail($deadlineEmail)
    {
        $this->deadlineEmail = $deadlineEmail;
    }

    /**
     * @return string
     */
    public function getOrganizationUnit()
    {
        return $this->organizationUnit;
    }

    /**
     * @param string $organizationUnit
     * @return void
     */
    public function setOrganizationUnit($organizationUnit)
    {
        $this->organizationUnit = $organizationUnit;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getUserPa()
    {
        return $this->userPa;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $userPa
     * @return void
     */
    public function setUserPa(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $userPa)
    {
        $this->userPa = $userPa;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\BackendUser $userPa
     * @return void
     */
    public function addUserPa(BackendUser $userPa)
    {
        $this->userPa->attach($userPa);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\BackendUser $userPa
     * @return void
     */
    public function removeUserPa(BackendUser $userPa)
    {
        $this->userPa->detach($userPa);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $department
     * @return void
     */
    public function setDepartment(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $department)
    {
        $this->department = $department;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup $department
     * @return void
     */
    public function addDepartment(BackendUserGroup $department)
    {
        $this->department->attach($department);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup $department
     * @return void
     */
    public function removeDepartment(BackendUserGroup $department)
    {
        $this->department->detach($department);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getOfficials()
    {
        return $this->officials;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $officials
     * @return void
     */
    public function setOfficials(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $officials)
    {
        $this->officials = $officials;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup $official
     * @return void
     */
    public function addOfficial(BackendUserGroup $official)
    {
        $this->officials->attach($official);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup $official
     * @return void
     */
    public function removeOfficial(BackendUserGroup $official)
    {
        $this->officials->detach($official);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getContributors()
    {
        return $this->contributors;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $contributors
     * @return void
     */
    public function setContributors(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $contributors)
    {
        $this->contributors = $contributors;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup $contributor
     * @return void
     */
    public function addContributor(BackendUserGroup $contributor)
    {
        $this->contributors->attach($contributor);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup $contributor
     * @return void
     */
    public function removeContributor(BackendUserGroup $contributor)
    {
        $this->contributors->detach($contributor);
    }

    /**
     * @return bool
     */
    public function getEnableFormLink()
    {
        return $this->enableFormLink;
    }

    /**
     * @param bool $enableFormLink
     * @return void
     */
    public function setEnableFormLink($enableFormLink)
    {
        $this->enableFormLink = $enableFormLink;
    }
}
