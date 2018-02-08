<?php
namespace PAGEmachine\Ats\Domain\Repository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Persistence\Repository;

/**
 * The repository for Jobs
 */
class JobRepository extends Repository
{

    // Order by BE sorting
    protected $defaultOrderings = array(
        'endtime' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
        'title' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    );

    /**
     * Override findAll() function to apply hidden field restriction in backend context, since all enableFields are not applied there
     *
     * @return QueryResult
     */
    public function findAll()
    {
        $query = $this->createQuery();

        return $query->matching(
            $query->equals('hidden', 0)
        )->execute();
    }
}
