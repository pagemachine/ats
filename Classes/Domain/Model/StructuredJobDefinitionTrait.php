<?php
namespace PAGEmachine\Ats\Domain\Model;

use PAGEmachine\Ats\Service\TyposcriptService;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * StructuredJobDefinitionTrait
 */
trait StructuredJobDefinitionTrait
{
    /**
     * Returns structured JSON-LD data when run through json_encode()
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [
            '@context' => 'http://schema.org',
            '@type' => 'JobPosting',
            'title' => $this->getJobTitle(),
            'datePosted' => $this->getCreationDate()->format('Y-m-d'),
            'description' => $this->getDescription() . $this->getDescriptionAfterLink(),
            'baseSalary' => [
                '@type' => 'MonetaryAmount',
                'currency' => $this->getBaseSalaryCurrency() ? $this->getBaseSalaryCurrency()->getIsoCodeA3() : '',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'value' => $this->getBaseSalary(),
                    'unitText' => $this->getBaseSalaryUnit(),
                ],
            ],
            'educationRequirements' => $this->getEducationRequirements(),
            'employmentType' => $this->getEmploymentType(),
            'experienceRequirements' => $this->getExperienceRequirements(),
            'hiringOrganization' => $this->getStructuredHiringOrganization(),
            'incentiveCompensation' => $this->getIncentiveCompensation(),
            'industry' => $this->getIndustry(),
            'jobBenefits' => $this->getJobBenefits(),
            'jobLocation' => $this->getStructuredLocationData(),
            'occupationalCategory' => $this->getOccupationalCategory(),
            'qualifications' => $this->getQualifications(),
            'responsibilities' => $this->getResponsibilities(),
            'skills' => $this->getSkills(),
            'specialCommitments' => $this->getSpecialCommitments(),
            'workHours' => $this->getWorkHours(),
        ];

        if ($this->getEndtime() != null) {
            $data['validThrough'] = $this->getEndtime()->format('Y-m-d');
        }

        return $data;
    }

    /**
     * Returns the hiring organization in structured form
     *
     * @return array
     */
    public function getStructuredHiringOrganization()
    {
        if ($this->overrideGlobalHiringOrganization) {
            $data = [
                '@type' => 'Organization',
                'name' => $this->getHiringOrganization(),
            ];
        } else {
            $settings = TyposcriptService::getInstance()->getSettings();
            $data = [
                '@type' => 'Organization',
                'name' => $settings['companyData']['name'],
            ];
        }
        return $data;
    }

    /**
     * Returns job location data in structured form
     *
     * @return array
     */
    public function getStructuredLocationData()
    {
        if ($this->overrideGlobalLocation) {
            $data = [
                '@type' => 'Place',
                'address' => [
                    'streetAddress' => $this->getJobLocationAddressStreetAddress(),
                    'addressLocality' => $this->getJobLocationAddressLocality(),
                    'addressRegion' => $this->getJobLocationAddressRegion() ? $this->getJobLocationAddressRegion()->getLocalName() : '',
                    'postalCode' => $this->getJobLocationAddressPostalCode(),
                    'addressCountry' => $this->getJobLocationAddressCountry() ? $this->getJobLocationAddressCountry()->getIsoCodeA3() : '',
                ],
            ];
        } else {
            $settings = TyposcriptService::getInstance()->getSettings();
            $data = [
                '@type' => 'Place',
                'address' => [
                    'streetAddress' => $settings['companyData']['street'],
                    'addressLocality' => $settings['companyData']['locality'],
                    'addressRegion' => $settings['companyData']['locality'],
                    'postalCode' => $settings['companyData']['postalCode'],
                    'addressCountry' => $settings['companyData']['country'],
                ],
            ];
        }
        return $data;
    }

    /**
     * @var string $jobTitle
     */
    protected $jobTitle;


    /**
     * @var float $baseSalary
     */
    protected $baseSalary;


    /**
     * @var SJBR\StaticInfoTables\Domain\Model\Currency $baseSalaryCurrency
     */
    protected $baseSalaryCurrency;


    /**
     * @var string $baseSalaryUnit
     */
    protected $baseSalaryUnit;

    /**
     * @var string $educationRequirements
     */
    protected $educationRequirements;

    /**
     * @var string $employmentType
     */
    protected $employmentType;


    /**
     * @var string $experienceRequirements
     */
    protected $experienceRequirements;


    /**
     * @var bool $overrideGlobalHiringOrganization
     */
    protected $overrideGlobalHiringOrganization;


    /**
     * @var string $hiringOrganization
     */
    protected $hiringOrganization;

    /**
     * @var string $incentiveCompensation
     */
    protected $incentiveCompensation;


    /**
     * @var string $industry
     */
    protected $industry;

    /**
     * @var string $jobBenefits
     */
    protected $jobBenefits;

    /**
     * @var bool $overrideGlobalLocation
     */
    protected $overrideGlobalLocation;

    /**
     * @var \SJBR\StaticInfoTables\Domain\Model\Country $jobLocationAddressCountry
     */
    protected $jobLocationAddressCountry;

    /**
     * @var string $jobLocationAddressLocality
     */
    protected $jobLocationAddressLocality;

    /**
     * @var \SJBR\StaticInfoTables\Domain\Model\CountryZone $jobLocationAddressRegion
     */
    protected $jobLocationAddressRegion;


    /**
     * @var string $jobLocationAddressPostalCode
     */
    protected $jobLocationAddressPostalCode;

    /**
     * @var string $jobLocationAddressStreetAddress
     */
    protected $jobLocationAddressStreetAddress;

    /**
     * @var string $occupationalCategory
     */
    protected $occupationalCategory;

    /**
     * @var string $qualifications
     */
    protected $qualifications;

    /**
     * @var string $responsibilities
     */
    protected $responsibilities;

    /**
     * @var string $skills
     */
    protected $skills;

    /**
     * @var string $specialCommitments
     */
    protected $specialCommitments;

    /**
     * @var string $workHours
     */
    protected $workHours;
}
