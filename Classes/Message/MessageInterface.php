<?php
namespace PAGEmachine\Ats\Message;

use PAGEmachine\Ats\Domain\Model\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */

interface MessageInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getHistoryName();

    /**
     * @return Application
     */
    public function getApplication();

    /**
     * @param Application $Application
     * @return void
     */
    public function setApplication(Application $Application);

    /**
     * @return array
     */
    public function getBackendUser();

    /**
     * Returns the custom fields to render in the form
     *
     * @return array
     */
    public function getCustomFields();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getSendType();

    /**
     * @param string $sendType
     * @return void
     */
    public function setSendType($sendType);

    /**
     * @return string|null
     */
    public function getRenderedBody();

    /**
     * Sends the message
     *
     * @return void
     */
    public function send();

    /**
     * Applies the text template
     *
     * @return void
     */
    public function applyTextTemplate();


    /**
     * @return string
     */
    public function getSubject();

    /**
     * @param string $subject
     * @return void
     */
    public function setSubject($subject);

    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string $body
     * @return void
     */
    public function setBody($body);

    /**
     * @return string
     */
    public function getPdfFilePath();
}
