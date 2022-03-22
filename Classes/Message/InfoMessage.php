<?php
namespace PAGEmachine\Ats\Message;

use PAGEmachine\Ats\Service\ExtconfService;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class InfoMessage extends AbstractMessage implements MessageInterface
{
    /**
     * @var int
     */
    protected $type = AbstractMessage::MESSAGE_INFO;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getName()
    {

        return "InfoMessage";
    }

   /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getHistoryName()
    {
        return "info";
    }

    /**
     * InfoMessage has no custom fields, return empty array
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getCustomFields()
    {
        return [];
    }

    /**
     * Return template
     *
     * @return array
     */
    public function getTemplate()
    {
        return 'Mail/InfoHtml';
    }

    /**
     * Applies the template for the automatic info E-Mail
     *
     * @return bool (Allowed to be send)
     */
    public function applyAutoInfoTemplate()
    {
        $templateUid = ExtconfService::getInstance()->getAutoInfoTemplate();

        if (array_key_exists($templateUid, $this->getTextTemplateDropdownOptions())) {
            $this->setTextTemplate($templateUid);
            $this->applyTextTemplate();
            return true;
        }
        return false;
    }
}
