<?php
namespace PAGEmachine\Ats\Message;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class RejectMessage extends AbstractMessage implements MessageInterface
{
    /**
     * @var int
     */
    protected $type = AbstractMessage::MESSAGE_REJECT;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getName()
    {

        return "RejectMessage";
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
