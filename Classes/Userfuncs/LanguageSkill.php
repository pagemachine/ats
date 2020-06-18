<?php
namespace PAGEmachine\Ats\Userfuncs;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * History
 * @codeCoverageIgnore
 */
class LanguageSkill
{
    public function getTitle(&$parameters)
    {

        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
        $languageSkillRepository = $objectManager->get(\PAGEmachine\Ats\Domain\Repository\LanguageSkillRepository::class);
        $language = $languageSkillRepository->findByUid($parameters['row']['uid']);
        $parameters['title'] = $language->getLocalizedName();
    }
}
