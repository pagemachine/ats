<?php
namespace PAGEmachine\Ats\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/*
 * This file is part of the PAGEmachine ATS project.
 */


/**
 * ApplicationE
 * @codeCoverageIgnore
 */
class ApplicationE extends ApplicationD
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->files = new ObjectStorage();
    }

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<PAGEmachine\Ats\Domain\Model\FileReference>
     * @lazy
     */
    protected $files;

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $files
     * @return void
     */
    public function setFiles(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $files)
    {
        $this->files = $files;
    }

    /**
     * @param PAGEmachine\Ats\Domain\Model\FileReference $file
     * @return void
     */
    public function addFile(FileReference $file)
    {
        $this->files->attach($file);
    }

    /**
     * @param PAGEmachine\Ats\Domain\Model\FileReference $file
     * @return void
     */
    public function removeFile(FileReference $file)
    {
        $this->files->detach($file);
    }
}
