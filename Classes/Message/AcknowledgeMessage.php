<?php
namespace PAGEmachine\Ats\Message;

use PAGEmachine\Ats\Service\ExtconfService;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class AcknowledgeMessage extends AbstractMessage implements MessageInterface
{
    /**
     * @var int
     */
    protected $type = AbstractMessage::MESSAGE_ACKNOWLEDGE;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getName()
    {

        return "AcknowledgeMessage";
    }

   /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getHistoryName()
    {
        return "acknowledge";
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

    /**
     * Applies the template for the automatic acknowledgement E-Mail
     *
     * @return bool (Allowed to be send)
     */
    public function applyAutoAcknowledgeTemplate()
    {
        if (ExtconfService::getInstance()->getSendAutoAcknowledge()) {
            $templateUid = ExtconfService::getInstance()->getAutoAcknowledgeTemplate();
            if (array_key_exists($templateUid, $this->getTextTemplateDropdownOptions())) {
                $this->setTextTemplate($templateUid);
                $this->applyTextTemplate();
                return true;
            }
        }
        return false;
    }
}
