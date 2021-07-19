<?php
namespace PAGEmachine\Ats\Message;

/*
 * This file is part of the PAGEmachine ATS project.
 */
use PAGEmachine\Ats\Service\HashServiceUrl;
use PAGEmachine\Ats\Service\IcsGeneratorService;

class VideoInvitationMessage extends AbstractMessage implements MessageInterface
{

    /**
     * @var int
     */
    protected $type = AbstractMessage::MESSAGE_VIDEOINVITATION;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getName()
    {

        return "VideoInvitationMessage";
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getHistoryName()
    {
        return "videoInvitation";
    }


    /**
     * @var \DateTime $dateTime
     */
    protected $date;

    /**
     * @return \DateTime
     * @codeCoverageIgnore
     */
    public function getDate()
    {
        return $this->date;
    }

    public function getDateString()
    {

        if ($this->date) {
            return $this->date->format(
                $this->typoscriptService->getSettings()['dateFormat'] ?: 'Y-m-d'
            );
        }

        return null;
    }

    /**
     * @param \DateTime $dateTime
     * @return void
     * @codeCoverageIgnore
     */
    public function setDate(\DateTime $date = null)
    {
        $this->date = $date;
    }


    /**
     * @var \DateTime $dateTime
     */
    protected $appointmentFrom;

    /**
     * @return \DateTime
     * @codeCoverageIgnore
     */
    public function getAppointmentFrom()
    {
        return $this->appointmentFrom;
    }

    public function getAppointmentFromString()
    {

        if ($this->appointmentFrom) {
            return $this->appointmentFrom->format(
                $this->typoscriptService->getSettings()['timeFormat'] ?: 'Y-m-d'
            );
        }

        return null;
    }

    /**
     * @param \DateTime $dateTime
     * @return void
     * @codeCoverageIgnore
     */
    public function setAppointmentFrom(\DateTime $appointmentFrom = null)
    {
        $this->appointmentFrom = $appointmentFrom;
    }

    /**
     * @var \DateTime $dateTime
     */
    protected $appointmentUntil;

    /**
     * @return \DateTime
     * @codeCoverageIgnore
     */
    public function getAppointmentUntil()
    {
        return $this->appointmentUntil;
    }


    public function getAppointmentUntilString()
    {

        if ($this->appointmentUntil) {
            return $this->appointmentUntil->format(
                $this->typoscriptService->getSettings()['timeFormat'] ?: 'Y-m-d'
            );
        }

        return null;
    }

    /**
     * @param \DateTime $dateTime
     * @return void
     * @codeCoverageIgnore
     */
    public function setAppointmentUntil(\DateTime $appointmentUntil = null)
    {
        $this->appointmentUntil = $appointmentUntil;
    }

    /**
     * @var string $hashUrl
     * 
     */
    protected $hashUrl;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getUrl()
    {
        $hash = new HashServiceUrl($this->application);
        return $hash->getHashedUrl();
    }


    /**
     * @var string $ics
     */
    protected $ics;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getIcs()
    {
        $icsFile = new IcsGeneratorService($this);
        $this->ics = $icsFile->icsProps_to_string();
        return $this->ics;
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getCustomFields()
    {
        return [
            'date' => $this->getDateString(),
            'appointmentFrom' => $this->getAppointmentFromString(),
            'appointmentUntil' => $this->getAppointmentUntilString(),
            'url' => $this->getUrl(),
        ];
    }

    /**
     * Sends the message
     * @return void
     */
    public function send()
    {
        $this->setAttachments([$this->getIcs()]);
        return parent::send();
    }
}