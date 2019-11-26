<?php
namespace PAGEmachine\Ats\Controller\Backend;

use PAGEmachine\Ats\Application\ApplicationQuery;
use PAGEmachine\Ats\Domain\Repository\AjaxApplicationRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * AjaxApplicationController
 */
class AjaxApplicationController
{
    public function getApplications(ServerRequestInterface $request, ResponseInterface $response)
    {
        $body = $request->getParsedBody();

        $query = new ApplicationQuery($body['query']);

        $repository = GeneralUtility::makeInstance(AjaxApplicationRepository::class);

        $data = [
            'draw' => (int)$body['draw'],
            'recordsFiltered' => $repository->getTotalResultsOfQuery($query),
            'data' => $repository->findWithQuery($query)
        ];

        $response->getBody()->write(json_encode($data));
        return $response;
    }
}
