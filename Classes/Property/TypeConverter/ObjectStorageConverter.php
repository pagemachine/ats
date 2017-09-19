<?php
namespace PAGEmachine\Ats\Property\TypeConverter;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Class ObjectStorageConverter
 */
class ObjectStorageConverter extends \TYPO3\CMS\Extbase\Property\TypeConverter\ObjectStorageConverter
{
    /**
     * Take precedence over the available ObjectStorageConverter
     *
     * @var int
     */
    protected $priority = 20;

    /**
     * Return the source, if it is an array, otherwise an empty array.
     * Filter out empty uploads
     *
     * @param mixed $source
     * @return array
     * @api
     */
    public function getSourceChildPropertiesToBeConverted($source)
    {
        $propertiesToConvert = array();

        if (!empty($source)) {
            // TODO: Find a nicer way to throw away empty uploads
            foreach ($source as $propertyName => $propertyValue) {
                if ($this->isUploadType($propertyValue)) {
                    if ($propertyValue['error'] !== \UPLOAD_ERR_NO_FILE || isset($propertyValue['submittedFile']['resourcePointer'])) {
                        $propertiesToConvert[$propertyName] = $propertyValue;
                    }
                } else {
                    $propertiesToConvert[$propertyName] = $propertyValue;
                }
            }
        }



        return $propertiesToConvert;
    }

    /**
     * Check if this is an upload type
     *
     * @param mixed $propertyValue
     * @return bool
     */
    protected function isUploadType($propertyValue)
    {
        return is_array($propertyValue) && isset($propertyValue['tmp_name']) && isset($propertyValue['error']);
    }
}
