<?php

declare(strict_types=1);

namespace PAGEmachine\Ats\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Frontend\Middleware\BackendUserAuthenticator;

class AtsBackendUserAuthenticator extends \TYPO3\CMS\Core\Middleware\BackendUserAuthenticator
{
    /**
     * @var BackendUserAuthenticator
     */
    protected $backendUserAuthenticator;

    /**
     * @param BackendUserAuthenticator $backendUserAuthenticator
     */
    public function __construct(BackendUserAuthenticator $backendUserAuthenticator)
    {
        $this->backendUserAuthenticator = $backendUserAuthenticator;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $eID = $request->getParsedBody()['eID'] ?? $request->getQueryParams()['eID'] ?? null;

        if ($eID === null || $eID !== 'dumpFile') {
            return $handler->handle($request);
        }
        
        $response = $this->backendUserAuthenticator->process($request, $handler);

        return $response;
    }
}
