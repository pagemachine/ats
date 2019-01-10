<?php
namespace PAGEmachine\Ats\Service;

use PAGEmachine\Ats\Application\ApplicationStatus;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_job');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $queryBuilder->select('job.title as title')
            ->addSelectLiteral(
                $queryBuilder->expr()->count('application.uid', 'counter')
            )
            ->from('tx_ats_domain_model_job', 'job')
            ->join('job', 'tx_ats_domain_model_application', 'application', 'job.uid = application.job')
            ->groupBy('job');

        if (!empty($where = $this->getWhereApplicationInterval($queryBuilder, $dates, 'application'))) {
            $queryBuilder->where(...$where);
        }

        $res = $queryBuilder->execute();

        $rows = $res->fetchAll();
        $total = $this->getTotalNumber($rows, "counter");

        if (!empty($rows) && $total > 0) {
            foreach ($rows as $key => $value) {
                $rows[$key]['perc'] = number_format($value['counter'] * 100 / $total, 1);
            }
        }

        return ['value' => $rows, 'total' => $total];
    }

    /**
     * Gets number of applications for a single provenance
     *
     * @param  array $dates
     * @return int
     */

    public function getTotalApplicationsProvenance($dates)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_application');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $count = $queryBuilder
            ->count('uid')
            ->from('tx_ats_domain_model_application')
            ->where(
                $queryBuilder->expr()->neq('referrer', $queryBuilder->createNamedParameter(0))
            );

        if (!empty($where = $this->getWhereApplicationInterval($queryBuilder, $dates))) {
            $queryBuilder->andWhere(...$where);
        }

        return $queryBuilder->execute()->fetchColumn(0);
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
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_application');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $queryBuilder->select('referrer as ref')
            ->addSelectLiteral(
                $queryBuilder->expr()->count('*', 'total')
            )
            ->from('tx_ats_domain_model_application')
            ->where($queryBuilder->expr()->neq('referrer', $queryBuilder->createNamedParameter(0)))
            ->groupBy('referrer');

        if (!empty($where = $this->getWhereApplicationInterval($queryBuilder, $dates))) {
            $queryBuilder->andWhere(...$where);
        }

        $res =  $queryBuilder->execute();

        $rows = $res->fetchAll();
        $total = $this->getTotalNumber($rows, "total");

        if (!empty($rows) && $total > 0) {
            foreach ($rows as $key => $value) {
                $rows[$key]['perc'] = number_format($value['total'] * 100 / $total, 1);
            }
        }

        return ['value' => $rows, 'total' => $total];
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
        return[];
        $ageUpperLimit = array(20, 29, 39, 49, 59, 100);
        $ageLowerLimit = array(0, 20, 30, 40, 50, 60);
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
                        ) BETWEEN $ageLowerLimit[$i] AND $ageUpperLimit[$i]
                        ".$this->getWhereApplicationInterval($dates)
                        .BackendUtility::deleteClause("tx_ats_domain_model_application")."
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
                        ) BETWEEN 0 AND 100
                        ".$this->getWhereApplicationInterval($dates)
                        .BackendUtility::deleteClause("tx_ats_domain_model_application")."
                ) c",
                "1=1"
            );
            array_push($ageList, $ageDistribution);
        }
        return ['value' => $ageList, 'total' => $this->getTotalNumber($ageList, 'single')];
    }

     /**
     * Gets number of tendering procedures
     *
     * @param  array $dates
     * @return int
     */

    public function getTenderingProcedures($dates)
    {
        return[];
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_job');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $count = $queryBuilder
            ->count('*')
            ->from('tx_ats_domain_model_job');

        if (!empty($where = $this->getWhereJobInterval($queryBuilder, $dates, 'tx_ats_domain_model_job'))) {
            $queryBuilder->where(...$where);
        }

        return $queryBuilder->execute()->fetchColumn(0);
    }

    /**
     * Gets number of applicatons for men and women and the ratio
     *
     * @param  array $dates
     * @return array
     */

    public function getApplications($dates, &$queryBuilder = null, $additionalWhere = null)
    {
        if ($queryBuilder == null) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_application');
        }

        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $queryBuilder->select('salutation')
            ->addSelectLiteral(
                $queryBuilder->expr()->count('*', 'total')
            )
            ->from('tx_ats_domain_model_application')
            ->orWhere(
                $queryBuilder->expr()->eq('salutation', $queryBuilder->createNamedParameter(1)),
                $queryBuilder->expr()->eq('salutation', $queryBuilder->createNamedParameter(2))
            )
            ->groupBy('salutation');


        if ($additionalWhere != null) {
            $queryBuilder->andWhere(...$additionalWhere);
        }

        if (!empty($where = $this->getWhereApplicationInterval($queryBuilder, $dates))) {
            $queryBuilder->andWhere(...$where);
        }

        $res =  $queryBuilder->execute();

        $rows = $res->fetchAll();
        $total = $this->getTotalNumber($rows, "total");

        $applications = [
            'men' => 0,
            'women' => 0,
            'menPerc' => 0,
            'womenPerc' => 0,
        ];

        if (!empty($rows) && $total > 0) {
            foreach ($rows as $key => $value) {
                if ($value['salutation'] == 1) {
                    $applications['men'] = $value['total'];
                    $applications['menPerc'] = number_format($value['total'] * 100 / $total, 1);
                } else {
                    $applications['women'] = $value['total'];
                    $applications['womenPerc'] = number_format($value['total'] * 100 / $total, 1);
                }
            }
        }

        return ['value' =>  $applications, 'total' => $total];
    }

     /**
     * Gets number of interviews for men and women and the ratio
     *
     * @param  array $dates
     * @return array
     */

    public function getInterviews($dates)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_application');
        return $this->getApplications($dates, $queryBuilder, [
            $queryBuilder->expr()->neq('invited', $queryBuilder->createNamedParameter(0)),
        ]);
    }

     /**
     * Gets number of occupied positions for men and women and the ratio
     *
     * @param  array $dates
     * @return array
     */

    public function getOccupiedPositions($dates)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_application');
        return $this->getApplications($dates, $queryBuilder, [
            $queryBuilder->expr()->eq('status', $queryBuilder->createNamedParameter(ApplicationStatus::EMPLOYED)),
        ]);
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
    public function getWhereApplicationInterval(&$queryBuilder, $dates, $table = 'tx_ats_domain_model_application')
    {
        $where = [];
        if ($dates == null) {
            return $where;
        }
        $where[] = $queryBuilder->expr()->gte($table.'.crdate', $queryBuilder->createNamedParameter(strtotime($dates['start'])));
        $where[] = $queryBuilder->expr()->lte($table.'.crdate', $queryBuilder->createNamedParameter(strtotime($dates['finish'])));

        return $where;
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

    /**
     * Sums the values of an array on a specific key.
     *
     * @param      array   $statisticArray
     * @param      string  $key
     */
    protected function getTotalNumber($statisticArray, $key)
    {
        return array_sum(array_column($statisticArray, $key));
    }
}
