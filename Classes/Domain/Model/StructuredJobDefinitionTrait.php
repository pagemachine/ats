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
     * This is a compat function since in TYPO3 7 Fluid does not include f:format.json.
     * @TODO Remove this and use {job -> f:format.json()} in the JsonLD template
     * once we drop TYPO3 7 support
     *
     * @return string
     */
    public function getJson()
    {
        return json_encode($this);
    }

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
                    'addressRegion' => $settings['companyData']['region'],
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
     * @return string
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * @var float $baseSalary
     */
    protected $baseSalary;

    /**
     * @return float
     */
    public function getBaseSalary()
    {
        return $this->baseSalary;
    }

    /**
     * @var \SJBR\StaticInfoTables\Domain\Model\Currency $baseSalaryCurrency
     */
    protected $baseSalaryCurrency;

    /**
     * @return \SJBR\StaticInfoTables\Domain\Model\Currency
     */
    public function getBaseSalaryCurrency()
    {
        return $this->baseSalaryCurrency;
    }

    /**
     * @var string $baseSalaryUnit
     */
    protected $baseSalaryUnit;

    /**
     * @return string
     */
    public function getBaseSalaryUnit()
    {
        return $this->baseSalaryUnit;
    }

    /**
     * @var string $educationRequirements
     */
    protected $educationRequirements;

    /**
     * @return string
     */
    public function getEducationRequirements()
    {
        return $this->educationRequirements;
    }

    /**
     * @var string $employmentType
     */
    protected $employmentType;

    /**
     * @return string
     */
    public function getEmploymentType()
    {
        return $this->employmentType;
    }

    /**
     * @var string $experienceRequirements
     */
    protected $experienceRequirements;

    /**
     * @return string
     */
    public function getExperienceRequirements()
    {
        return $this->experienceRequirements;
    }

    /**
     * @var bool $overrideGlobalHiringOrganization
     */
    protected $overrideGlobalHiringOrganization;

    /**
     * @return bool
     */
    public function getOverrideGlobalHiringOrganization()
    {
        return $this->overrideGlobalHiringOrganization;
    }

    /**
     * @var string $hiringOrganization
     */
    protected $hiringOrganization;

    /**
     * @return string
     */
    public function getHiringOrganization()
    {
        return $this->hiringOrganization;
    }

    /**
     * @var string $incentiveCompensation
     */
    protected $incentiveCompensation;

    /**
     * @return string
     */
    public function getIncentiveCompensation()
    {
        return $this->incentiveCompensation;
    }

    /**
     * @var string $industry
     */
    protected $industry;

    /**
     * @return string
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * @var string $jobBenefits
     */
    protected $jobBenefits;

    /**
     * @return string
     */
    public function getJobBenefits()
    {
        return $this->jobBenefits;
    }

    /**
     * @var bool $overrideGlobalLocation
     */
    protected $overrideGlobalLocation;

    /**
     * @return bool
     */
    public function getOverrideGlobalLocation()
    {
        return $this->overrideGlobalLocation;
    }

    /**
     * @var \SJBR\StaticInfoTables\Domain\Model\Country $jobLocationAddressCountry
     */
    protected $jobLocationAddressCountry;

    /**
     * @return \SJBR\StaticInfoTables\Domain\Model\Country
     */
    public function getJobLocationAddressCountry()
    {
        return $this->jobLocationAddressCountry;
    }

    /**
     * @var string $jobLocationAddressLocality
     */
    protected $jobLocationAddressLocality;

    /**
     * @return string
     */
    public function getJobLocationAddressLocality()
    {
        return $this->jobLocationAddressLocality;
    }

    /**
     * @var \SJBR\StaticInfoTables\Domain\Model\CountryZone $jobLocationAddressRegion
     */
    protected $jobLocationAddressRegion;

    /**
     * @return \SJBR\StaticInfoTables\Domain\Model\CountryZone
     */
    public function getJobLocationAddressRegion()
    {
        return $this->jobLocationAddressRegion;
    }

    /**
     * @var string $jobLocationAddressPostalCode
     */
    protected $jobLocationAddressPostalCode;

    /**
     * @return string
     */
    public function getJobLocationAddressPostalCode()
    {
        return $this->jobLocationAddressPostalCode;
    }

    /**
     * @var string $jobLocationAddressStreetAddress
     */
    protected $jobLocationAddressStreetAddress;

    /**
     * @return string
     */
    public function getJobLocationAddressStreetAddress()
    {
        return $this->jobLocationAddressStreetAddress;
    }

    /**
     * @var string $occupationalCategory
     */
    protected $occupationalCategory;

    /**
     * @return string
     */
    public function getOccupationalCategory()
    {
        return $this->occupationalCategory;
    }

    /**
     * @var string $qualifications
     */
    protected $qualifications;

    /**
     * @return string
     */
    public function getQualifications()
    {
        return $this->qualifications;
    }

    /**
     * @var string $responsibilities
     */
    protected $responsibilities;

    /**
     * @return string
     */
    public function getResponsibilities()
    {
        return $this->responsibilities;
    }

    /**
     * @var string $skills
     */
    protected $skills;

    /**
     * @return string
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * @var string $specialCommitments
     */
    protected $specialCommitments;

    /**
     * @return string
     */
    public function getSpecialCommitments()
    {
        return $this->specialCommitments;
    }

    /**
     * @var string $workHours
     */
    protected $workHours;

    /**
     * @return string
     */
    public function getWorkHours()
    {
        return $this->workHours;
    }
}
