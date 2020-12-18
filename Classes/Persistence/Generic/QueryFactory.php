<?php
namespace PAGEmachine\Ats\Persistence\Generic;

use PAGEmachine\Ats\Persistence\OpenRepositoryInterface;
use PAGEmachine\Ats\Traits\StaticCalling;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Extbase\Reflection\Exception\UnknownClassException;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Override class for the extbase QueryFactory which considers default orderings and querysettings from the repository (if it exists)
 */
class QueryFactory extends \TYPO3\CMS\Extbase\Persistence\Generic\QueryFactory
{
    use StaticCalling;


    /**
     * Creates a query object working on the given class name
     *
     * @param string $className The class name
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function create($className)
    {

        $query = $this->callStatic(parent::class, 'create', $className);

        $repository = null;

        try {
            $repositoryClassName = ClassNamingUtility::translateModelNameToRepositoryName($className);
            $repository = $this->objectManager->get($repositoryClassName);
        } catch (UnknownClassException $e) {
            // Silently ignore
        }

        // If there is an existing repository, check if it provides the necessary getters for settings and orderings (via interface)
        if ($repository !== null && ($repository instanceof OpenRepositoryInterface)) {
            //Set default query settings
            if ($repository->getDefaultQuerySettings() != null) {
                $query->setQuerySettings($repository->getDefaultQuerySettings());
            }

            //Set orderings
            if ($repository->getDefaultOrderings() != []) {
                $query->setOrderings($repository->getDefaultOrderings());
            }
        }

        return $query;
    }
}
