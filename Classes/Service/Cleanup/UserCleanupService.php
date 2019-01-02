<?php
namespace PAGEmachine\Ats\Service\Cleanup;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class UserCleanupService
{
    /**
     * @return AnonymizationService
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    /**
     * Cleans up users of given group and minimum age
     *
     * @param  int $userGroup
     * @param  string $loginOlderThan
     * @return int $affectedUsers
     */
    public function cleanupUsers($userGroup, $loginOlderThan)
    {
        $threshold = new \DateTime();
        $threshold->sub(
            \DateInterval::createFromDateString($loginOlderThan)
        );

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('fe_users');

        // Remove hidden and deleted records as well
        $queryBuilder->getRestrictions()->removeAll();

        /**
         * Deletes all users which did not login in the specified period
         */
        $affectedRows = $queryBuilder
            ->delete('fe_users')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->lt('lastlogin', $threshold->getTimestamp()),
                    $queryBuilder->expr()->inSet('usergroup', (int)$userGroup)
                )
            )
            ->execute();

        return $affectedRows;
    }
}
