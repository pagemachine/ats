<?php
namespace PAGEmachine\Ats\Service\Cleanup;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class ApplicationCleanupService
{
    /**
     * @var FileRepository
     */
    protected $fileRepository;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @return AnonymizationService
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    /**
     * @param FileRepository|null $fileRepository
     */
    public function __construct(FileRepository $fileRepository = null, QueryBuilder $queryBuilder = null)
    {
        $this->fileRepository = $fileRepository ?: GeneralUtility::makeInstance(FileRepository::class);
        $this->queryBuilder = $queryBuilder ?: GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_application');

        // Show hidden and deleted records as well
        $this->queryBuilder->getRestrictions()->removeAll();
    }

    /**
     * Anonymizes applications
     *
     * @param string $olderThan Defines how long unfinished applications are kept
     * @return void
     */
    public function cleanupUnfinishedApplications($olderThan = '30 days')
    {
        $unfinishedThreshold = new \DateTime();
        $unfinishedThreshold->sub(
            \DateInterval::createFromDateString($olderThan)
        );

        /**
         * Deletes all applications which are unfinished and older than 30 days
         */
        $statement = $this->queryBuilder
            ->select('uid')
            ->from('tx_ats_domain_model_application')
            ->where(
                $this->queryBuilder->expr()->andX(
                    $this->queryBuilder->expr()->lt('crdate', $unfinishedThreshold->getTimestamp()),
                    $this->queryBuilder->expr()->eq('status', 0)
                )
            )
            ->execute();

        $affectedRows = 0;

        while ($row = $statement->fetch()) {
            $affectedRows += $this->cleanupApplicationRow((int)$row['uid']);
        }
        return $affectedRows;
    }

    /**
     * Removes a single application
     *
     * @param  int $applicationUid The application ID to delete
     * @return int $affectedRows The amount of rows affected (should be 0 or 1 of course)
     */
    public function cleanupApplicationRow($applicationUid)
    {
        $applicationUid = (int) $applicationUid;
        $this->removeApplicationChildren('tx_ats_domain_model_languageskill', $applicationUid);
        $this->removeApplicationChildren('tx_ats_domain_model_history', $applicationUid);
        $this->removeApplicationChildren('tx_ats_domain_model_note', $applicationUid);
        $this->removeFiles($applicationUid);

        $affectedRows = $this->queryBuilder
            ->delete('tx_ats_domain_model_application')
            ->where(
                $this->queryBuilder->expr()->eq('uid', $applicationUid)
            )
            ->execute();

        return $affectedRows;
    }

    /**
     * Cleans up application child records (hard-deletes them)
     *
     * @param string $table Child table
     * @param int $applicationUid
     * @return void
     */
    public function removeApplicationChildren($table, $applicationUid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        // Show hidden and deleted records as well
        $queryBuilder->getRestrictions()->removeAll();

        $queryBuilder
            ->delete($table)
            ->where(
                $queryBuilder->expr()->eq('application', (int)$applicationUid)
            )
            ->execute();
    }

    /**
     * Removes files
     *
     * @param  int $applicationUid
     * @return void
     */
    public function removeFiles($applicationUid)
    {
        $fileObjects = $this->fileRepository->findByRelation('tx_ats_domain_model_application', 'files', $applicationUid);

        if (!empty($fileObjects)) {
            foreach ($fileObjects as $fileReference) {
                $originalFile = $fileReference->getOriginalFile();

                if ($originalFile->exists()) {
                    $originalFile->getStorage()->deleteFile($originalFile);
                }
            }
        }
    }
}
