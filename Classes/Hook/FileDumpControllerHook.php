<?php
namespace PAGEmachine\Ats\Hook;

use PAGEmachine\Ats\Service\ExtconfService;
use TYPO3\CMS\Backend\FrontendBackendUserAuthentication;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\Hook\FileDumpEIDHookInterface;
use TYPO3\CMS\Core\Resource\ResourceInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * FileDumpControllerHook
 *
 * Protects files in ATS folders from public access
 */
class FileDumpControllerHook implements FileDumpEIDHookInterface
{
    /**
     * The sys_file_reference table names to linked applications
     *
     * @var string
     */
    protected $tableNames = 'tx_ats_domain_model_application';

    /**
     * The sys_file_reference field name inside the application
     *
     * @var string
     */
    protected $fieldName = 'files';

    /**
     * Perform custom security/access when accessing file
     * Method should issue 403 if access is rejected
     * or 401 if authentication is required
     *
     * @param ResourceInterface $file
     */
    public function checkFileAccess(ResourceInterface $file)
    {
        // First check: If the file is a "real" file (no folder etc.) and is within the protected storage area, access must be examined further
        if (is_subclass_of($file, AbstractFile::class) && $this->fileIsInAtsStorage($file)) {
            $applications = $this->getLinkedApplications($file->getUid());

            if (empty($applications)) {
                HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_403);
            }

            $feUser = $this->getFeUser();
            $beUserIsLoggedIn = $this->beUserIsLoggedIn();

            foreach ($this->getLinkedApplications($file->getUid()) as $application) {
                if ($this->hasAccess($application, $feUser, $beUserIsLoggedIn)) {
                    return;
                }
            }
            
            HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_403);
        }
    }

    /**
     * Get feUser
     *
     * @return FrontendUserAuthentication
     */
    public function getFeUser()
    {
        $feUser = $GLOBALS['ATS_USER_AUTH'];

        if (!$feUser instanceof FrontendUserAuthentication) {
            $feUser = null;
        }

        return $feUser;
    }

    /**
     *  Returns true if beUser is logged in.
     *
     * @return bool
     */
    public function beUserIsLoggedIn()
    {
        $context = GeneralUtility::makeInstance(Context::class);
        $beUserIsLoggedIn = $context->getPropertyFromAspect('backend.user', 'isLoggedIn');

        return $beUserIsLoggedIn;
    }

    /**
     * Returns true if the current file is located inside the protected ATS folder.
     *
     * @param  AbstractFile $file
     * @return bool
     */
    public function fileIsInAtsStorage(AbstractFile $file)
    {
        return StringUtility::beginsWith($file->getCombinedIdentifier(), ExtconfService::getInstance()->getUploadConfiguration()['uploadFolder']);
    }

    /**
     * Checks if the given be/fe user combination has access to the current file
     *
     * @todo currently all logged in backend users have access to protected files.
     * This should be narrowed down further to check if they actually have permission to view the corresponding application.
     * Since there are a lot of factors for this permission check (access to listAll-Action, extensions like pagemachine/extbase-acl),
     * permission handling needs simplification/cleanup before it can be evaluated here.
     *
     * @param  array  $application
     * @param  FrontendUserAuthentication $feUser
     * @param  bool $beUser
     * @return bool
     */
    public function hasAccess($application, FrontendUserAuthentication $feUser = null, bool $beUserIsLoggedIn = false)
    {
        $granted = $beUserIsLoggedIn;

        if ($feUser->user !== null && !empty($application['user'])) {
            if ($feUser->user['uid'] == $application['user']) {
                $granted = true;
            }
        } elseif ($feUser->user !== null) {
            if ($feUser->getKey('ses', 'Ats/Application') == $application['uid'] && $application['uid'] !== null) {
                $granted = true;
            }
        }

        #if ($beUserIsLoggedIn == true) {
        #    $granted = $beUserIsLoggedIn;
        #}
        return $granted;
    }

    /**
     * Returns all applications connected to a file
     *
     * @param  int $fileUid
     * @return \Generator
     */
    protected function getLinkedApplications($fileUid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file');
        $queryBuilder->getRestrictions()->removeAll();
        $res = $queryBuilder->select('tx_ats_domain_model_application.*')
            ->from('sys_file')
            ->leftJoin(
                'sys_file',
                'sys_file_reference',
                'sys_file_reference',
                'sys_file.uid = sys_file_reference.uid_local'
            )
            ->leftJoin(
                'sys_file_reference',
                'tx_ats_domain_model_application',
                'tx_ats_domain_model_application',
                'sys_file_reference.uid_foreign = tx_ats_domain_model_application.uid'
            )
            ->where(
                $queryBuilder->expr()->eq('sys_file_reference.tablenames', $queryBuilder->createNamedParameter($this->tableNames)),
                $queryBuilder->expr()->eq('sys_file_reference.fieldname', $queryBuilder->createNamedParameter($this->fieldName)),
                $queryBuilder->expr()->eq('sys_file.uid', $queryBuilder->createNamedParameter(intval($fileUid)))
            )
            ->execute();

        foreach ($res as $row) {
            yield $row;
        }
    }
}
