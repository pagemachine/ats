# Workflows

ATS uses [Symfony/Workflow](https://symfony.com/doc/current/components/workflow.html) to manage when and how applications change their state. Its documentation applies for the overall principle (*places* and *transitions*) with some specialization:

- Available **places** are defined by the `ApplicationStatus` enumeration (see [here](../Classes/Application/ApplicationStatus.php)). You should not modify these.

- Available **transitions** are defined via `WorkflowManager` (see [here](../Classes/Workflow/WorkflowManager.php)) and reflect available actions in the `Backend\ApplicationController`. Each time the corresponding controller action is called, the defined status change in the transition will be applied.

Example configuration (`ext_localconf.php` in your site extension):

```
<?php
//Define custom workflow
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['workflows']['your-workflow'] = [
    'places' => \PAGEmachine\Ats\Application\ApplicationStatus::getConstants(),
    'transitions' => [
        'submit' => ['from' => ApplicationStatus::INCOMPLETE, 'to' => ApplicationStatus::NEW_APPLICATION],
        'acknowledge' => ['from' => ApplicationStatus::NEW_APPLICATION, 'to' => ApplicationStatus::DEPARTMENT],
        'employ' => ['from' => ApplicationStatus::DEPARTMENT, 'to' => ApplicationStatus::EMPLOYED],
        'edit' => ['from' => [], 'to' => []],
    ]
];

//Activate the workflow
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['activeWorkflow'] = 'your-workflow';
```

This configuration would define the following workflow:

- Once the application is submitted by the applicant, it receives the status `New Application` and is now visible in the backend.
- It needs to be **acknowledged** and will then be moved to the `Department` status.
- Finally, it moves to `Employed` with the **employ** action.
- The **edit** action is always available and does not trigger a status change.

All other actions will not appear in the backend module until you add them to your workflow.
