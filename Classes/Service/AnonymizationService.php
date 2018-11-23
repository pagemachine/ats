<?php
namespace PAGEmachine\Ats\Service;

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
     * @param  string $className
     * @return void
     */
    public function anonymize($className)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->persistenceManager = $objectManager->get(PersistenceManager::class);

        $threshold = new \DateTime();
        $threshold->sub(
            \DateInterval::createFromDateString($this->getMinimumAnonymizationAge())
        );

        $repository = $this->findRepositoryForClass($className);

        $config = $this->getAnonymizationConfigurationForClassName($className);
        $counter = 0;

        foreach ($repository->findOldObjects($threshold) as $object) {
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

    /**
     * @return string
     */
    protected function getMinimumAnonymizationAge()
    {
        $settings = TyposcriptService::getInstance()->getSettings();
        return $settings['anonymization']['minimumAge'] ?: '120 days';
    }

    /**
     * Fetches config for anonymization for given class
     *
     * @param  string $className
     * @return array
     */
    protected function getAnonymizationConfigurationForClassName($className)
    {
        $settings = TyposcriptService::getInstance()->getSettings();

        if ($settings['anonymization']['objects'][$className]) {
            return $settings['anonymization']['objects'][$className];
        } else {
            throw new \PAGEmachine\Ats\Exception(sprintf('Could not find anonymization configuration for class %1$s. Check your TypoScript setup in path "module.tx_ats.anonymization.objects.%1$s.', $className), 1542970640);
        }
    }
}
