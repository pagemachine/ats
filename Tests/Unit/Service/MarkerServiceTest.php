<?php
namespace PAGEmachine\Ats\Tests\Unit\Service;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Service\ExtconfService;
use PAGEmachine\Ats\Service\MarkerService;

/**
 * Testcase for PAGEmachine\Ats\Service\MarkerService
 */
class MarkerServiceTest extends UnitTestCase
{
    /**
     *
     * @var MarkerService
     */
    protected $markerService;

    /**
     *
     * @var ExtconfService
     */
    protected $extconfService;



    /**
     * Setup
     */
    protected function setUp()
    {

        $this->extconfService = $this->prophesize(ExtconfService::class);


        $this->markerService = new MarkerService($this->extconfService->reveal());

        $this->markerService->inputFormat="[[%s]]";
    }

    /**
     * @test
     */
    public function replacesMarkersWithFluidMarkers()
    {

        $text = "Hello [[application.firstname]] [[application.surname]]";

        $replacedText = $this->markerService->replaceMarkers($text);

        $this->assertEquals("Hello {application.firstname -> f:format.raw()} {application.surname -> f:format.raw()}", $replacedText);
    }

    /**
     * @test
     */
    public function convertsContextSpecificMarkers()
    {


        $this->extconfService->getMarkerReplacements(MarkerService::CONTEXT_MAIL)->willReturn(
            [
                'backenduser.signature' => 'backenduser.tx_ats_email_signature',
            ]
        );

        $text = "[[backenduser.signature]]";

        $replacedText = $this->markerService->replaceMarkers($text, MarkerService::CONTEXT_MAIL);

        $this->assertEquals("{backenduser.tx_ats_email_signature -> f:format.raw()}", $replacedText);
    }
}
