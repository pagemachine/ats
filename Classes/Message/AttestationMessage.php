<?php
namespace PAGEmachine\Ats\Message;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class AttestationMessage extends AbstractMessage
{
    /**
     * @var int
     */
    protected $type = AbstractMessage::MESSAGE_ATTESTATION;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getName()
    {

        return "AttestationMessage";
    }

    /**
     * AttestationMessage has no custom fields, return empty array
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getCustomFields()
    {
        return [];
    }
}
