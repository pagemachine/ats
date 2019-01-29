<?php
namespace PAGEmachine\Ats\TCA;

use PAGEmachine\Ats\Service\ExtconfService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * FormHelper class, provides TCA functions for fields related to be_users/groups.
 * Former class.tx_jobmodul_TCAform.php
 */
class FormHelper
{
    /**
     * populates array params[] with all Users who belong to a group that has role 'Z' (Perso)
     * required for creation of a new job in the sysfolder
     *
     *
     * @param array $params
     */
    public function findUserPa(&$params)
    {

         $params['items'] = $this->findUserByGroupRoleAndLocation(ExtconfService::getInstance()->getJobRoleDefinitions()['user_pa'], $params['row']['location']);
    }

    /**
     * populates array params[] with all Users who belong to a group that has role 'Berufung Sachbearbeiter'
     * required for creation of a new job in the sysfolder
     *
     *
     * @param array $params
     */
    public function findOfficials(&$params)
    {

         $params['items'] = $this->findUserByGroupRoleAndLocation(ExtconfService::getInstance()->getJobRoleDefinitions()['officials'], $params['row']['location']);
    }

    /**
     * populates array params[] with all Users who belong to a group that has role 'Berufung Mitwirkender'
     * required for creation of a new job in the sysfolder
     *
     *
     * @param array $params
     */
    public function findContributors(&$params)
    {

         $params['items'] = $this->findUserByGroupRoleAndLocation(ExtconfService::getInstance()->getJobRoleDefinitions()['contributors'], $params['row']['location']);
    }

    /**
     * Populates department usergroup array
     *
     * @param  array &$params
     * @return void
     */
    public function findDepartment(&$params)
    {

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_groups');
        $queryBuilder->select('uid', 'title')
            ->from('be_groups')
            ->where($queryBuilder->expr()->eq('tx_ats_location', $queryBuilder->createNamedParameter($params['row']['location'])));

        if (ExtensionManagementUtility::isLoaded('extbase_acl') && !empty(ExtconfService::getInstance()->getJobRoleDefinitions()['department'])) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('tx_extbaseacl_role', $queryBuilder->createNamedParameter(ExtconfService::getInstance()->getJobRoleDefinitions()['department']))
            );
        }

        $res = $queryBuilder->execute();

        while ($row = $res->fetch()) {
            $params['items'][] = [
                $row['title'],
                $row['uid'],
            ];
        }
    }

    /**
     * User finding function
     *
     * @param  string $role See field tx_ats_roles in be_groups
     * @param  string $location See field tx_ats_location in be_groups
     * @return array
     */
    protected function findUserByGroupRoleAndLocation($role, $location)
    {

        $groupsArray = [];

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_groups');
        $queryBuilder->select('uid')
            ->from('be_groups')
            ->where($queryBuilder->expr()->eq('tx_ats_location', $queryBuilder->createNamedParameter($location)));

        // Include extbase acl roles if available
        if (ExtensionManagementUtility::isLoaded('extbase_acl') && !empty($role)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq('tx_extbaseacl_role', $queryBuilder->createNamedParameter($role))
            );
        }

        $res = $queryBuilder->execute();

        while ($row = $res->fetch()) {
            $groupsArray[] = $row['uid'];
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_users');

        $queryBuilder->select("*")->from("be_users")->orderBy('realName');

        $orWhere = [];
        foreach ($groupsArray as $val) {
            $orWhere[] = $queryBuilder->expr()->inSet('usergroup', $queryBuilder->createNamedParameter($val));
        }

        if (!empty($orWhere)) {
            $queryBuilder->orWhere(...$orWhere);
        }

        $res = $queryBuilder->execute();

        $items = [];

        while ($row = $res->fetch()) {
            $items[] = array($row['realName'].' ('.$row['username'].')', $row['uid']);
        }
        return $items;
    }
}
