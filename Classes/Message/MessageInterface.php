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
     * @param string $type
     * @return void
     */
    public function setType($type);

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
}
