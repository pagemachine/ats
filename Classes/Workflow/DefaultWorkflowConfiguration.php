<?php
namespace PAGEmachine\Ats\Workflow;

use PAGEmachine\Ats\Application\ApplicationStatus;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Default Workflow class
 * This is a helper class to deliver workflow configuration to ext_localconf.
 * If you want to include custom workflows, you can add them to $_EXTCONF without the need for a class like this.
 *
 * @codeCoverageIgnore
 */
class DefaultWorkflowConfiguration
{
    protected static $transitions = [
        'submit' => ['from' => ApplicationStatus::INCOMPLETE, 'to' => ApplicationStatus::NEW_APPLICATION],
        'show' => ['from' => [], 'to' => []],
        'edit' => ['from' => [], 'to' => []],
        //'editStatus'=> ['from' => [], 'to' => []],
        'acknowledge' => ['from' => ApplicationStatus::NEW_APPLICATION, 'to' => ApplicationStatus::DEPARTMENT],
        'autoAcknowledge'=> ['from' => [], 'to' => []],
        'backToPerso' => ['from' => ApplicationStatus::DEPARTMENT, 'to' => ApplicationStatus::PERSO],
        'employ' => ['name' => 'close','from' => ApplicationStatus::PERSO, 'to' => ApplicationStatus::EMPLOYED],
        'cancelByEmployer' => ['name' => 'close','from' => ApplicationStatus::PERSO, 'to' => ApplicationStatus::CANCELLED_BY_EMPLOYER],
        'cancelByCandidate' => ['name' => 'close','from' => ApplicationStatus::PERSO, 'to' => ApplicationStatus::CANCELLED_BY_CANDIDATE],
        'notes' => ['from' => [], 'to' => []],
        'reply' => ['from' => [], 'to' => []],
        'invite' => ['from' => [], 'to' => []],
        'reject1' => ['name'=> 'reject', 'from' => ApplicationStatus::NEW_APPLICATION, 'to' => ApplicationStatus::CANCELLED_BY_EMPLOYER],
        'reject2' => ['name'=> 'reject', 'from' => ApplicationStatus::PERSO, 'to' => ApplicationStatus::CANCELLED_BY_EMPLOYER],
        'reject3' => ['name'=> 'reject', 'from' => ApplicationStatus::DEPARTMENT, 'to' => ApplicationStatus::CANCELLED_BY_EMPLOYER],
//        'rating'=> ['from' => [], 'to' => []],
        'ratingPerso'=> ['from' => [], 'to' => []],
        'history' => ['from' => [], 'to' => []],
        'clone' => ['from' => [], 'to' => []],
        'close' => ['from' => [], 'to' => []],
        // All three "final" application types can be moved to pool. This is done by adding three transitions to the same status
        'moveToPool1' => ['name' => 'moveToPool', 'from' => [ApplicationStatus::CANCELLED_BY_EMPLOYER], 'to' => [ApplicationStatus::CANCELLED_BY_EMPLOYER]],
        'moveToPool2' => ['name' => 'moveToPool', 'from' => [ApplicationStatus::CANCELLED_BY_CANDIDATE], 'to' => [ApplicationStatus::CANCELLED_BY_CANDIDATE]],
        'moveToPool3' => ['name' => 'moveToPool', 'from' => [ApplicationStatus::EMPLOYED], 'to' => [ApplicationStatus::EMPLOYED]],
    ];

    /**
     * Returns the configuration for a simple workflow
     *
     * @return array
     */
    public static function get()
    {
        return [
            'places' => ApplicationStatus::getConstants(),
            'transitions' => self::$transitions,
        ];
    }
}
