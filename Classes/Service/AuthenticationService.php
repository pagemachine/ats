<?php
namespace PAGEmachine\Ats\Service;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Hairu\Domain\Model\FrontendUser;
use PAGEmachine\Hairu\Domain\Service\AuthenticationService as HairuAuthenticationServie;

class AuthenticationService {

    /**
     * @var PAGEmachine\Hairu\Domain\Service\AuthenticationService
     * @inject
    */
    protected $hairuAuthenticationService;

	/**
	 * Checks if a user belongs to a given group (via id)
	 * @param integer $groupId 
	 * @return boolean
	 */
	protected function userHasGroup($groupId) {

		if ($this->hairuAuthenticationService->isUserAuthenticated()) {

	        $feUser = $this->hairuAuthenticationService->getAuthenticatedUser();

	        foreach($feUser->getUsergroup() as $usergroup) {
	            if ($usergroup->getUid() == $groupId) {
	                return true;
	            }
	        }
		}
		return false;

	}

	/**
	 * Checks if a user belongs to a given group (via id)
	 * @param integer $groupId 
	 * @return boolean
	 */
	public function isUserAuthenticatedAndHasGroup($groupId = null) {
		if ($this->hairuAuthenticationService->isUserAuthenticated()) {
			if ($groupId === null) {
				return true;
			} else if ($this->userHasGroup($groupId)) {
				return true;
			}

		}
		return false;

	}

	/**
	 * Returns the currently authenticated user
	 *
	 * @return FrontendUser
	 */
	public function getAuthenticatedUser() {

		return $this->hairuAuthenticationService->getAuthenticatedUser();
	}


}