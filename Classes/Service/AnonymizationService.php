<?php
namespace PAGEmachine\Ats\Service;

use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class AnonymizationService
{
    /**
     * @return AnonymizationService
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    /**
     * Anonymizes records of given classname
     *
     * @param  string $className
     * @return void
     */
    public function anonymize($className)
    {
        $threshold = new \DateTime();
        $threshold->sub(
            \DateInterval::createFromDateString($this->getMinimumAnonymizationAge())
        );

        $repository = $this->findRepositoryForClass($className);

        $config = $this->getAnonymizationConfigurationForClassName($className);
        $counter = 0;

        foreach ($repository->findOldObjects($threshold) as $object) {
            foreach ($config['properties'] as $property => $value) {
                $object->_setProperty($property, $value);
            }
            $object->setAnonymized(true);
            $repository->update($object);

            $counter++;

            if ($counter >= 20) {
                $repository->persistAll();
                $counter = 0;
            }
        }

        $repository->persistAll();
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
