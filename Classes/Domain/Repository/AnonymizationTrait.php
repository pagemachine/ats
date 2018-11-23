<?php
namespace PAGEmachine\Ats\Domain\Repository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Trait for anonymization related calls
 */
trait AnonymizationTrait
{
    /**
     * Counts all applications older than a given date
     * @param  \DateTime $threshold The date up to which applications are "old"
     * @return QueryResult
     */
    public function countOldObjects(\DateTime $threshold)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('anonymized', false),
                $query->lessThan("creationDate", $threshold)
            )
        );

        return $query->count();
    }

    /**
     * Finds all applications older than a given date
     * Fetches one application at a time to prevent too large result sets
     *
     * @param  \DateTime $threshold The date up to which applications are "old"
     * @return \Generator
     */
    public function findOldObjects(\DateTime $threshold)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('anonymized', false),
                $query->lessThan("creationDate", $threshold)
            )
        )->setLimit(1);

        for ($i = 0; $i < $this->countOldObjects($threshold); $i++) {
            $query->setOffset($i);
            yield $query->execute()->getFirst();
        }
    }

    public function persistAll()
    {
        $this->persistenceManager->persistAll();
    }
}
