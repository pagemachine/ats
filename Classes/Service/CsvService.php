<?php
namespace PAGEmachine\Ats\Service;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Domain\Model\Application;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class CsvService implements SingletonInterface
{
    public function __construct()
    {
    }

    /**
     * @codeCoverageIgnore
     * @return     CsvService
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * Gets the export options.
     *
     * @return array
     */
    public function getExportOptions()
    {
        $options = [
            'jobNumber',
            'job',
        ];
        
        return $options;
    }

    /**
     * Checks if Fields are allowed and if not filter them out
     *
     * @param  array $options
     * @return array
     */
    public function checkExportOptions($options)
    {
        $newOptions = [];
        foreach ($options as $key => $option) {
            if (in_array($option, $this->getExportOptions())) {
                $newOptions[] = $option;
            }
        }

        return $newOptions;
    }

    /**
     * Gets CSV 1st Row
     *
     * @param  array $options
     * @return string
     */
    protected function getExportHeader($options)
    {
        $exportHeader = '';
        foreach ($options as $option) {
            $exportHeader .= '"'.$option.'";';
        }
        $exportHeader .= "\r\n";

        return $exportHeader;
    }

    /**
     * Gets CSV Data
     *
     * @param  array $options
     * @param  Application $application
     * @param  string $fileName
     * @return string
     */
    protected function getExportBody($options, Application $application, $fileName)
    {
        $exportBody = '';        
        $row = [];

        $job = $application->getJob();

        foreach ($options as $option) {
            switch ($option) {
                case 'jobNumber':
                    $row[] = $job->getJobNumber();
                    break;
                case 'job':
                    $row[] = $job->getTitle();
                    break;
            } 
        }

        foreach ($row as $key => $value) {
            $exportBody .= '"'.( empty($value) ? '-' : str_replace('"', '""', $value)).'";';
        }

        $exportBody .= "\r\n";
        
        return $exportBody;
    }

    /**
     * Gets the job options.
     *
     * @param  array $options
     * @param  Application $application
     * @param   string $fileName
     * @return string
     */
    public function getExportData($options, Application $application, $fileName)
    {
        $exportData = '';

        if (!($options == null || $options == '')) {
            $options = $this->checkExportOptions($options);
            if (!($options == null || $options == '')) {
                $exportData = $this->getExportHeader($options).$this->getExportBody($options, $application, $fileName);
            }
        }

        return $exportData;
    }

    /**
     * Download CSV file.
     *
     * @param   Application $application
     * @param   string $fileName
     * @return  string
     */
    public function getCsv(Application $application, $fileName = 'application.csv')
    {
        $options = $this->getExportOptions();

        return $this->getExportData($options, $application, $fileName);
    }
}
