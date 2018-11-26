<?php
namespace PAGEmachine\Ats\Service;

use PAGEmachine\Ats\Domain\Model\FileReference;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class AnonymizationService
{
    const ANONYMIZATION_MODE_ANONYMIZE = 'anonymize';
    const ANONYMIZATION_MODE_ANONYMIZE_DELETE = 'anonymize_and_delete';
    const ANONYMIZATION_MODE_DELETE_FILES = 'delete_files';

    /**
     * @return AnonymizationService
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * Anonymizes records of given classname
     *
     * @param string $className
     * @param string $minimumAge
     * @param array $config
     * @return void
     */
    public function anonymize($className, $minimumAge, $config)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->persistenceManager = $objectManager->get(PersistenceManager::class);

        $threshold = new \DateTime();
        $threshold->sub(
            \DateInterval::createFromDateString($minimumAge)
        );

        $repository = $this->findRepositoryForClass($className);
        $counter = 0;

        foreach ($repository->findOldObjects($threshold, $config['conditions']) as $object) {
            $this->anonymizeObject($object, $config);

            $counter++;

            if ($counter >= 20) {
                $this->persistenceManager->persistAll();
                $counter = 0;
            }
        }

        $this->persistenceManager->persistAll();
    }

    /**
     * Anonymizes a single object
     *
     * @param  AbstractDomainObject $object
     * @param  array               $config
     * @return void
     */
    protected function anonymizeObject(AbstractDomainObject $object, $config)
    {
        if ($object->_hasProperty('anonymized')) {
            $object->setAnonymized(true);
        }

        switch ($config['mode']) {
            case self::ANONYMIZATION_MODE_ANONYMIZE:
                foreach ($config['properties'] as $property => $value) {
                    $object->_setProperty($property, $value);
                }
                $this->persistenceManager->update($object);
                break;
            case self::ANONYMIZATION_MODE_ANONYMIZE_DELETE:
                foreach ($config['properties'] as $property => $value) {
                    $object->_setProperty($property, $value);
                }
                $this->persistenceManager->remove($object);
                break;
            case self::ANONYMIZATION_MODE_DELETE_FILES:
                $this->deleteFile($object);
                break;
        }
        if (!empty($config['children'])) {
            foreach ($config['children'] as $propertyName => $childConfig) {
                $property = $object->_getProperty($propertyName);

                if ($property instanceof ObjectStorage) {
                    foreach ($property as $child) {
                        $this->anonymizeObject($child, $childConfig);
                    }
                } else {
                    throw new IllegalObjectTypeException('Only ObjectStorages are supported for anonymization', 1542985424);
                }
            }
        }
    }

    /**
     * Deletes the file behind given file reference.
     * The reference itself is deleted automatically.
     *
     * @param  FileReference $fileReference
     * @return void
     */
    protected function deleteFile(FileReference $fileReference)
    {
        $originalFile = $fileReference->getOriginalResource()->getOriginalFile();

        if ($originalFile->exists()) {
            $originalFile->getStorage()->deleteFile($originalFile);
        }
    }

    /**
     * Tries to find a repository for given classname
     *
     * @param  string $className
     * @return \TYPO3\CMS\Extbase\Persistence\RepositoryInterface
     */
    protected function findRepositoryForClass($className)
    {
        try {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $repositoryClassName = ClassNamingUtility::translateModelNameToRepositoryName($className);
            $repository = $objectManager->get($repositoryClassName);
            return $repository;
        } catch (UnknownObjectException $e) {
            throw new \PAGEmachine\Ats\Exception(sprintf('Repository for class %s not found. Stopping anonymization.', $className), 1542970640);
        }
    }
}
