<?php
namespace PAGEmachine\Ats\Persistence;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class Repository extends \TYPO3\CMS\Extbase\Persistence\Repository implements OpenRepositoryInterface
{
    /**
     * Gets the default orderings of this repository
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getDefaultOrderings()
    {
        return $this->defaultOrderings;
    }

    /**
     * Gets the default query settings of this repository
     *
     * @return QuerySettingsInterface|null
     * @codeCoverageIgnore
     */
    public function getDefaultQuerySettings()
    {
        return $this->defaultQuerySettings;
    }
}
