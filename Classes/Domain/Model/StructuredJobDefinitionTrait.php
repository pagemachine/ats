<?php
namespace PAGEmachine\Ats\Domain\Model;

use PAGEmachine\Ats\Service\TyposcriptService;
use SJBR\StaticInfoTables\Domain\Model\Country;
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use SJBR\StaticInfoTables\Domain\Model\Currency;

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
     * @return string
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * @param string $jobTitle
     * @return void
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;
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
     * @param float $baseSalary
     * @return void
     */
    public function setBaseSalary($baseSalary)
    {
        $this->baseSalary = $baseSalary;
    }


    /**
     * @var SJBR\StaticInfoTables\Domain\Model\Currency $baseSalaryCurrency
     */
    protected $baseSalaryCurrency;

    /**
     * @return SJBR\StaticInfoTables\Domain\Model\Currency
     */
    public function getBaseSalaryCurrency()
    {
        return $this->baseSalaryCurrency;
    }

    /**
     * @param SJBR\StaticInfoTables\Domain\Model\Currency $baseSalaryCurrency
     * @return void
     */
    public function setBaseSalaryCurrency(Currency $baseSalaryCurrency)
    {
        $this->baseSalaryCurrency = $baseSalaryCurrency;
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
     * @param string $baseSalaryUnit
     * @return void
     */
    public function setBaseSalaryUnit($baseSalaryUnit)
    {
        $this->baseSalaryUnit = $baseSalaryUnit;
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
     * @param string $educationRequirements
     * @return void
     */
    public function setEducationRequirements($educationRequirements)
    {
        $this->educationRequirements = $educationRequirements;
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
     * @param string $employmentType
     * @return void
     */
    public function setEmploymentType($employmentType)
    {
        $this->employmentType = $employmentType;
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
     * @param string $experienceRequirements
     * @return void
     */
    public function setExperienceRequirements($experienceRequirements)
    {
        $this->experienceRequirements = $experienceRequirements;
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
     * @param bool $overrideGlobalHiringOrganization
     * @return void
     */
    public function setOverrideGlobalHiringOrganization($overrideGlobalHiringOrganization)
    {
        $this->overrideGlobalHiringOrganization = $overrideGlobalHiringOrganization;
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
     * @param string $hiringOrganization
     * @return void
     */
    public function setHiringOrganization($hiringOrganization)
    {
        $this->hiringOrganization = $hiringOrganization;
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
     * @param string $incentiveCompensation
     * @return void
     */
    public function setIncentiveCompensation($incentiveCompensation)
    {
        $this->incentiveCompensation = $incentiveCompensation;
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
     * @param string $industry
     * @return void
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;
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
     * @param string $jobBenefits
     * @return void
     */
    public function setJobBenefits($jobBenefits)
    {
        $this->jobBenefits = $jobBenefits;
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
     * @param bool $overrideGlobalLocation
     * @return void
     */
    public function setOverrideGlobalLocation($overrideGlobalLocation)
    {
        $this->overrideGlobalLocation = $overrideGlobalLocation;
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
     * @param \SJBR\StaticInfoTables\Domain\Model\Country $jobLocationAddressCountry
     * @return void
     */
    public function setJobLocationAddressCountry(Country $jobLocationAddressCountry)
    {
        $this->jobLocationAddressCountry = $jobLocationAddressCountry;
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
     * @param string $jobLocationAddressLocality
     * @return void
     */
    public function setJobLocationAddressLocality($jobLocationAddressLocality)
    {
        $this->jobLocationAddressLocality = $jobLocationAddressLocality;
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
     * @param \SJBR\StaticInfoTables\Domain\Model\CountryZone $jobLocationAddressRegion
     * @return void
     */
    public function setJobLocationAddressRegion(CountryZone $jobLocationAddressRegion)
    {
        $this->jobLocationAddressRegion = $jobLocationAddressRegion;
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
     * @param string $jobLocationAddressPostalCode
     * @return void
     */
    public function setJobLocationAddressPostalCode($jobLocationAddressPostalCode)
    {
        $this->jobLocationAddressPostalCode = $jobLocationAddressPostalCode;
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
     * @param string $jobLocationAddressStreetAddress
     * @return void
     */
    public function setJobLocationAddressStreetAddress($jobLocationAddressStreetAddress)
    {
        $this->jobLocationAddressStreetAddress = $jobLocationAddressStreetAddress;
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
     * @param string $occupationalCategory
     * @return void
     */
    public function setOccupationalCategory($occupationalCategory)
    {
        $this->occupationalCategory = $occupationalCategory;
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
     * @param string $qualifications
     * @return void
     */
    public function setQualifications($qualifications)
    {
        $this->qualifications = $qualifications;
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
     * @param string $responsibilities
     * @return void
     */
    public function setResponsibilities($responsibilities)
    {
        $this->responsibilities = $responsibilities;
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
     * @param string $skills
     * @return void
     */
    public function setSkills($skills)
    {
        $this->skills = $skills;
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
     * @param string $specialCommitments
     * @return void
     */
    public function setSpecialCommitments($specialCommitments)
    {
        $this->specialCommitments = $specialCommitments;
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

    /**
     * @param string $workHours
     * @return void
     */
    public function setWorkHours($workHours)
    {
        $this->workHours = $workHours;
    }
}
