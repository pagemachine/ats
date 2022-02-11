<?php

declare(strict_types=1);

namespace PAGEmachine\Ats\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Frontend\Middleware\FrontendUserAuthenticator;

class AtsFrontendUserAuthenticator implements MiddlewareInterface
{
    /**
     * @var FrontendUserAuthenticator
     */
    protected $frontendUserAuthenticator;

    /**
     * @param FrontendUserAuthenticator $frontendUserAuthenticator
     */
    public function __construct(FrontendUserAuthenticator $frontendUserAuthenticator)
    {
        $this->frontendUserAuthenticator = $frontendUserAuthenticator;
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

        $response = $this->frontendUserAuthenticator->process($request, $handler);

        return $response;
    }
} 
