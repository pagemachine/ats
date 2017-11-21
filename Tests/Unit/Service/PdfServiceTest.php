<?php
namespace PAGEmachine\Ats\Tests\Unit\Service;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Service\PdfService;
use Prophecy\Argument;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Testcase for PAGEmachine\Ats\Service\PdfService
 */
class PdfServiceTest extends UnitTestCase
{
    /**
     *
     * @var PdfService
     */
    protected $pdfService;

    /**
     *
     * @var Application
     */
    protected $application;

    /**
     *
     * @var StandaloneView
     */
    protected $standaloneView;

    /**
     * Set up this testcase
     */
    protected function setUp()
    {
        if (!file_exists(GeneralUtility::getFileAbsFileName('typo3temp/'))) {
            mkdir(GeneralUtility::getFileAbsFileName('typo3temp/'), 0777);
        }
        if (!file_exists(GeneralUtility::getFileAbsFileName('typo3temp/Pdf'))) {
            mkdir(GeneralUtility::getFileAbsFileName('typo3temp/Pdf'), 0777);
        }
        register_shutdown_function(function () {
            if (file_exists(GeneralUtility::getFileAbsFileName('typo3temp/unitTest.pdf'))) {
                unlink(GeneralUtility::getFileAbsFileName('typo3temp/unitTest.pdf'));
            }
            if (file_exists(GeneralUtility::getFileAbsFileName('typo3temp/Pdf/Header.html'))) {
                unlink(GeneralUtility::getFileAbsFileName('typo3temp/Pdf/Header.html'));
            }
        });

        $this->application = new Application();

        $this->standaloneView = $this->prophesize(StandaloneView::class);

        $backendUser = new BackendUserAuthentication();
        $backendUser->user = [
            'tx_ats_pdf_signature' => 'Hello World!',
        ];

        $configurationManager = $this->prophesize(ConfigurationManager::class);
        $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK)->willReturn(
            [ 'view' =>
                ['layoutRootPaths' => [0 => GeneralUtility::getFileAbsFileName('typo3temp/')],
                'partialRootPaths' => [0 => GeneralUtility::getFileAbsFileName('typo3temp/')],
                'templateRootPaths' => [
                    0 => 'typo3temp/base/',
                    10 => 'typo3temp/',
                ]],
            ]
        );

        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->get(ConfigurationManager::class)->willReturn($configurationManager->reveal());
        $objectManager->get(StandaloneView::class)->willReturn($this->standaloneView->reveal());
        $objectManager->get('mPDF', Argument::type('string'), Argument::type('string'), Argument::type('string'), Argument::type('string'), 0, 0, 0, 0, 0, 0)->willReturn(new \mPDF("c", "A4", "", "", 0, 0, 0, 0, 0, 0));

        $this->pdfService = $this->getMockBuilder(PdfService::class)
        ->setConstructorArgs(['backendUser' => $backendUser, 'objectManager' => $objectManager->reveal()])
        ->setMethods(array("setHeader","setexit"))
        ->getMock();
    }

    /**
     * @test
     */
    public function generatePdf()
    {
        $this->standaloneView->setFormat(Argument::type('string'))->willReturn();
        $this->standaloneView->setLayoutRootPaths(Argument::type('array'))->willReturn(void);
        $this->standaloneView->setPartialRootPaths(Argument::type('array'))->willReturn(void);
        $this->standaloneView->assign(Argument::type('string'), Argument::type(Application::class))->willReturn(void);
        $this->standaloneView->assign(Argument::type('string'), Argument::type('array'))->willReturn(void);
        $this->standaloneView->assign(Argument::type('string'), Argument::type('string'))->willReturn(void);
        $this->standaloneView->render()->willReturn("<p>header/footer</p>")->shouldBeCalled();

        //Legacy Test
        file_put_contents(GeneralUtility::getFileAbsFileName('typo3temp/Pdf/Header.html'), '');
        $this->standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('typo3temp/Pdf/Header.html'))->willReturn(void)->shouldBeCalled();
        $this->standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('typo3temp/base/Pdf/Footer.html'))->willReturn(void)->shouldBeCalled();

        $path = $this->pdfService->generatePdf($this->application, '<p>Hello World!</p>', 'unitTest.pdf');
        $pdfexists = false;
        define('PDF', "\x25\x50\x44\x46\x2D");
        if (file_exists($path)) {
            $pdfexists = true;
            //validate pdf header
            $this->assertTrue(file_get_contents($path, false, null, 0, strlen(PDF)) === PDF) ? true : false;
            unlink($path);
        }
        $this->assertTrue($pdfexists);
    }

    /**
     * @test
     */
    public function downloadPdf()
    {
        $file = GeneralUtility::getFileAbsFileName('typo3temp/unitTest.pdf');

        $this->pdfService->expects($this->any())->method('setHeader')
        ->will($this->returnCallback(function ($string) {
            $this->header[] = $string;
        }));

        file_put_contents($file, '');
        $this->pdfService->expects($this->once())->method('setexit');
        $this->pdfService->downloadPdf($file, 'unitTest.pdf');

        $fieleHeader = false;
        foreach ($this->header as $key => $value) {
            if ($value == 'Content-Disposition: ' .'attachment' . '; filename="' . 'unitTest.pdf' . '"') {
                $fieleHeader = true;
            }
        }
        $this->assertTrue($fieleHeader);
    }

    /**
     * @test
     */
    public function cleansFilename()
    {
        $dirtySubject = "filename!§$%&/=?-foo^#'+*öäü";
        $expectedFilename = "filename-fooöäü.pdf";

        $this->assertEquals($expectedFilename, $this->pdfService->createCleanedFilename($dirtySubject));
    }

    /**
     * @test
     */
    public function generateAndDownloadPdf()
    {
        $pdfServiceStub = $this->getMockBuilder(PdfService::class)
        ->setConstructorArgs(['backendUser' => null, 'objectManager' => $this->prophesize(ObjectManager::class)->reveal()])
        ->setMethods(array('createCleanedFilename','generatePdf','downloadPdf'))
        ->getMock();

        $pdfServiceStub->expects($this->once())->method('createCleanedFilename')->will($this->returnValue('unitTest.pdf'));
        $pdfServiceStub->expects($this->once())->method('generatePdf')->will($this->returnValue('/test/unitTest.pdf'));
        $pdfServiceStub->expects($this->once())->method('downloadPdf');

        $pdfServiceStub->generateAndDownloadPdf('unitTest.pdf', $this->application, 'Text');
    }

    /**
     * @test
     */
    public function generateRandomFilename()
    {
        $this->assertRegExp('/.+\.pdf$/i', $this->pdfService->generateRandomFilename());
    }
}
