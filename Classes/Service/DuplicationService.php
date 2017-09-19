<?php
namespace PAGEmachine\Ats\Service;

use PAGEmachine\Ats\Domain\Model\CloneableInterface;
use PAGEmachine\Ats\Domain\Model\FileReference;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
/*
 * This file is part of the PAGEmachine ATS project.
 */


class DuplicationService implements SingletonInterface {


    /**
     * @var ObjectManager $objectManager
     */
    protected $objectManager;


    /**
     * @var DataMapper $dataMapper
     */
    protected $dataMapper;
    
    
    /**
     * @codeCoverageIgnore
     * @return DuplicationService
     */
    public static function getInstance() {

        return GeneralUtility::makeInstance(self::class);
    }

    /**
     *
     * @param ObjectManager|null $objectManager
     * @param DataMapper|null    $dataMapper
     */
    public function __construct(ObjectManager $objectManager = null, DataMapper $dataMapper = null) {

        $this->objectManager = $objectManager ?: GeneralUtility::makeInstance(ObjectManager::class);
        $this->dataMapper = $dataMapper ?: $this->objectManager->get(DataMapper::class);
    }

    /**
     * Duplicates an object
     *
     * @param  CloneableInterface $object
     * @return CloneableInterface $clone
     */
    public function duplicateObject(CloneableInterface $object)
    {
        $clone = $this->objectManager->get(get_class($object));

        foreach ($object->_getProperties() as $propertyName => $value) {

            if ($propertyName == "uid") {
                continue;
            }

            if (ObjectAccess::isPropertyGettable($object, $propertyName)) {

                // Clone ObjectStorages
                if ($value instanceof ObjectStorage && $value->current() instanceof CloneableInterface) {

                    $clone->_setProperty($propertyName, $this->duplicateStorage($value, $propertyName, $clone));
                    continue;
                }

                // Clone File ObjectStorages
                if ($value instanceof ObjectStorage && $value->current() instanceof FileReference) {


                    $clone->_setProperty($propertyName, $this->duplicateFileStorage($value));
                    continue;
                }

                // Copy all plain properties
                $clone->_setProperty($propertyName, $value);
            }   
        }

        return $clone;
    }

    /**
     * Duplicates an ObjectStorage.
     * Also handles the relation field on child side (sets it to the new parent)
     *
     * @param  ObjectStorage      $value     The OS to copy
     * @param  string             $fieldName The parent fieldName this ObjectStorage is located at
     * @param  CloneableInterface $newParent The parent to assign to the given parent fieldname
     * @return ObjectStorage      $storage   The copy
     */
    public function duplicateStorage(ObjectStorage $value, $fieldName, CloneableInterface $newParent)
    {
        $columnMap = $this->dataMapper->getDataMap(get_class($newParent))->getColumnMap($fieldName);
        $parentField = $columnMap->getParentKeyFieldName();

        $storage = new ObjectStorage();
        $value->rewind();

        foreach ($value as $child) {
            $childClone = $this->duplicateObject($child);
            $childClone->_setProperty($parentField, $newParent);

            $storage->attach($childClone);
        }

        return $storage;
    }

    /**
     * Duplicates an ObjectStorage containing FAL FileReferences.
     * Also copies the files (a unique name is created by the storage).
     *
     * @param  ObjectStorage      $value     The OS to copy
     * @return ObjectStorage      $storage   The copy
     */
    public function duplicateFileStorage(ObjectStorage $value)
    {
        $storage = new ObjectStorage();
        $value->rewind();

        foreach ($value as $reference) {

            $originalFile = $reference->getOriginalResource()->getOriginalFile();
            $fileCopy = $originalFile->copyTo($originalFile->getParentFolder());

            $newReference = $this->objectManager->get(FileReference::class);
            $newReference->setFile($fileCopy);

            $storage->attach($newReference);

            return $storage;        
        }        
    }
    

}
