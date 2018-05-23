<?php
namespace PAGEmachine\Ats\Domain\Repository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

/**
 * The repository for TextTemplates
 */
class TextTemplateRepository extends Repository
{
    /**
     * Default query settings adjustments
     * Ensures text templates are loaded system-wide and not restricted by pid, since they are used in BE and FE
     *
     * @return void
     */
    public function initializeObject()
    {
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);

        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Returns dropdown options for given message type
     *
     * @param  int $type
     * @return array
     */
    public function getDropdownOptionsForType($type)
    {
        $textTemplates = $this->findByType($type);

        $dropdown = [];

        if ($textTemplates->count() > 0) {
            foreach ($textTemplates as $template) {
                $dropdown[$template->getUid()] = $template->getTitle();
            }
        }

        return $dropdown;
    }
}
