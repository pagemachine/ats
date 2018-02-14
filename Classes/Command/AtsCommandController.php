<?php
namespace PAGEmachine\Ats\Command;

use PAGEmachine\Ats\Domain\Model\Job;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/*
 * This file is part of the Pagemachine ATS Sync project.
 */


/**
 * Command Controller for various tasks
 */
class AtsCommandController extends CommandController
{

    /**
     * @var \PAGEmachine\Ats\Domain\Repository\JobRepository
     * @inject
     */
    protected $jobRepository;


    /**
     * @var \TYPO3\CMS\Extbase\Domain\Repository\BackendUserGroupRepository
     * @inject
     */
    protected $backendUserGroupRepository;

    /**
     * The pattern for new job groups
     *
     * @var string
     */
    protected $groupPattern = "bms_jobno_%s";

    protected $templateName = "bms department template %s";

    protected $createdGroups = 0;

    /**
     * Command to auto-create department usergroups for each new job
     *
     * @return void
     */
    public function createJobGroupsCommand()
    {
        $this->createdGroups = 0;

        foreach ($this->jobRepository->findJobsWithEndtimeInFuture() as $job) {

            $name = sprintf($this->groupPattern, $job->getJobNumber());
            $groups = $this->backendUserGroupRepository->countByTitle(
                $name
            );

            // Create new usergroup if it does not exist
            if ($this->backendUserGroupRepository->countByTitle($name) == 0) {
                $groupId = $this->cloneGroupTemplate($job, $name);
                $this->addGroupToJob($job, $groupId);
            }
        }

        $this->outputLine();
        $this->outputLine("Successfully checked for job groups. <options=bold>" . strval($this->createdGroups) . "</> groups were added.");
    }

    /**
     * Deletes all job groups
     *
     * @return void
     */
    public function cleanGroupsCommand()
    {
        $this->getDatabaseConnection()->exec_DELETEquery(
            'be_groups',
            'title LIKE "bms_jobno_%"'
        );
        $this->outputLine("Cleaned all groups.");
    }

    /**
     * Creates a new BE group based on a template
     *
     * @param  Job    $job
     * @param  string $newName
     * @return void
     */
    protected function cloneGroupTemplate(Job $job, $newName)
    {
        $title = sprintf($this->templateName, $job->getLocation());
        $template = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
            '*',
            'be_groups',
            'title = "' . $title . '"'
        );
        if ($template === false) {
            $this->outputLine('<error>Could not find template "' . $title . '". Group for job "' . $job->getJobNumber() . '" was not generated.</error>');
        }
        else {
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
                $this->createdGroups++;
                $this->outputLine("<info>New group created: " . $newName . "</info>");
                return $this->getDatabaseConnection()->sql_insert_id();
            }
            else {
                $this->outputLine("<error>Something went wrong with the insertion, group " . $newName . " was NOT created.");
            }
        }
    }

    protected function addGroupToJob(Job $job, $groupId)
    {
        $group = $this->backendUserGroupRepository->findByUid($groupId);

        $job->addDepartment($group);
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($group, __METHOD__, 2, defined('TYPO3_cliMode') || defined('TYPO3_REQUESTTYPE') && (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_CLI));
        $this->jobRepository->update($job);

    }

    public function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
