<?php
namespace PAGEmachine\Ats\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Trait for anonymization related calls
 */
trait AnonymizationTrait
{
    /**
     * Finds all applications older than a given date
     * Fetches one application at a time to prevent too large result sets
     *
     * @param \DateTime $threshold The date up to which applications are "old"
     * @param array $additionalConditions
     * @return \Generator
     */
    public function findOldObjects(\DateTime $threshold, $additionalConditions = null)
    {
        $query = $this->createQuery();

        $constraints = [
            $query->equals('anonymized', false),
            $query->lessThan('creationDate', $threshold),
        ];

        // Add additional conditions from config
        $conditions = [];
        if (!empty($additionalConditions)) {
            foreach ($additionalConditions as $conditionConfig) {
                $conditions[] = $this->buildConstraint($query, $conditionConfig);
            }
            $constraints[] = $query->logicalAnd($conditions);
        }

        $query->matching(
            $query->logicalAnd(
                $constraints
            )
        );

        return $query->execute();
    }

    public function persistAll()
    {
        $this->persistenceManager->persistAll();
    }

    /**
     * Converts the given config into a query constraint
     *
     * @param  QueryInterface $query
     * @param  array $constraintConfig
     * @return ConstraintInterface
     */
    public function buildConstraint(QueryInterface $query, $constraintConfig)
    {
        $value = $constraintConfig['value'];

        if ($constraintConfig['cast']) {
            $castResult = settype($value, $constraintConfig['cast']);
            if (!$castResult) {
                throw new \Exception(sprintf('Could not cast value "%s" to type "%s"', $value, $constraintConfig['type']));
            }
        }

        return $query->{$constraintConfig['operator']}($constraintConfig['property'], $value);
    }
}
