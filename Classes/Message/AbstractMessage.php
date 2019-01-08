<?php
namespace PAGEmachine\Ats\Message;

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Repository\TextTemplateRepository;
use PAGEmachine\Ats\Service\FluidRenderingService;
use PAGEmachine\Ats\Service\MailService;
use PAGEmachine\Ats\Service\MarkerService;
use PAGEmachine\Ats\Service\PdfService;
use PAGEmachine\Ats\Service\TyposcriptService;

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
    const MESSAGE_REJECT = 5;

    /**
     * @var TextTemplateRepository $textTemplateRepository
     */
    protected $textTemplateRepository;

    /**
     * @var MarkerService $markerService
     */
    protected $markerService;

    /**
     * @var FluidRenderingService $fluidRenderingService
     */
    protected $fluidRenderingService;

    /**
     * @var TyposcriptService $typoscriptService
     */
    protected $typoscriptService;

    /**
     * @param TextTemplateRepository $textTemplateRepository
     */
    public function injectTextTemplateRepository(TextTemplateRepository $textTemplateRepository)
    {
        $this->textTemplateRepository = $textTemplateRepository;
    }

    /**
     * @param MarkerService $markerService
     */
    public function injectMarkerService(MarkerService $markerService)
    {
        $this->markerService = $markerService;
    }

    /**
     * @param FluidRenderingService $fluidRenderingService
     */
    public function injectFluidRenderingService(FluidRenderingService $fluidRenderingService)
    {
        $this->fluidRenderingService = $fluidRenderingService;
    }

    /**
     * @param TyposcriptService $typoscriptService
     */
    public function injectTyposcriptService(TyposcriptService $typoscriptService)
    {
        $this->typoscriptService = $typoscriptService;
    }

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
     * @return int
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
     * @var bool
     */
    protected $useBackendUserCredentials = true;

    /**
     * @return bool
     */
    public function getUseBackendUserCredentials()
    {
        return $this->useBackendUserCredentials;
    }

    /**
     * @param bool $useBackendUserCredentials
     * @return void
     */
    public function setUseBackendUserCredentials($useBackendUserCredentials)
    {
        $this->useBackendUserCredentials = $useBackendUserCredentials;
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
     * Rendered subject with replaced markers
     *
     * @var string
     */
    protected $renderedSubject = null;

    /**
     *
     * @return string|null
     */
    public function getRenderedSubject()
    {
        if ($this->renderedSubject != null) {
            return $this->renderedSubject;
        } elseif ($this->subject != null) {
            $this->renderedSubject = $this->fluidRenderingService->render(
                $this->markerService->replaceMarkers(
                    $this->subject,
                    $this->sendType
                ),
                [
                    "application" => $this->application,
                    "backenduser" => $this->getBackendUser(),
                    "fields" => $this->getCustomFields(),
                ]
            );
            return $this->renderedSubject;
        } else {
            return null;
        }
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
     * @return string|null
     */
    public function getRenderedBody()
    {
        if ($this->renderedBody != null) {
            return $this->renderedBody;
        } elseif ($this->body != null) {
            $this->renderedBody = $this->fluidRenderingService->render(
                $this->markerService->replaceMarkers(
                    $this->body,
                    $this->sendType
                ),
                [
                    "application" => $this->application,
                    "backenduser" => $this->getBackendUser(),
                    "fields" => $this->getCustomFields(),
                ]
            );
            return $this->renderedBody;
        } else {
            return null;
        }
    }

    /**
     * Testing function
     *
     * @param string $renderedBody
     */
    public function setRenderedBody($renderedBody)
    {
        $this->renderedBody = $renderedBody;
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
     * @var string
     */
    protected $pdfFilePath = null;

    /**
     * @return string
     */
    public function getPdfFilePath()
    {
        return $this->pdfFilePath;
    }

    /**
     * @param string $pdfFilePath
     * @return void
     */
    public function setPdfFilePath($pdfFilePath)
    {
        $this->pdfFilePath = $pdfFilePath;
    }

    /**
     * Returns a download filename for the generated pdf file
     *
     * @return string
     */
    public function getFileName()
    {
        return PdfService::getInstance()->createCleanedFilename($this->getRenderedSubject() . '_' . $this->application->getSurname() . '_' . date('Y-m-d'));
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
     * Generates a pdf and returns the file path
     *
     * @param  string $fileName
     *
     * @return string filepath
     */
    public function generatePdf($fileName = 'download.pdf')
    {
        if ($this->sendType == AbstractMessage::SENDTYPE_PDF) {
            $filePath = PdfService::getInstance()->generatePdf($this->application, $this->getRenderedSubject(), $this->getRenderedBody(), $fileName);
            $this->setPdfFilePath($filePath);
            return $filePath;
        }
        return false;
    }

    /**
     * Sends the message
     *
     * @return void
     */
    public function send()
    {

        if ($this->sendType == AbstractMessage::SENDTYPE_MAIL) {
            MailService::getInstance()->sendReplyMail(
                $this->application,
                $this->getRenderedSubject(),
                $this->getRenderedBody(),
                $this->cc,
                $this->bcc,
                $this->useBackendUserCredentials
            );
        } elseif ($this->sendType == AbstractMessage::SENDTYPE_PDF) {
            PdfService::getInstance()->generateAndDownloadPdf($this->getRenderedSubject(), $this->application, $this->getRenderedBody());
        }
    }
}
