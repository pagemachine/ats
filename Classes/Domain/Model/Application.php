<?php
namespace PAGEmachine\Ats\Domain\Model;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Application\ApplicationStatus;

/**
 * Application
 * @codeCoverageIgnore
 */
class Application extends ApplicationE implements CloneableInterface
{
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
     * @var \DateTime $receiptdate
     */
    protected $receiptdate;

    /**
     * @return \DateTime
     */
    public function getReceiptdate()
    {
        return $this->receiptdate;
    }

    /**
     * @param \DateTime $receiptdate
     * @return void
     */
    public function setReceiptdate($receiptdate)
    {
        $this->receiptdate = $receiptdate;
    }


    /**
     * @var int $pool
     */
    protected $pool;

    /**
     * @return int
     */
    public function getPool()
    {
        return $this->pool;
    }

    /**
     * @param int $pool
     * @return void
     */
    public function setPool($pool)
    {
        $this->pool = $pool;
    }


    /**
     * @var int $applicationType
     */
    protected $applicationType;

    /**
     * @return int
     */
    public function getApplicationType()
    {
        return $this->applicationType;
    }

    /**
     * @param int $applicationType
     * @return void
     */
    public function setApplicationType($applicationType)
    {
        $this->applicationType = $applicationType;
    }

    /**
     * @var int $statusChange
     */
    protected $statusChange;

    /**
     * @return int
     */
    public function getStatusChange()
    {
        return $this->statusChange;
    }

    /**
     * @param int $statusChange
     * @return void
     */
    public function setStatusChange($statusChange)
    {
        $this->statusChange = $statusChange;
    }


    /**
     * @var \PAGEmachine\Ats\Application\ApplicationRating $rating
     */
    protected $rating;

    /**
     * @return \PAGEmachine\Ats\Application\ApplicationRating
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param \PAGEmachine\Ats\Application\ApplicationRating $rating
     * @return void
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }


    /**
     * @var \PAGEmachine\Ats\Application\ApplicationRating $ratingPerso
     */
    protected $ratingPerso;

    /**
     * @return \PAGEmachine\Ats\Application\ApplicationRating
     */
    public function getRatingPerso()
    {
        return $this->ratingPerso;
    }

    /**
     * @param \PAGEmachine\Ats\Application\ApplicationRating $ratingPerso
     * @return void
     */
    public function setRatingPerso($ratingPerso)
    {
        $this->ratingPerso = $ratingPerso;
    }


    /**
     * @var int $aip
     */
    protected $aip;

    /**
     * @return int
     */
    public function getAip()
    {
        return $this->aip;
    }

    /**
     * @param int $aip
     * @return void
     */
    public function setAip($aip)
    {
        $this->aip = $aip;
    }


    /**
     * @var bool $invited
     */
    protected $invited;

    /**
     * @return bool
     */
    public function getInvited()
    {
        return $this->invited;
    }

    /**
     * @param bool $invited
     * @return void
     */
    public function setInvited($invited)
    {
        $this->invited = $invited;
    }


    /**
     * @var int $opr
     */
    protected $opr;

    /**
     * @return int
     */
    public function getOpr()
    {
        return $this->opr;
    }

    /**
     * @param int $opr
     * @return void
     */
    public function setOpr($opr)
    {
        $this->opr = $opr;
    }


    /**
     * @var bool $anonym
     */
    protected $anonym;

    /**
     * @return bool
     */
    public function getAnonym()
    {
        return $this->anonym;
    }

    /**
     * @param bool $anonym
     * @return void
     */
    public function setAnonym($anonym)
    {
        $this->anonym = $anonym;
    }


    /**
     * @var bool $vocationalTrainingCompleted
     */
    protected $vocationalTrainingCompleted;

    /**
     * @return bool
     */
    public function getVocationalTrainingCompleted()
    {
        return $this->vocationalTrainingCompleted;
    }

    /**
     * @param bool $vocationalTrainingCompleted
     * @return void
     */
    public function setVocationalTrainingCompleted($vocationalTrainingCompleted)
    {
        $this->vocationalTrainingCompleted = $vocationalTrainingCompleted;
    }


    public function submit()
    {
        $this->setCreationDate(new \DateTime);

        if ($this->receiptdate == null) {
            $this->setReceiptdate(new \DateTime);
        }
        $this->setStatus(ApplicationStatus::cast(ApplicationStatus::NEW_APPLICATION));
    }
}
