<?php
namespace PAGEmachine\Ats\Message;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class ReplyMessage extends AbstractMessage implements MessageInterface
{
    /**
     * @var int
     */
    protected $type = AbstractMessage::MESSAGE_REPLY;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getName()
    {

        return "ReplyMessage";
    }

   /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getHistoryName()
    {
        return "reply";
    }

    /**
     * ReplyMessage has no custom fields, return empty array
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getCustomFields()
    {
        return [];
    }
}
