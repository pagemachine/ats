<?php
namespace PAGEmachine\Ats\Service;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Service\FluidRenderingService;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\File\BasicFileUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class PdfService implements SingletonInterface
{
     /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Core\Utility\File\BasicFileUtility
     */
    protected $basicFileFunctions;

    /**
     * @var \PAGEmachine\Ats\Service\FluidRenderingService
     */
    protected $fluidRenderingService;

    /**
     * @var BackendUserAuthentication $backendUser
     */
    protected $backendUser;

    public function __construct(BackendUserAuthentication $backendUser = null, ObjectManager $objectManager = null, FluidRenderingService $fluidRenderingService = null)
    {
        $this->objectManager = $objectManager ? $objectManager : GeneralUtility::makeInstance(ObjectManager::class);
        $this->configurationManager = $this->objectManager->get(ConfigurationManager::class);
        $this->basicFileFunctions = $this->objectManager->get(BasicFileUtility::class);
        $this->backendUser = $backendUser ?: $GLOBALS['BE_USER'];
        $this->fluidRenderingService = $fluidRenderingService ?: GeneralUtility::makeInstance(FluidRenderingService::class);
    }

    /**
     * @codeCoverageIgnore
     * @return     PdfService
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * @codeCoverageIgnore
     * @param      string  $string
     */
    protected function setHeader($string)
    {
        header($string);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function setexit()
    {
        exit;
    }

    /**
     * Creates a cleaned filename
     *
     * @param  string $rawFilename (without ".pdf")
     * @return string
     */
    public function createCleanedFilename($rawFilename)
    {
        return $this->basicFileFunctions->cleanFileName($rawFilename.'.pdf');
    }

    /**
     * Generates a random filename
     *
     * @return string
     */
    public function generateRandomFilename()
    {

        return $this->createCleanedFilename(rand());
    }

    /**
     * Generates a pdf and sends it to the browser as a download
     *
     * @todo what if something goes wrong here? Needs to check if there is actually a pdf to send
     *
     * @param  string      $subject
     * @param  Application $application
     * @param  string      $body
     * @return void
     */
    public function generateAndDownloadPdf($subject, Application $application, $body)
    {

        $filename = $this->createCleanedFilename($subject);
        $filePath = $this->generatePdf($application, $subject, $body, $filename);
        $this->downloadPdf($filePath, $filename);
    }

    /**
     * Generates Pdf with footer and header.
     *
     * @param      Application  $application    The application to pull information from
     * @param      string       $subject        The subject html
     * @param      string       $body           The body html.
     * @param      string       $fileName       The file name
     *
     * @return     string  ( Absolute path of the pdf file )
     */
    public function generatePdf(Application $application, $subject, $body, $fileName = 'download.pdf')
    {
        $filePath = GeneralUtility::getFileAbsFileName('typo3temp/' . $fileName);

        /* @var $pdf \Mpdf\Mpdf */
        $pdf = $this->objectManager->get('Mpdf\Mpdf', [
            'mode' => 'c',
            'format' => 'A4',
            'default_font_size' => '',
            'default_font' => '',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'orientation' => 'P',
        ]);

        $pdf->setAutoTopMargin = true;

        $pdf->setHTMLHeader(
            $this->fluidRenderingService->renderTemplate(
                'Pdf/Header',
                [
                    'application' => $application,
                    'backenduser' => $this->backendUser->user,
                ]
            )
        );

        $pdf->setAutoBottomMargin = true;

        $pdf->setHTMLFooter(
            $this->fluidRenderingService->renderTemplate(
                'Pdf/Footer',
                [
                    'application' => $application,
                    'backenduser' => $this->backendUser->user,
                ]
            )
        );

        $body = $this->fluidRenderingService->renderTemplate(
            'Pdf/Body',
            [
                'application' => $application,
                'backenduser' => $this->backendUser->user,
                'subject' => $subject,
                'body' => $body,
            ]
        );

        $pdf->WriteHTML($body);
        $pdf->Output($filePath, 'F');
        return $filePath;
    }

    /**
     * Downloads the pdf file.
     *
     * @param      string  $filePath  The absolute file path
     * @param      string  $fileName  The download file name
     */
    public function downloadPdf($filePath, $fileName = 'download.pdf')
    {
        $this->setHeader('Content-Description: File Transfer');
        $this->setHeader('Content-Transfer-Encoding: binary');
        $this->setHeader('Cache-Control: public, must-revalidate, max-age=0');
        $this->setHeader('Pragma: public');
        $this->setHeader('Expires: 0');
        $this->setHeader('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        $this->setHeader('Content-Type: application/pdf', false);
        $this->setHeader('Content-Disposition: ' .'attachment' . '; filename="' . $fileName . '"');
        if (file_exists($filePath)) {
            readfile($filePath);
            unlink($filePath);
            $this->setexit();
        }
        return false;
    }
}
