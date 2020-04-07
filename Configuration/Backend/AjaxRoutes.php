<?php

return [
    'ats_applications_list' => [
        'path' => '/ats/applications',
        'target' => \PAGEmachine\Ats\Controller\Backend\AjaxApplicationController::class . '::getApplications'
    ],
];
