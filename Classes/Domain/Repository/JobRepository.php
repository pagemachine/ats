<?php
namespace PAGEmachine\Ats\Domain\Repository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

/**
 * The repository for Jobs
 */
class JobRepository extends Repository
{
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
