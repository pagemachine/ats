<?php
namespace PAGEmachine\Ats\Hook;

use PAGEmachine\Ats\Service\ExtconfService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class DataHandlerJobGroups
{
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
            in_array($status, ['new', 'update'])
        ) {
            if (MathUtility::canBeInterpretedAsInteger($id)) {
                $job = $this->getJob($id);
            } else {
                $job = $fieldArray;
            }
            if (!empty($job['job_number']) && !empty($job['location'])) {
                $groupName = sprintf(ExtconfService::getInstance()->getJobGroupPattern(), $job['job_number']);

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
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_job');
        $queryBuilder->getRestrictions()->removeAll();
        $res = $queryBuilder->select('job_number', 'location', 'department')
            ->from('tx_ats_domain_model_job')
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter(intval($uid))))
            ->setMaxResults(1)
            ->execute();

        return $res->fetch();
    }

    /**
     * Either loads an existing group or creates a new one
     *
     * @param  string $name
     * @return string $groupName
     */
    public function ensureGroupForJob($name, $location)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_groups');
        $queryBuilder->getRestrictions()->removeAll();
        $res = $queryBuilder->select('uid')
            ->from('be_groups')
            ->where($queryBuilder->expr()->eq('title', $queryBuilder->createNamedParameter($name)))
            ->setMaxResults(1)
            ->execute();

        $group = $res->fetch();

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
        $title = sprintf(ExtconfService::getInstance()->getJobGroupTemplate(), $location);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_groups');
        $queryBuilder->getRestrictions()->removeAll();
        $res = $queryBuilder->select('*')
            ->from('be_groups')
            ->where($queryBuilder->expr()->eq('title', $queryBuilder->createNamedParameter($title)))
            ->setMaxResults(1)
            ->execute();
        $template = $res->fetch();

        if ($template !== false) {
            $newGroup = $template;
            $newGroup['title'] = $newName;
            $newGroup['tstamp'] = time();
            $newGroup['crdate'] = time();
            unset($newGroup['uid']);

            $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('be_groups');
            $result = $connection->insert('be_groups', $newGroup);

            if ($result) {
                return (int)$connection->lastInsertId('be_groups');
            } else {
                //Something went wrong with the insertion, group was NOT created.
                return false;
            }
        }
    }
}
