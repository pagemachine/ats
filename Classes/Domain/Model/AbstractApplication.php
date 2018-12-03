<?php
namespace PAGEmachine\Ats\Domain\Model;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Application\ApplicationStatus;
use PAGEmachine\Ats\Domain\Model\Note;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Application
 * @codeCoverageIgnore
 */
class AbstractApplication extends AbstractEntity
{
    /**
     * Just prepare function so each child model can create its object storages
     *
     * @return void
     */
    public function initializeObject()
    {

        $this->notes = new ObjectStorage();
        $this->history = new ObjectStorage();
    }

    /**
     * @var PAGEmachine\Ats\Application\ApplicationStatus $status
     */
    protected $status;

    /**
     * @return PAGEmachine\Ats\Application\ApplicationStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param PAGEmachine\Ats\Application\ApplicationStatus $status
     * @return void
     */
    public function setStatus(ApplicationStatus $status)
    {
        $this->status = $status;
    }

    /**
     *
     *
     * @param string $status
     */
    public function setStatusPlain($status)
    {

        $this->status = ApplicationStatus::cast($status);
    }

    /**
     *
     *
     * @return string
     */
    public function getStatusPlain()
    {

        return $this->status->__toString();
    }


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\PAGEmachine\Ats\Domain\Model\Note>
     * @lazy
     */
    protected $notes;

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $notes
     * @return void
     */
    public function setNotes(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $notes)
    {
        $this->notes = $notes;
    }

    /**
     * @param \PAGEmachine\Ats\Domain\Model\Note $note
     * @return void
     */
    public function addNote(Note $note)
    {
        $this->notes->attach($note);
    }

    /**
     * @param \PAGEmachine\Ats\Domain\Model\Note $note
     * @return void
     */
    public function removeNote(Note $note)
    {
        $this->notes->detach($note);
    }

    /**
     * Returns the latest note (thanks to repository orderings it is always the first one)
     *
     * @return \PAGEmachine\Ats\Domain\Model\Note
     */
    public function getLatestNote()
    {

        $this->notes->rewind();
        return $this->notes->current();
    }


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\PAGEmachine\Ats\Domain\Model\History>
     * @lazy
     */
    protected $history;

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $history
     * @return void
     */
    public function setHistory(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $history)
    {
        $this->history = $history;
    }

    /**
     * @param \PAGEmachine\Ats\Domain\Model\History $historyEntry
     * @return void
     */
    public function addHistoryEntry(\PAGEmachine\Ats\Domain\Model\History $historyEntry)
    {
        $this->history->attach($historyEntry);
    }

    /**
     * @param \PAGEmachine\Ats\Domain\Model\History $historyEntry
     * @return void
     */
    public function removeHistoryEntry(\PAGEmachine\Ats\Domain\Model\History $historyEntry)
    {
        $this->history->detach($historyEntry);
    }

    /**
     * @var bool $anonymized
     */
    protected $anonymized;

    /**
     * @return bool
     */
    public function getAnonymized()
    {
        return $this->anonymized;
    }

    /**
     * @param bool $anonymized
     * @return void
     */
    public function setAnonymized($anonymized)
    {
        $this->anonymized = $anonymized;
    }
}
