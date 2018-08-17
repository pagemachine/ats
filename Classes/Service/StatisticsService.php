<?php
namespace PAGEmachine\Ats\Service;

use PAGEmachine\Ats\Application\ApplicationStatus;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class StatisticsService implements SingletonInterface
{
    /**
     * Gets number of applications for a single job offer
     *
     * @param  array $dates
     * @return int
     */

    public function getTotalApplications($dates)
    {
        $totalApplications = $this->getDatabaseConnection()
            ->exec_SELECTgetRows(
                "job.title as title, COUNT( application.uid ) AS counter,
                TRUNCATE((
                    COUNT( application.uid ) *100 / (
                        SELECT COUNT( * )
                        FROM tx_ats_domain_model_application
                        WHERE 1 = 1
                            ".$this->getWhereApplicationInterval($dates)."
                            ".BackendUtility::deleteClause("tx_ats_domain_model_application", "application")."
                    )
                ),1) AS perc",
                "tx_ats_domain_model_job job, tx_ats_domain_model_application application",
                "job.uid = application.job".$this->getWhereApplicationInterval($dates, 'application')
                    .BackendUtility::deleteClause("tx_ats_domain_model_application", "application"),
                "job"
            );
        return $totalApplications;
    }

    /**
     * Gets number of applications for a single provenance
     *
     * @param  array $dates
     * @return int
     */

    public function getTotalApplicationsProvenance($dates)
    {
        $totalApplicationsProvenance = $this->getDatabaseConnection()
            ->exec_SELECTcountRows(
                "*",
                "tx_ats_domain_model_application",
                "`referrer`" . BackendUtility::deleteClause("tx_ats_domain_model_application").$this->getWhereApplicationInterval($dates)
            );
        return $totalApplicationsProvenance;
    }

    /**
     * Performs a query to receive provenance, frequency of this provenance
     * and percentage of total (frequency/total)
     *
     * @param  array $dates
     * @return array
     */

    public function getProvenances($dates)
    {
        $provenancesArray = $this->getDatabaseConnection()->exec_SELECTgetRows(
            "a.ref, num1 as total, TRUNCATE(((num1/num2) * 100),1) as perc",
            "(SELECT `referrer` as ref, COUNT( * ) as num1
                  FROM `tx_ats_domain_model_application`
                  WHERE `referrer` != 0 ".$this->getWhereApplicationInterval($dates).BackendUtility::deleteClause("tx_ats_domain_model_application")." group by `referrer`) a,
                  (SELECT COUNT( * ) as num2
                  FROM `tx_ats_domain_model_application`
                  WHERE `referrer` != 0 ".$this->getWhereApplicationInterval($dates).BackendUtility::deleteClause("tx_ats_domain_model_application").") b",
            "1=1"
        );
        return $provenancesArray;
    }

    /**
     * Calculates age of the applicants based on their date of birth
     * and checks in which range their age is
     * Also calculates the ratio to the total applicants
     *
     * @param  array $dates
     * @return array
     */

    public function getAgeDistributionUnder($dates)
    {
        $ageUpperLimit = array(20, 29, 39, 49, 59, 100);
        $ageLowerLimit = array(1, 20, 30, 40, 50, 60);
        $ageList = array();
        $size = count($ageUpperLimit);
        for ($i = 0; $i < $size; $i++) {
            $ageDistribution = $this->getDatabaseConnection()
            ->exec_SELECTgetSingleRow(
                "single, TRUNCATE(single/total * 100, 1) as ratio",
                "(
                    SELECT COUNT(
                        DATE_FORMAT( NOW( ) ,  '%Y' )
                        - DATE_FORMAT( birthday,  '%Y' )
                        - (
                            DATE_FORMAT( NOW( ) ,  '00-%m-%d' )
                            < DATE_FORMAT( birthday,  '00-%m-%d' )
                        )
                    ) AS single
                    FROM  `tx_ats_domain_model_application`
                    WHERE DATE_FORMAT( NOW( ) ,  '%Y' )
                        - DATE_FORMAT( birthday,  '%Y' )
                        - (
                            DATE_FORMAT( NOW( ) , '00-%m-%d' )
                            < DATE_FORMAT( birthday,  '00-%m-%d' )
                        )
                        ".$this->getWhereApplicationInterval($dates)
                        .BackendUtility::deleteClause("tx_ats_domain_model_application")."
                    BETWEEN $ageLowerLimit[$i] AND $ageUpperLimit[$i]
                ) b, (
                    SELECT COUNT(
                        DATE_FORMAT( NOW( ) ,  '%Y' )
                        - DATE_FORMAT( birthday,  '%Y' )
                        - (
                            DATE_FORMAT( NOW( ) ,  '00-%m-%d' )
                            < DATE_FORMAT( birthday,  '00-%m-%d' )
                        )
                    ) AS total
                    FROM  `tx_ats_domain_model_application`
                    WHERE DATE_FORMAT( NOW( ) ,  '%Y' )
                        - DATE_FORMAT( birthday,  '%Y' )
                        - (
                            DATE_FORMAT( NOW( ) , '00-%m-%d' )
                            < DATE_FORMAT( birthday,  '00-%m-%d' )
                        )
                        ".$this->getWhereApplicationInterval($dates)
                        .BackendUtility::deleteClause("tx_ats_domain_model_application")."
                    BETWEEN 0 AND 100
                ) c",
                "1=1"
            );
            array_push($ageList, $ageDistribution);
        }
        return $ageList;
    }

     /**
     * Gets number of tendering procedures
     *
     * @param  array $dates
     * @return int
     */

    public function getTenderingProcedures($dates)
    {
        $tenderingProcedures = $this->getDatabaseConnection()
            ->exec_SELECTcountRows(
                "*",
                "tx_ats_domain_model_job",
                "1=1" . BackendUtility::deleteClause("tx_ats_domain_model_job") . $this->getWhereJobInterval($dates)
            );
        return $tenderingProcedures;
    }

    /**
     * Gets number of applicatons for men and women and the ratio
     *
     * @param  array $dates
     * @return array
     */

    public function getApplications($dates)
    {
        $applications = $this->getDatabaseConnection()
            ->exec_SELECTgetSingleRow(
                "men, women, TRUNCATE (men/sum(men + women) * 100, 1) as menPerc, TRUNCATE (women/sum(men + women) * 100, 1) as womenPerc",
                "(
                    SELECT COUNT(*) as men
                    FROM `tx_ats_domain_model_application`
                    WHERE salutation = 1
                        ".$this->getWhereApplicationInterval($dates)
                        .BackendUtility::deleteClause("tx_ats_domain_model_application")."
                ) b, (
                    SELECT COUNT(*) as women
                    FROM `tx_ats_domain_model_application`
                    WHERE salutation = 2
                        ".$this->getWhereApplicationInterval($dates)
                        .BackendUtility::deleteClause("tx_ats_domain_model_application")."
                ) c",
                "1 = 1"
            );
        return $applications;
    }

     /**
     * Gets number of interviews for men and women and the ratio
     *
     * @param  array $dates
     * @return array
     */

    public function getInterviews($dates)
    {
        $interviews = array();
        $interviews = $this->getDatabaseConnection()
            ->exec_SELECTgetSingleRow(
                "men, women, TRUNCATE (men/sum(men + women) * 100, 1) as menPerc, TRUNCATE (women/sum(men + women) * 100, 1) as womenPerc",
                "(
                    SELECT COUNT(*) as men
                    FROM `tx_ats_domain_model_application`
                    WHERE salutation = 1 AND `invited`!= 0
                        ".$this->getWhereApplicationInterval($dates)
                        .BackendUtility::deleteClause("tx_ats_domain_model_application")."
                ) b, (
                    SELECT COUNT(*) as women
                    FROM `tx_ats_domain_model_application`
                    WHERE salutation = 2 AND `invited`!= 0
                        ".$this->getWhereApplicationInterval($dates)
                        .BackendUtility::deleteClause("tx_ats_domain_model_application")."
                ) c",
                "1 = 1"
            );
        return $interviews;
    }

     /**
     * Gets number of occupied positions for men and women and the ratio
     *
     * @param  array $dates
     * @return array
     */

    public function getOccupiedPositions($dates)
    {
        $occupiedPositions = array();
        $occupiedPositions = $this->getDatabaseConnection()
            ->exec_SELECTgetSingleRow(
                "men, women, TRUNCATE (men/sum(men + women) * 100, 1) as menPerc, TRUNCATE (women/sum(men + women) * 100, 1) as womenPerc",
                "(
                    SELECT COUNT(*) as men
                    FROM `tx_ats_domain_model_application`
                    WHERE salutation = 1 AND `status` = ".ApplicationStatus::EMPLOYED
                        .$this->getWhereApplicationInterval($dates)
                        .BackendUtility::deleteClause("tx_ats_domain_model_application")."
                ) b, (
                    SELECT COUNT(*) as women
                    FROM `tx_ats_domain_model_application`
                    WHERE salutation = 2 AND `status` = ".ApplicationStatus::EMPLOYED
                        .$this->getWhereApplicationInterval($dates)
                        .BackendUtility::deleteClause("tx_ats_domain_model_application")."
                ) c",
                "1 = 1"
            );
        return $occupiedPositions;
    }

     /**
     *
     * @return DatabaseConnection
     * @codeCoverageIgnore
     */
    public function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Generates a Where-Clause for displaying applications in the defined time interval
     *
     * @param  array $dates
     * @param  string $table
     * @return string
     */
    public function getWhereApplicationInterval($dates, $table = 'tx_ats_domain_model_application')
    {
        if ($dates == null) {
            return '';
        }

        $whereClause = " AND ".$table.".crdate >= UNIX_TIMESTAMP('".$dates['start']."') AND ".$table.".crdate <= UNIX_TIMESTAMP('".$dates['finish']."') ";
        return $whereClause;
    }

    /**
     * Generates a Where-Clause for displaying JOBs in the defined time interval
     *
     * @param  array $dates
     * @param  string $table
     * @return string
     */
    public function getWhereJobInterval($dates, $table = 'tx_ats_domain_model_job')
    {
        if ($dates == null) {
            return '';
        }

        $whereClause = " AND ".$table.".crdate >= UNIX_TIMESTAMP('".$dates['start']."') AND ".$table.".crdate <= UNIX_TIMESTAMP('".$dates['finish']."') ";


        $whereClause = " AND
            (
                ".$table.".starttime BETWEEN UNIX_TIMESTAMP('".$dates["start"]."') AND UNIX_TIMESTAMP('".$dates["finish"]."')
                OR
                ".$table.".starttime=0 AND ".$table.".crdate BETWEEN  UNIX_TIMESTAMP('".$dates["start"]."') AND UNIX_TIMESTAMP('".$dates["finish"]."')
            ) AND (
                ".$table.".endtime BETWEEN UNIX_TIMESTAMP('".$dates["start"]."') AND UNIX_TIMESTAMP('".$dates["finish"] ."') OR ".$table.".endtime=0
            )";

        return $whereClause;
    }
}
