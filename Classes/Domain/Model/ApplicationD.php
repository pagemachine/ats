<?php
namespace PAGEmachine\Ats\Domain\Model;

/*
 * This file is part of the PAGEmachine ATS project.
 */


/**
 * ApplicationD
 * @codeCoverageIgnore
 */
class ApplicationD extends ApplicationC
{
    /**
     * @var string $comment
     */
    protected $comment;

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return void
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }


    /**
     * @var string $referrer
     */
    protected $referrer;

    /**
     * @return string
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * @param string $referrer
     * @return void
     */
    public function setReferrer($referrer)
    {
        $this->referrer = $referrer;
    }


    /**
     * @var int $forwardToDepartments
     */
    protected $forwardToDepartments;

    /**
     * @return int
     */
    public function getForwardToDepartments()
    {
        return $this->forwardToDepartments;
    }

    /**
     * @param int $forwardToDepartments
     * @return void
     */
    public function setForwardToDepartments($forwardToDepartments)
    {
        $this->forwardToDepartments = $forwardToDepartments;
    }
}
