<?php
namespace PAGEmachine\Ats\Service;

use PAGEmachine\Ats\Domain\Model\Application;
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
     * @codeCoverageIgnore
     * @return MailService
     */
    public static function getInstance()
    {

        return GeneralUtility::makeInstance(self::class);
    }

    public function __construct(BackendUserAuthentication $backendUser = null)
    {

        $this->backendUser = $backendUser ?: $GLOBALS['BE_USER'];
    }

    /**
     * Sends a standard reply message
     *
     * @param  Application $application The application to pull information from
     * @param  string $subject
     * @param  string $body
     * @return void
     */
    public function sendReplyMail(Application $application, $subject = "", $body = "", $cc = "", $bcc = "")
    {

        $backendUser = $GLOBALS['BE_USER'];

        $mail = $this->callStatic(GeneralUtility::class, 'makeInstance', MailMessage::class);

        $mail
            ->setSubject($subject)
            ->setFrom($this->fetchFrom())
            ->setTo([$application->getEmail() => $application->getFirstname() . ' ' . $application->getSurname()])
            ->setBody($body, 'text/html');

        if ($cc != "") {
            $mail->setCc($cc);
        }

        if ($bcc != "") {
            $mail->setBcc($bcc);
        }

        $mail->send();
    }


    /**
     * Returns Sender email/name from the current backend user (or fallback settings if not set)
     *
     * @return array
     */
    protected function fetchFrom()
    {

        if (empty($this->backendUser->user['email']) || empty($this->backendUser->user['realName'])) {
            $systemFrom = $this->fetchSystemFrom();
            return $systemFrom;
        }

        return [$this->backendUser->user['email'] => $this->backendUser->user['realName']];
    }

    /**
     * Fetches system mail data
     *
     * @codeCoverageIgnore
     * @return array
     */
    protected function fetchSystemFrom()
    {

        return MailUtility::getSystemFrom();
    }
}
