<?php
namespace PAGEmachine\Ats\Hook;

use PAGEmachine\Ats\Service\ExtconfService;
use TYPO3\CMS\Core\Utility\MathUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class DataHandlerJobGroups
{
    /**
     * The pattern for new job groups
     *
     * @var string
     */
    protected $groupPattern = "bms_jobno_%s";

    protected $templateName = "bms department template %s";

    /**
     *
     *
     * @param  string $status
     * @param  string $table
     * @param  int|string $id
     * @param  array &$fieldArray
     * @param  DataHandler $dataHandler
     * @return void
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $dataHandler)
    {
        if ($table == 'tx_ats_domain_model_job' &&
            ExtconfService::getInstance()->getCreateJobGroups() &&
            MathUtility::canBeInterpretedAsInteger($id) &&
            in_array($status, ['new', 'update'])
        ) {
            $job = $this->getJob($id);
            if (!empty($job['job_number'])) {
                $groupName = sprintf($this->groupPattern, $job['job_number']);

                $groupId = $this->ensureGroupForJob($groupName, $job['location']);

                if ($groupId) {
                    $fieldArray['department'] = $fieldArray['department'] ?: $job['department'];

                    if (!in_array(strval($groupId), explode(",", $fieldArray['department']))) {
                        $fieldArray['department'] = empty($fieldArray['department']) ? $groupId : ($fieldArray['department'] . "," . $groupId);
                    }
                }
            }
        }
    }

    /**
     * Returns a job row
     *
     * @param  int $uid
     * @return array
     */
    public function getJob($uid)
    {
        $job = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
            'job_number, location, department',
            'tx_ats_domain_model_job',
            'uid = ' . intval($uid)
        );
        return $job;
    }

    /**
     * Either loads an existing group or creates a new one
     *
     * @param  string $name
     * @return string $groupName
     */
    public function ensureGroupForJob($name, $location)
    {
        $group = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
            'uid',
            'be_groups',
            'title = "' . $name . '"'
        );

        if ($group === false) {
            return $this->cloneGroupTemplate($name, $location);
        }
        return $group['uid'];
    }

    /**
     * Creates a new BE group based on a template
     *
     * @param  Job    $job
     * @param  string $newName
     * @return int $groupId The ID of the newly created group
     */
    public function cloneGroupTemplate($newName, $location)
    {
        $title = sprintf($this->templateName, $location);
        $template = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
            '*',
            'be_groups',
            'title = "' . $title . '"'
        );
        if ($template !== false) {
            $newGroup = $template;
            $newGroup['title'] = $newName;
            $newGroup['tstamp'] = time();
            $newGroup['crdate'] = time();
            unset($newGroup['uid']);

            $result = $this->getDatabaseConnection()->exec_INSERTquery(
                'be_groups',
                $newGroup
            );

            if ($result === true) {
                return $this->getDatabaseConnection()->sql_insert_id();
            } else {
                //Something went wrong with the insertion, group was NOT created.
                return false;
            }
        }
    }

    public function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
