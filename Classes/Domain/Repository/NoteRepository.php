<?php
namespace PAGEmachine\Ats\Domain\Repository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * The repository for Notes
 */
class NoteRepository extends Repository
{
    protected $defaultOrderings = [
        'creationDate' => QueryInterface::ORDER_DESCENDING,
    ];
}
