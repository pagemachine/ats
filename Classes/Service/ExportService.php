<?php
namespace PAGEmachine\Ats\Service;

use PAGEmachine\Ats\Domain\Repository\ApplicationRepository;
use PAGEmachine\Ats\Domain\Repository\JobRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class ExportService implements SingletonInterface
{
    /**
     * @var ApplicationRepository $applicationRepository
     */
    protected $applicationRepository;

    /**
     * @var JobRepository $jobRepository
     */
    protected $jobRepository;

    /**
     * @param ApplicationRepository $applicationRepository
     */
    public function injectApplicationRepository(ApplicationRepository $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
    }

    /**
     * @param JobRepository $jobRepository
     */
    public function injectJobRepository(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    public function __construct()
    {
        $GLOBALS['LANG']->includeLLFile('EXT:ats/Resources/Private/Language/locallang.xlf');
        $GLOBALS['LANG']->includeLLFile('EXT:ats/Resources/Private/Language/locallang_db.xlf');
    }

    /**
     * Gets the job options.
     *
     * @return array
     */
    public function getJobOptions()
    {
        $options[''] = '';
        $jobs = $this->jobRepository->findAll();
        foreach ($jobs as $job) {
            $options[$job->getUid()] = $job->getJobNumber().' - '.$job->getTitle();
        }
        return $options;
    }

    /**
     * Gets the custom export options.
     *
     * @return array
     */
    public function getExportOptions()
    {
        $options = [
            'uid',
            'crdate',
            'application_type',
            'status',
            'aip',
            'rating',
            'comment_rating',
            'rating_perso',
            'comment_rating_perso',
            'job_number',
            'job',
            //'vocational_training',
            'privacy_policy',
            'vocational_training_completed',
            'title',
            'salutation',
            'firstname',
            'surname',
            'birthday',
            'disability',
            'nationality',
            'street',
            'zipcode',
            'city',
            'country',
            'email',
            'phone',
            'mobile',
            'employed',
            'school_qualification',
            'professional_qualification',
            'professional_qualification_final_grade',
            'academic_degree',
            'academic_degree_final_grade',
            'doctoral_degree',
            'doctoral_degree_final_grade',
            'previous_knowledge',
            'it_knowledge',
            'language',
            //'target_graduation',
            //'graduation_completed',
            //'maths_grade',
            //'physics_grade',
            //'chemistry_grade',
            //'german_grade',
            //'english_grade',
            //'art_grade',
            'comment',
            'referrer',
            //'communication_channel',
            'forward_to_departments',
            //'comment_employer',
            //'working_hours',
            'career',
            //'limitation',
            'location',
        ];
        return $options;
    }

    /**
     * Gets the short export options.
     *
     * @return array
     */
    public function getSimpleOptions()
    {
        $options = [
            'uid',
            'crdate',
            'status',
            'rating',
            'job_number',
            'job',
            'vocational_training',
            'title',
            'salutation',
            'firstname',
            'surname',
            'birthday',
            'disability',
            'street',
            'zipcode',
            'city',
            'country',
            'email',
            'phone',
            'mobile',
            'employed',
            'school_qualification',
            'professional_qualification',
            'academic_degree',
            'doctoral_degree',
            'communication_channel',
        ];
        return $options;
    }

    /**
     * Gets the Default export options.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return $this->getExportOptions();
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
            if ($GLOBALS['LANG']->getLL('tx_ats_domain_model_application.'.$option)) {
                $exportHeader .= '"'.utf8_decode($GLOBALS['LANG']->getLL('tx_ats_domain_model_application.'.$option)).'";';
            } elseif ($GLOBALS['LANG']->getLL('tx_ats_domain_model_job.'.$option)) {
                $exportHeader .= '"'.utf8_decode($GLOBALS['LANG']->getLL('tx_ats_domain_model_job.'.$option)).'";';
            } else {
                $exportHeader .= '"'.$option.'";';
            }
        }
        $exportHeader .= "\r\n";
        return $exportHeader;
    }

    /**
     * Gets CSV Data
     *
     * @param  array $options
     * @param  array $filter
     * @return string
     */
    protected function getExportBody($options, $filter)
    {

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_application');
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder->select('t1.uid')
            ->from('tx_ats_domain_model_application', 't1')
            ->leftJoin('t1', 'tx_ats_domain_model_job', 't10', 't1.job = t10.uid');
        $where = $this->getExportFilterWhere($queryBuilder, $filter);
        if (!empty($where)) {
            $queryBuilder->where(...$where);
        }
        $queryBuilder->orderBy('t1.crdate', 'ASC');

        $res = $queryBuilder->execute();

        if ($res) {
            foreach ($res as $resRow) {
                $uid = $resRow['uid'];
                $row = [];
                $application = $this->applicationRepository->findByUid($uid);
                if ($application !== null) {
                    foreach ($options as $option) {
                        switch ($option) {
                            case 'uid':
                                $row[] = $application->getUid();
                                break;
                            case 'crdate':
                                $row[] = $application->getCreationDate() ? $application->getCreationDate()->format('Y-m-d'): '';
                                break;
                            case 'application_type':
                                $row[] = $GLOBALS['LANG']->getLL('tx_ats.application.application_type.'.$application->getApplicationType());
                                break;
                            case 'status':
                                $row[] = $GLOBALS['LANG']->getLL('tx_ats.application.status.'.$application->getStatus()->__toString());
                                break;
                            case 'aip':
                                $row[] = $application->getAip() == 1 ? 'yes' : '';
                                break;
                            case 'rating':
                                $row[] = $GLOBALS['LANG']->getLL('tx_ats.application.rating.'.$application->getRating()->__toString());
                                break;
                            case 'comment_rating':
                                $comments = [];
                                foreach ($application->getnotes() as $key => $note) {
                                    if (!$note->getIsInternal() && $note->getSubject() == 'rating') {
                                        $string = $note->getCreationDate()->format('Y-m-d').' - ';
                                        if ($note->getUser()) {
                                            $string .= $note->getUser()->getRealName() ? $note->getUser()->getRealName().' ('.$note->getUser()->getUserName().')':$note->getUser()->getUserName();
                                        }
                                        $string .= ': '.$note->getDetails();
                                        $comments[] = $string;
                                    }
                                }
                                $row[] = str_replace("\r\n", ' ', implode(' ', $comments));
                                break;
                            case 'rating_perso':
                                $row[] = $GLOBALS['LANG']->getLL('tx_ats.application.rating.'.$application->getRatingPerso()->__toString());
                                break;
                            case 'comment_rating_perso':
                                $comments = [];
                                foreach ($application->getnotes() as $key => $note) {
                                    if (!$note->getIsInternal() && $note->getSubject() == 'ratingPerso') {
                                        $string = $note->getCreationDate()->format('Y-m-d').' - ';
                                        $string .= $note->getUser()->getRealName() ? $note->getUser()->getRealName().' ('.$note->getUser()->getUserName().')':$note->getUser()->getUserName();
                                        $string .= ': '.$note->getDetails();
                                        $comments[] = $string;
                                    }
                                }
                                $row[] = str_replace("\r\n", ' ', implode(' ', $comments));
                                break;
                            case 'job_number':
                                $row[] = $application->getJob() ? $application->getJob()->getJobNumber() : '';
                                break;
                            case 'job':
                                $row[] = $application->getJob() ? $application->getJob()->getTitle() : '';
                                break;
                            case 'vocational_training':
                                $row[] = 'vocational_training';
                                break;
                            case 'privacy_policy':
                                $row[] = $application->getPrivacyPolicy() == 1 ? 'yes' : '';
                                break;
                            case 'vocational_training_completed':
                                $row[] = $application->getVocationalTrainingCompleted() == 1 ? 'yes' : '';
                                break;
                            case 'title':
                                $row[] = $application->getTitle();
                                break;
                            case 'salutation':
                                $row[] = $GLOBALS['LANG']->getLL('tx_ats.label.salutation.'.$application->getSalutation());
                                break;
                            case 'firstname':
                                $row[] = $application->getFirstname();
                                break;
                            case 'surname':
                                $row[] = $application->getSurname();
                                break;
                            case 'birthday':
                                $row[] = $application->getBirthday() ? $application->getBirthday()->format('Y-m-d') : '';
                                break;
                            case 'disability':
                                $row[] = $GLOBALS['LANG']->getLL('tx_ats.label.disability.'.$application->getDisability());
                                break;
                            case 'nationality':
                                $row[] = $application->getNationality();
                                break;
                            case 'street':
                                $row[] = $application->getStreet();
                                break;
                            case 'zipcode':
                                $row[] = $application->getZipcode();
                                break;
                            case 'city':
                                $row[] = $application->getCity();
                                break;
                            case 'country':
                                $row[] = $application->getCountry() ? $application->getCountry()->getShortNameEn() : '';
                                break;
                            case 'email':
                                $row[] = $application->getEmail();
                                break;
                            case 'phone':
                                $row[] = $application->getPhone();
                                break;
                            case 'mobile':
                                $row[] = $application->getMobile();
                                break;
                            case 'employed':
                                $row[] = $GLOBALS['LANG']->getLL('tx_ats.label.employed.'.$application->getEmployed());
                                break;
                            case 'school_qualification':
                                $row[] = $GLOBALS['LANG']->getLL('tx_ats.label.school_qualification.'.$application->getSchoolQualification());
                                break;
                            case 'professional_qualification':
                                $row[] = $application->getProfessionalQualification();
                                break;
                            case 'professional_qualification_final_grade':
                                $row[] = $application->getProfessionalQualificationFinalGrade();
                                break;
                            case 'academic_degree':
                                $row[] = $application->getAcademicDegree();
                                break;
                            case 'academic_degree_final_grade':
                                $row[] = $application->getAcademicDegreeFinalGrade();
                                break;
                            case 'doctoral_degree':
                                $row[] = $application->getDoctoralDegree();
                                break;
                            case 'doctoral_degree_final_grade':
                                $row[] = $application->getDoctoralDegreeFinalGrade();
                                break;
                            case 'previous_knowledge':
                                $row[] = $application->getPreviousKnowledge();
                                break;
                            case 'it_knowledge':
                                $row[] = $application->getItKnowledge();
                                break;
                            case 'language':
                                $lang = [];
                                foreach ($application->getLanguageSkills() as $key => $languageSkill) {
                                    if ($languageSkill->getTextLanguage() != '') {
                                        $lang[] = sprintf('%s : %s', $languageSkill->getTextLanguage, $GLOBALS['LANG']->getLL('tx_ats.languageskill.level.'.$languageSkill->getLevel()));
                                    } elseif ($languageSkill->getLanguage() != null) {
                                        $lang[] = sprintf('%s : %s', $languageSkill->getLanguage()->getNameEn(), $GLOBALS['LANG']->getLL('tx_ats.languageskill.level.'.$languageSkill->getLevel()));
                                    }
                                }
                                $row[] = implode(', ', $lang);
                                break;
                            case 'target_graduation':
                                $row[] = 'target_graduation';
                                break;
                            case 'graduation_completed':
                                $row[] = 'graduation_completed';
                                break;
                            case 'maths_grade':
                                $row[] = 'maths_grade';
                                break;
                            case 'physics_grade':
                                $row[] = 'physics_grade';
                                break;
                            case 'chemistry_grade':
                                $row[] = 'chemistry_grade';
                                break;
                            case 'german_grade':
                                $row[] = 'german_grade';
                                break;
                            case 'english_grade':
                                $row[] = 'english_grade';
                                break;
                            case 'art_grade':
                                $row[] = 'art_grade';
                                break;
                            case 'comment':
                                $row[] = str_replace("\r\n", ' ', $application->getComment());
                                break;
                            case 'referrer':
                                $row[] = $GLOBALS['LANG']->getLL('tx_ats.label.referrer.'.$application->getReferrer());
                                break;
                            case 'communication_channel':
                                $row[] = 'communication_channel';
                                break;
                            case 'forward_to_departments':
                                $row[] = $GLOBALS['LANG']->getLL('tx_ats.label.forward_to_departments.'.$application->getForwardToDepartments());
                                break;
                            case 'comment_employer':
                                $row[] = 'comment_employer';
                                break;
                            case 'working_hours':
                                $row[] = 'working_hours';
                                break;
                            case 'career':
                                $row[] = $application->getJob() ? $application->getJob()->getCareer() : '';
                                break;
                            case 'limitation':
                                $row[] = 'limitation';
                                break;
                            case 'location':
                                $row[] = $application->getJob() ? $application->getJob()->getLocation() : '';
                                break;
                            default:
                                $row[] = '';
                                break;
                        }
                    }
                    foreach ($row as $key => $value) {
                        $exportBody .= utf8_decode('"'.( empty($value) ? '-' : str_replace('"', '""', $value)).'";');
                    }
                    $exportBody .= "\r\n";
                }
            }
        }
        return $exportBody;
    }

    /**
     * Gets the job options.
     *
     * @param  array $options
     * @param  array $filter
     * @return array
     */
    public function getExportData($options, $filter = null)
    {
        if (!($options == null || $options == '')) {
            $options = $this->checkExportOptions($options);
            if (!($options == null || $options == '')) {
                $exportData = $this->getExportHeader($options).$this->getExportBody($options, $filter);
            }
        }
        return $exportData;
    }

    /**
     * Download CSV file.
     *
     * @param   array $options
     * @param   array $filter
     * @return  void
     */
    public function getCsv($options, $filter = null)
    {
        header('Content-Type: text/x-csv');
        header('Content-Disposition: attachment; filename=export.csv');
        echo $this->getExportData($options, $filter);
        exit;
    }

    /**
     * get filter for queryBuilder
     *
     * @param  object $queryBuilder
     * @param  array $filter
     * @return array
     */
    public function getExportFilterWhere(&$queryBuilder, $filter)
    {
        $where = [];
        if ($filter['job']) {
            $where[] = $queryBuilder->expr()->eq('t1.job', $queryBuilder->createNamedParameter($filter['job']));
        }
        if ($filter['location']) {
            $where[] = $queryBuilder->expr()->eq('t10.location', $queryBuilder->createNamedParameter($filter['location']));
        }
        if ($filter['start']) {
            $where[] = $queryBuilder->expr()->gte('t1.crdate', $queryBuilder->createNamedParameter(strtotime($filter['location'])));
        }
        if ($filter['finish']) {
            $where[] = $queryBuilder->expr()->lte('t1.crdate', $queryBuilder->createNamedParameter(strtotime($filter['finish'])));
        }

        return $where;
    }
}
