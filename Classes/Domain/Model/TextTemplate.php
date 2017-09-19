<?php
namespace PAGEmachine\Ats\Domain\Model;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * TextTemplate
 * @codeCoverageIgnore
 */
class TextTemplate extends AbstractEntity
{
    /**
     * @var string $type
     */
    protected $texttemplate;

    /**
     * @var int $type
     */
    protected $type;

    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var string $subject
     */
    protected $subject;

    /**
     * @return string
     */
    public function getTexttemplate()
    {
        return $this->texttemplate;
    }

    /**
     * @param string $textTemplate
     * @return void
     */
    public function setTexttemplate($texttemplate)
    {
        $this->texttemplate = $texttemplate;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
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
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return void
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
}
