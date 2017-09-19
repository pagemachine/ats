<?php
namespace PAGEmachine\Ats\Persistence;

interface OpenRepositoryInterface
{
    /**
     * Gets the default orderings of this repository
     *
     * @return array
     */
    public function getDefaultOrderings();

    /**
     * Gets the default query settings of this repository
     *
     * @return QuerySettingsInterface|null
     */
    public function getDefaultQuerySettings();
}
