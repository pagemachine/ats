<?php
namespace PAGEmachine\Ats\Service;

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Service\ExtconfService;
use PAGEmachine\Ats\Service\FluidRenderingService;
use PAGEmachine\Ats\Traits\StaticCalling;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;

use PAGEmachine\Ats\Service\CsvService;
use PAGEmachine\Ats\Service\PdfService;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class MailService implements SingletonInterface
{
    use StaticCalling;


    /**
     * @var BackendUserAuthentication $backendUser
     */
    protected $backendUser;


    /**
     * @var \PAGEmachine\Ats\Service\FluidRenderingService
     */
    protected $fluidRenderingService;

    /**
     * @codeCoverageIgnore
     * @return MailService
     */
    public static function getInstance()
    {

        return GeneralUtility::makeInstance(self::class);
    }

    public function __construct(BackendUserAuthentication $backendUser = null, FluidRenderingService $fluidRenderingService = null)
    {
        $this->typo3Version = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);
        $this->backendUser = $backendUser ?: $GLOBALS['BE_USER'];
        $this->fluidRenderingService = $fluidRenderingService ?: GeneralUtility::makeInstance(FluidRenderingService::class);
    }

    /**
     * Sends a standard reply message
     *
     * @param  Application $application The application to pull information from
     * @param  string $subject
     * @param  string $body
     * @param  array|string $cc
     * @param  array|string $bcc
     * @param  bool $useBackendUserCredentials Whether the mail should use the current backend user for sender details
     * @param  string $template
     * @param  int $type
     * @return void
     */
    public function sendReplyMail(Application $application, $subject = "", $body = "", $cc = [], $bcc = [], $useBackendUserCredentials = true, $template = "Mail/Html", $type = 0)
    {
        $mail = $this->callStatic(GeneralUtility::class, 'makeInstance', MailMessage::class);

        $renderedBody = $this->fluidRenderingService->renderTemplate(
            $template,
            [
                'subject' => $subject,
                'application' => $application,
                'backenduser' => $GLOBALS['BE_USER'],
                'body' => $body,
            ]
        );

        if ($type >= 6) {
            $to = $this->fetchTo();
        } else {
            $to = [$application->getEmail() => $application->getFirstname() . ' ' . $application->getSurname()];
        }

        $mail
            ->setSubject($subject)
            ->setFrom($this->fetchFrom($useBackendUserCredentials))
            ->setTo($to);

        if ($this->typo3Version->getMajorVersion() < 10) {
            $mail->setBody($renderedBody, 'text/html');
        } else {
            $mail->html($renderedBody);
        }

        if (!empty($cc)) {
            $mail->setCc($cc);
        }

        if (!empty($bcc)) {
            $mail->setBcc($bcc);
        }

        if ($type = 6 && $application->getFiles()) {
            foreach ($application->getFiles() as $file) {
                $mail->attach($file->getOriginalResource()->getOriginalFile()->getContents(), $file->getOriginalResource()->getName(), $file->getOriginalResource()->getMimeType());
            }

            $fileName = 'Application'.$application->getUid();
            $pdf = PdfService::getInstance()->generateApplicationPdf($application, $subject, $body);
            $mail->attach($pdf, $fileName.'.pdf', 'application/pdf');

            $csv = CsvService::getInstance()->getCSV($application, $fileName);
            $mail->attach($csv, $fileName.'.csv', 'text/x-csv');
        }

        $mail->send();
    }


    /**
     * Returns Sender email/name from the current backend user (or fallback settings if not set)
     *
     * Fallback order is BE user credentials > ATS settings > system wide settings
     *
     * @param $useBackendUserCredentials
     * @return array
     */
    public function fetchFrom($useBackendUserCredentials = true)
    {
        $extconfService = ExtconfService::getInstance();

        if ($extconfService->getUseBackendUserCredentialsInEmails() && $useBackendUserCredentials && GeneralUtility::validEmail($this->backendUser->user['email']) && !empty($this->backendUser->user['realName'])) {
            return [$this->backendUser->user['email'] => $this->backendUser->user['realName']];
        }

        if (!empty($extconfService->getEmailDefaultSenderName()) && GeneralUtility::validEmail($extconfService->getEmailDefaultSenderAddress())) {
            return [$extconfService->getEmailDefaultSenderAddress() => $extconfService->getEmailDefaultSenderName()];
        }

        return $this->fetchSystemFrom();
    }

    /**
     * Returns Receiver email/name
     *
     * @return array
     */
    public function fetchTo()
    {
        $extconfService = ExtconfService::getInstance();

        if (!empty($extconfService->getInfoEmailReceiverName()) && GeneralUtility::validEmail($extconfService->getInfoEmailReceiverAddress())) {
            return [$extconfService->getInfoEmailReceiverAddress() => $extconfService->getInfoEmailReceiverName()];
        }

        if (!empty($extconfService->getEmailDefaultSenderName()) && GeneralUtility::validEmail($extconfService->getEmailDefaultSenderAddress())) {
            return [$extconfService->getEmailDefaultSenderAddress() => $extconfService->getEmailDefaultSenderName()];
        }

        return $this->fetchSystemFrom();
    }

    /**
     * Fetches system mail data
     *
     * @codeCoverageIgnore
     * @return array
     */
    public function fetchSystemFrom()
    {
        return MailUtility::getSystemFrom();
    }
}
