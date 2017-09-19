<?php
namespace PAGEmachine\Ats\Domain\Repository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use PAGEmachine\Ats\Persistence\Repository;

/**
 * The repository for history entries
 * @codeCoverageIgnore
 */
class HistoryRepository extends Repository
{
    protected $defaultOrderings = [
        'creationDate' => QueryInterface::ORDER_DESCENDING
    ];
}
