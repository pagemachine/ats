<?php
namespace PAGEmachine\Ats\Service;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class AuthenticationService
{
    /**
     * @var FrontendUserRepository $frontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     * @param FrontendUserRepository $frontendUserRepository
     */
    public function injectFrontendUserRepository(FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    /**
     * Returns whether any user is currently authenticated
     *
     * @return bool
     */
    public function isUserAuthenticated()
    {
        return (bool)GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('frontend.user', 'isLoggedIn');
    }

    /**
     * Returns the currently authenticated user
     *
     * @return FrontendUser
     */
    public function getAuthenticatedUser()
    {
        return $this->frontendUserRepository->findByIdentifier($this->getFrontendController()->fe_user->user['uid']);
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * Checks if a user belongs to a given group (via id)
     * @param int $groupId
     * @return bool
     */
    protected function userHasGroup($groupId)
    {

        if ($this->isUserAuthenticated()) {
            $feUser = $this->getAuthenticatedUser();

            foreach ($feUser->getUsergroup() as $usergroup) {
                if ($usergroup->getUid() == $groupId) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Checks if a user belongs to a given group (via id)
     * @param int $groupId
     * @return bool
     */
    public function isUserAuthenticatedAndHasGroup($groupId = null)
    {
        if ($this->isUserAuthenticated()) {
            if ($groupId === null) {
                return true;
            } elseif ($this->userHasGroup($groupId)) {
                return true;
            }
        }
        return false;
    }
}
