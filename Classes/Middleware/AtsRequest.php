<?php

declare(strict_types=1);

namespace PAGEmachine\Ats\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AtsRequest implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $eID = $request->getParsedBody()['eID'] ?? $request->getQueryParams()['eID'] ?? null;

        if ($eID === null || $eID !== 'dumpFile') {
            unset($GLOBALS['ATS_USER_AUTH']);
            return $handler->handle($request);
        }

        $GLOBALS['ATS_USER_AUTH'] = $request->getAttribute('frontend.user');
        
        $response = $handler->handle($request);

        return $response;
    }
}
