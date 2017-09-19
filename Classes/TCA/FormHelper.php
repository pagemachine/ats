<?php
namespace PAGEmachine\Ats\TCA;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * FormHelper class, provides TCA functions for fields related to be_users/groups.
 * Former class.tx_jobmodul_TCAform.php
 */
class FormHelper {


    /**
     * populates array params[] with all Users who belong to a group that has role 'Z' (Perso)
     * required for creation of a new job in the sysfolder
     *
     *
     * @param array $params
     */
    public function findUserPa(&$params) {

         $params['items'] = $this->findUserByGroupRoleAndLocation($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['job']['roles']['user_pa'], $params['row']['location']);
    }

    /**
     * populates array params[] with all Users who belong to a group that has role 'Berufung Sachbearbeiter'
     * required for creation of a new job in the sysfolder
     *
     *
     * @param array $params
     */
    public function findOfficials(&$params) {

         $params['items'] = $this->findUserByGroupRoleAndLocation($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['job']['roles']['officials'], $params['row']['location']);
    }

    /**
     * populates array params[] with all Users who belong to a group that has role 'Berufung Mitwirkender'
     * required for creation of a new job in the sysfolder
     *
     *
     * @param array $params
     */
    public function findContributors(&$params) {

         $params['items'] = $this->findUserByGroupRoleAndLocation($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['job']['roles']['contributors'], $params['row']['location']);
    }

    /**
     * Populates department usergroup array
     *
     * @param  array &$params
     * @return void
     */
    public function findDepartment(&$params) {

        $where = implode('', [
            'be_groups.tx_ats_location = "' . $params['row']['location'] . '"',
            $this->getDeleteClause("be_groups"),
            $this->getBackendEnableFields("be_groups")
        ]);

        if (ExtensionManagementUtility::isLoaded('extbase_acl') && !empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['job']['roles']['department'])) {

            $where .= ' AND be_groups.tx_extbaseacl_role IN(' . $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['job']['roles']['department'] . ')';
        }
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery("uid, title", "be_groups", $where);

        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc ($res)) {

            $params['items'][] = [
                $row['title'],
                $row['uid']
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
    protected function findUserByGroupRoleAndLocation($role, $location) {

        $groupsArray = [];

        $where = 'be_groups.tx_ats_location = "'.$location.'"';

        // Include extbase acl roles if available
        if (ExtensionManagementUtility::isLoaded('extbase_acl') && !empty($role)) {

            $where .= ' AND be_groups.tx_extbaseacl_role = "' . $role . '"';
        }

        $where .= $this->getDeleteClause("be_groups") . $this->getBackendEnableFields("be_groups");


        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ("uid", "be_groups", $where);
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc ($res)) {
            $groupsArray[] = $row['uid'];
        }

        $where = ' ( 1=0 ';
        foreach($groupsArray as $val) {
            $where .= " OR FIND_IN_SET($val, `usergroup`)";
        }
        $where .= ')';

        $where .= $this->getDeleteClause("be_users") . $this->getBackendEnableFields("be_users");

        $orderBy = 'realName';
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery ("*", "be_users", $where, '', $orderBy);

        $items = [];

        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc ($res)) {
            $items[] = array($row['realName'].' ('.$row['username'].')', $row['uid']);
        }

        return $items;

    }

    /**
     * Helper function to mock static BackendUtility function
     * @codeCoverageIgnore
     *
     * @param  string $table
     * @return string
     */
    public function getDeleteClause($table)
    {
        return BackendUtility::deleteClause($table);
    }

    /**
     * Helper function to mock static BackendUtility function
     * @codeCoverageIgnore
     *
     * @param  string $table
     * @return string
     */
    public function getBackendEnableFields($table)
    {
        return BackendUtility::BEenableFields($table);
    }

}
