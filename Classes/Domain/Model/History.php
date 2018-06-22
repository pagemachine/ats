<?php
namespace PAGEmachine\Ats\Domain\Model;

use PAGEmachine\Ats\Domain\Model\AbstractApplication;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Beuser\Domain\Model\BackendUser;
use TYPO3\CMS\Core\Utility\DiffUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * History
 * @codeCoverageIgnore
 */
class History extends AbstractEntity implements CloneableInterface
{
    /**
     * @var \PAGEmachine\Ats\Domain\Model\AbstractApplication $application
     */
    protected $application;

    /**
     * @return AbstractApplication
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param AbstractApplication $application
     * @return void
     */
    public function setApplication(AbstractApplication $application)
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
     * @var string $subject
     */
    protected $subject;

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

    /**
     * @var string $details
     */
    protected $details = "";

    /**
     * @return string
     */
    public function getDetails()
    {
        return unserialize($this->details);
    }

    /**
     * @param string $details
     * @return void
     */
    public function setDetails($details)
    {
        $this->details = serialize($details);
    }

    /**
     * @var string $historyData
     */
    protected $historyData = "";

    /**
     * @return string
     */
    public function getHistoryData()
    {
        return unserialize($this->historyData);
    }

    /**
     * @param string $historyData
     * @return void
     */
    public function setHistoryData($historyData)
    {
        $this->historyData = serialize($historyData);
    }

    /**
     * Cached diff result
     *
     * @var array
     */
    protected $diff = null;

    /**
     * Returns the diff
     *
     * @return array
     */
    public function getDiff()
    {
        if ($this->diff == null) {
            $historyData = $this->getHistoryData();

            $diffData = [];

            if (!empty($historyData['newRecord'])) {
                $diffUtility = GeneralUtility::makeInstance(DiffUtility::class);

                foreach ($historyData['newRecord'] as $key => $newRecord) {
                    $diffData[$key] = $diffUtility->makeDiffDisplay(
                        $this->getHistoryValue($key, $historyData['oldRecord'][$key]),
                        $this->getHistoryValue($key, $newRecord)
                    );
                }
            }

            $this->diff = $diffData;
        }

        return $this->diff;
    }

    /**
     * Returns a human readable output
     *
     * @param string $col
     * @param string $value
     *
     * @return string
     */
    protected function getHistoryValue($col, $value)
    {
        if ($value == 'NULL') {
            return '';
        }
        switch ($col) {
            case 'referrer':
            case 'forward_to_departments':
            case 'school_qualification':
            case 'salutation':
            case 'disability':
            case 'employed':
                $translation = LocalizationUtility::translate('tx_ats.label.'.$col.'.'.$value, 'ats');
                break;

            case 'rating_perso':
                $translation = LocalizationUtility::translate('tx_ats.application.rating.'.$value, 'ats');
                break;

            case 'birthday':
                $value = date('r', $value);
                break;

            default:
                $translation = LocalizationUtility::translate('tx_ats.application.'.$col.'.'.$value, 'ats');
                break;
        }

        if ($translation) {
            return $translation;
        }

        return BackendUtility::getProcessedValue("tx_ats_domain_model_application", $col, $value, 0, true);
    }
}
