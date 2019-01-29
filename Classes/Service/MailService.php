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

        $this->backendUser = $backendUser ?: $GLOBALS['BE_USER'];
        $this->fluidRenderingService = $fluidRenderingService ?: GeneralUtility::makeInstance(FluidRenderingService::class);
    }

    /**
     * Sends a standard reply message
     *
     * @param  Application $application The application to pull information from
     * @param  string $subject
     * @param  string $body
     * @param  array $cc
     * @param  array $bcc
     * @param  bool $useBackendUserCredentials Whether the mail should use the current backend user for sender details
     * @return void
     */
    public function sendReplyMail(Application $application, $subject = "", $body = "", $cc = [], $bcc = [], $useBackendUserCredentials = true)
    {
        $mail = $this->callStatic(GeneralUtility::class, 'makeInstance', MailMessage::class);

        $renderedBody = $this->fluidRenderingService->renderTemplate(
            'Mail/Html',
            [
                'subject' => $subject,
                'application' => $application,
                'backenduser' => $GLOBALS['BE_USER'],
                'body' => $body,
            ]
        );

        $mail
            ->setSubject($subject)
            ->setFrom($this->fetchFrom($useBackendUserCredentials))
            ->setTo([$application->getEmail() => $application->getFirstname() . ' ' . $application->getSurname()])
            ->setBody($renderedBody, 'text/html');

        if (!empty($cc)) {
            $mail->setCc($cc);
        }

        if (!empty($bcc)) {
            $mail->setBcc($bcc);
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
