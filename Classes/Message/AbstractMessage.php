<?php
namespace PAGEmachine\Ats\Message;

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Service\MailService;
use PAGEmachine\Ats\Service\PdfService;
use TYPO3\CMS\Fluid\View\StandaloneView;

/*
 * This file is part of the PAGEmachine ATS project.
 */


abstract class AbstractMessage
{
    const SENDTYPE_DEFAULT = 'default';
    const SENDTYPE_MAIL = 'mail';
    const SENDTYPE_PDF = 'pdf';

    const MESSAGE_UNDEFINED = 0;
    const MESSAGE_INVITE = 1;
    const MESSAGE_ACKNOWLEDGE = 2;
    const MESSAGE_REPLY = 3;
    const MESSAGE_REQUEST = 4;
    const MESSAGE_REJECT = 4;
    const MESSAGE_ATTESTATION = 5;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

    /**
     * @var PAGEmachine\Ats\Domain\Repository\TextTemplateRepository
     * @inject
     */
    protected $textTemplateRepository;

    /**
     * @var \PAGEmachine\Ats\Service\MarkerService
     * @inject
     */
    protected $markerService;


    /**
     * @var Application $application
     */
    protected $application;

    /**
     * @return Application
     * @codeCoverageIgnore
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param Application $application
     * @return void
     * @codeCoverageIgnore
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @var string $textTemplate
     */
    protected $textTemplate;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getTextTemplate()
    {
        return $this->textTemplate;
    }

    /**
     * @param string $texttemplate
     * @return void
     * @codeCoverageIgnore
     */
    public function setTextTemplate($textTemplate)
    {
        $this->textTemplate = $textTemplate;
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    public function applyTextTemplate()
    {
        if ($this->getTextTemplate() != null) {
            $template = $this->textTemplateRepository->findByUid($this->getTextTemplate());
            $this->setSubject($template->getSubject());
            $this->setBody($template->getTextTemplate());
            $this->setTextTemplate(null);
        }
    }


    /**
     * @var array|null $textTemplateDropdownOptions
     */
    protected $textTemplateDropdownOptions = null;

    /**
     * @return array|null
     */
    public function getTextTemplateDropdownOptions()
    {
        if ($this->textTemplateDropdownOptions == null) {
            $this->textTemplateDropdownOptions = $this->textTemplateRepository->getDropdownOptionsForType($this->type);
        }

        return $this->textTemplateDropdownOptions;
    }

    /**
     * @param array|null $textTemplateDropdownOptions
     * @return void
     */
    public function setTextTemplateDropdownOptions($textTemplateDropdownOptions = null)
    {
        $this->textTemplateDropdownOptions = $textTemplateDropdownOptions;
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getBackendUser()
    {
        return $GLOBALS['BE_USER']->user;
    }


    /**
     * The action type that this message belongs to, like "invitation", "acknowledgement"...
     * @var int $type
     */
    protected $type = AbstractMessage::MESSAGE_UNDEFINED;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * The sendType, "pdf" or "mail"
     *
     * @var string $sendType
     */
    protected $sendType = AbstractMessage::SENDTYPE_MAIL;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getSendType()
    {
        return $this->sendType;
    }

    /**
     * @param string $sendType
     * @return void
     * @codeCoverageIgnore
     */
    public function setSendType($sendType)
    {
        $this->sendType = $sendType;
    }


    /**
     * @validate NotEmpty
     * @var string $subject
     */
    protected $subject;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return void
     * @codeCoverageIgnore
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }


    /**
     * @var string $body
     * @validate NotEmpty
     */
    protected $body = null;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return void
     * @codeCoverageIgnore
     */
    public function setBody($body)
    {
        $this->body = $body;
    }


    /**
     * @var string $renderedBody
     */
    protected $renderedBody = null;

    /**
     * For testing only
     *
     * @param string
     */
    public function setRenderedBody($renderedBody = null)
    {

        $this->renderedBody = $renderedBody;
    }

    /**
     * @return string|null
     */
    public function getRenderedBody()
    {
        if ($this->renderedBody != null) {
            return $this->renderedBody;
        } elseif ($this->body != null) {
            $this->renderedBody = $this->renderBody();
            return $this->renderedBody;
        } else {
            return null;
        }
    }


    /**
     * @var string $cc
     */
    protected $cc;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param string $cc
     * @return void
     * @codeCoverageIgnore
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
    }


    /**
     * @var string $bcc
     */
    protected $bcc;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param string $bcc
     * @return void
     * @codeCoverageIgnore
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;
    }

    /**
     * Renders the body
     *
     * @return string
     */
    public function renderBody()
    {

        $standaloneView = $this->objectManager->get(StandaloneView::class);

        $standaloneView->setTemplateSource(
            $this->markerService->replaceMarkers(
                $this->body,
                $this->sendType
            )
        );

        $standaloneView->assignMultiple([
            "application" => $this->application,
            "backenduser" => $this->getBackendUser(),
            "fields" => $this->getCustomFields(),
        ]);

        $renderedBody = $standaloneView->render();

        return $renderedBody;
    }

    /**
     * Return empty array by default
     *
     * @return array
     */
    public function getCustomFields()
    {
        return [];
    }


    /**
     * Sends the message
     *
     * @return void
     */
    public function send()
    {

        if ($this->sendType == AbstractMessage::SENDTYPE_MAIL) {
            MailService::getInstance()->sendReplyMail($this->application, $this->subject, $this->getRenderedBody(), $this->cc, $this->bcc);
        } elseif ($this->sendType == AbstractMessage::SENDTYPE_PDF) {
            PdfService::getInstance()->generateAndDownloadPdf($this->subject, $this->application, $this->getRenderedBody());
        }
    }
}
