<?php
namespace PAGEmachine\Ats\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class CleanupService
{
    /**
     * @var FileRepository
     */
    protected $fileRepository;

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
    public function __construct(FileRepository $fileRepository = null)
    {
        $this->fileRepository = $fileRepository ?: GeneralUtility::makeInstance(FileRepository::class);
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

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_application');

        // Show hidden and deleted records as well
        $queryBuilder->getRestrictions()->removeAll();

        /**
         * Deletes all applications which are unfinished and older than 30 days
         */
        $statement = $queryBuilder
            ->select('uid')
            ->from('tx_ats_domain_model_application')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->lt('crdate', $unfinishedThreshold->getTimestamp()),
                    $queryBuilder->expr()->eq('status', 0)
                )
            )
            ->execute();

        $affectedRows = 0;

        while ($row = $statement->fetch()) {
            // Deletes child language skills. History and notes are not present in unfinished applications, so no need to delete them
            $this->removeApplicationChildren('tx_ats_domain_model_languageskill', (int)$row['uid']);
            $this->removeFiles((int)$row['uid']);

            $affectedRows += $queryBuilder
                ->delete('tx_ats_domain_model_application')
                ->where(
                    $queryBuilder->expr()->eq('uid', (int)$row['uid'])
                )
                ->execute();
        }
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
