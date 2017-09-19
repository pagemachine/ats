<?php
namespace PAGEmachine\Ats\Service;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Domain\Model\Application;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\SingletonInterface;
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
     * @var BackendUserAuthentication $backendUser
     */
    protected $backendUser;

    public function __construct(BackendUserAuthentication $backendUser = null, ObjectManager $objectManager = null)
    {
        $this->objectManager = $objectManager ? $objectManager : GeneralUtility::makeInstance(ObjectManager::class);
        $this->configurationManager = $this->objectManager->get(ConfigurationManager::class);
        $this->backendUser = $backendUser ?: $GLOBALS['BE_USER'];
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

        return mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $rawFilename.'.pdf');
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
        $filePath = $this->generatePdf($application, $body, $filename);
        $this->downloadPdf($filePath, $filename);
    }

    /**
     * Generates Pdf with footer and header.
     *
     * @param      Application  $application    The application to pull information from
     * @param      string       $body           The body html.
     * @param      string       $fileName       The file name
     *
     * @return     string  ( Absolute path of the pdf file )
     */
    public function generatePdf(Application $application, $body, $fileName = 'download.pdf')
    {
        $filePath = GeneralUtility::getFileAbsFileName('typo3temp/' . $fileName);

        /* @var $pdf \mPDF */
        $pdf = $this->objectManager->get('mPDF', 'c', 'A4', '', '', 0, 0, 0, 0, 0, 0);

        $pdf->setAutoTopMargin = true;
        $pdf->SetHTMLHeader($this->getTemplate('Pdf/Header', $application));

        $pdf->setAutoBottomMargin = true;
        $pdf->SetHTMLFooter($this->getTemplate('Pdf/Footer', $application));

        $body = "<div style='margin-left: 20mm; margin-right: 20mm;'>".$body."<div>";
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

    /**
     * Gets the template html.
     *
     * @param      string  $templateName  The template name
     * @param      Application $application
     *
     * @return     string  html
     */
    protected function getTemplate($templateName, Application $application)
    {
        $configuration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $view = $this->objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);
        $view->setFormat('html');
        $view->setLayoutRootPaths($configuration['view']['layoutRootPaths']);
        $view->setPartialRootPaths($configuration['view']['partialRootPaths']);
        $view->setTemplatePathAndFilename($this->getTemplateRootPath($configuration['view']['templateRootPaths'], $templateName));
        $view->assign('application', $application);
        $view->assign('backenduser', $this->backendUser->user);
        return $view->render();
    }

    /**
     * Legacy Typo3 6.2 Workaround For setTemplateRootPaths
     * TODO: Replace when 6.2 Support expires
     *
     * @param      array  $pathArray     templateRootPaths array
     * @param      string  $templateName  The template name
     *
     * @return     string  The template root paths.
     */
    protected function getTemplateRootPath($pathArray, $templateName)
    {
        $path = GeneralUtility::getFileAbsFileName($pathArray[0]) . $templateName . '.html';
        foreach ($pathArray as $key => $value) {
            $template = GeneralUtility::getFileAbsFileName($value) . $templateName . '.html';
            if (file_exists($template)) {
                $path = $template;
            }
        }
        return $path;
    }
}
