<?php
namespace PAGEmachine\Ats\Message;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class InviteMessage extends AbstractMessage
{
    /**
     * @var int
     */
    protected $type = AbstractMessage::MESSAGE_INVITE;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getName()
    {

        return "InviteMessage";
    }

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime;

    /**
     * @return \DateTime
     * @codeCoverageIgnore
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param \DateTime $dateTime
     * @return void
     * @codeCoverageIgnore
     */
    public function setDateTime(\DateTime $dateTime = null)
    {
        $this->dateTime = $dateTime;
    }

    public function getDate()
    {

        if ($this->dateTime) {
            return $this->dateTime->format("Y-m-d");
        }

        return null;
    }

    public function getTime()
    {

        if ($this->dateTime) {
            return $this->dateTime->format("H:i");
        }

        return null;
    }



    /**
     * @var \DateTime $confirmDate
     */
    protected $confirmDate;

    /**
     * @return \DateTime
     * @codeCoverageIgnore
     */
    public function getConfirmDate()
    {
        return $this->confirmDate;
    }

    /**
     * Returns confirm date as string (for marker parsing)
     *
     * @return string
     */
    public function getConfirmDateString()
    {
        if ($this->confirmDate) {
            return $this->confirmDate->format("Y-m-d");
        }
        return null;
    }

    /**
     * @param \DateTime $confirmDate
     * @return void
     * @codeCoverageIgnore
     */
    public function setConfirmDate(\DateTime $confirmDate = null)
    {
        $this->confirmDate = $confirmDate;
    }


    /**
     * @var string $building
     */
    protected $building;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @param string $building
     * @return void
     * @codeCoverageIgnore
     */
    public function setBuilding($building)
    {
        $this->building = $building;
    }

    /**
     * @var string $room
     */
    protected $room;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param string $room
     * @return void
     * @codeCoverageIgnore
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * Invite message custom fields
     *
     * @return array
     */
    public function getCustomFields()
    {
        return [
            'date' => $this->getDate(),
            'time' => $this->getTime(),
            'confirmDate' => $this->getConfirmDateString(),
            'building' => $this->getBuilding(),
            'room' => $this->getRoom(),
        ];
    }
}
