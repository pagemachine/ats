<?php
namespace PAGEmachine\Ats\Service;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Domain\Model\Application;
use TYPO3\CMS\Core\SingletonInterface;

class HashServiceUrl implements SingletonInterface
{
    
    /**
     * Mecamic Url
     * @var string
     */
    const VIDEO_URL = 'https://mecamic.com/videocall-';


    /* * @var string $uid
     */
    protected $uid;

    /* * @var string $firstname
     */
    protected $firstname;

    /* * @var string $surname
     */
    protected $surname;

    /* * @var string $str
     */
    protected $str;

    /* * @var string $hashedUrl
     */
    protected $hashedUrl;
 
    public function __construct(Application $application)
    {
        $this->uid = strval($application->getUid());
        $this->firstname = $application->getFirstname(); 
        $this->surname = $application->getSurname();
        $this->str = implode('', array($this->uid, $this->firstname, $this->surname));
        $this->hashedUrl =implode('', array(self::VIDEO_URL, $this->hashFkt($this->str, $this->generateSalt(), 1000))); 
    }

    /**
     * @return string $hashedUrl
     * @codeCoverageIgnore
     */
    public function getHashedUrl() {
        return $this->hashedUrl;
    }

    /**
     * @return string $salt
     * @codeCoverageIgnore
     */
    private function generateSalt(){
        $randStr = random_bytes(32);
        $salt = bin2hex($randStr);
        return  $salt;
    }

    /**
     * @param string $str
     * @param string $salt
     * @param int $iterations
     * @return string $str
     * @codeCoverageIgnore
     */
    private function hashFkt($str, $salt, $iterations) {
        for ($x=0; $x<$iterations; $x++) {
            $str = hash("sha256", $str . $salt);
        }
        return $str;     
    }


}