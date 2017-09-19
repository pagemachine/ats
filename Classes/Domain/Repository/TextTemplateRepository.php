<?php
namespace PAGEmachine\Ats\Domain\Repository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Persistence\Repository;

/**
 * The repository for TextTemplates
 */
class TextTemplateRepository extends Repository
{
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
