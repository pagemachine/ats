<?php
namespace PAGEmachine\Ats\Controller\Backend;

use PAGEmachine\Ats\Service\ExportService;
use PAGEmachine\Ats\Service\StatisticsService;

/*
 * This file is part of the PAGEmachine ATS project.
 */


/**
 * StatisticsController
 */
class StatisticsController extends AbstractBackendController
{
    /**
     * @var StatisticsService $statisticsService
     */
    protected $statisticsService;

    /**
     * @var ExportService $exportService
     */
    protected $exportService;

    /**
     * @param StatisticsService $statisticsService
     */
    public function injectStatisticsService(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * @param ExportService $exportService
     */
    public function injectExportService(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Action URLs for the action menu
     *
     * @var array
     */
    protected $menuUrls = [
        'statistics' => ["action" => "statistics", "label" => "be.label.Statistics"],
        'export' => ["action" => "export", "label" => "be.label.Export"],
    ];

    /**
     * Testing helper class
     *
     * @return MenuRegistry
     * @codeCoverageIgnore
     */
    public function getMenuRegistry()
    {
        return $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry();
    }

    /**
     * Statistics action - displays statistics
     *
     * @param array $date
     * @return void
     */
    public function statisticsAction(array $dates = null)
    {
        if ($dates != null) {
            $this->view->assignMultiple([
                'start' => $dates['start'],
                'finish' => $dates['finish'],
            ]);
        }
        $totalApplications = $this->statisticsService->getTotalApplications($dates);
        $totalApplicationsProvenance = $this->statisticsService->getTotalApplicationsProvenance($dates);
        $ageDistribution = $this->statisticsService->getAgeDistributionUnder($dates);
        $tenderingProcedures = $this->statisticsService->getTenderingProcedures($dates);
        $interviews = $this->statisticsService->getInterviews($dates);
        $provenances = $this->statisticsService->getProvenances($dates);
        $applications = $this->statisticsService->getApplications($dates);
        $positions = $this->statisticsService->getOccupiedPositions($dates);

        $this->view->assignMultiple([
            'totalApplications' => $totalApplications,
            'totalApplicationsProvenance' => $totalApplicationsProvenance,
            'tenderingProcedures' => $tenderingProcedures,
            'ageDistribution' => $ageDistribution,
            'provenances' => $provenances,
            'applications' => $applications,
            'interviews' => $interviews,
            'positions' => $positions,
            ]);
    }

    /**
     * export action - displays Export
     *
     * @return void
     */
    public function exportAction()
    {
        $this->view->assignMultiple([
            'exportOptions' => $this->exportService->getExportOptions(),
            'jobOptions' => $this->exportService->getJobOptions(),
            ]);
    }

    /**
     * getCsv action - Download export CSV
     *
     * @param   array $selectedOptions
     * @param   array $filter
     * @param   string $exportType 'export' = normal export. 'simple' = simple export. 'custom' = Custom export
     * @return void
     */
    public function getCsvAction(array $selectedOptions = null, array $filter = null, $exportType)
    {
        switch ($exportType) {
            case 'export':
                $selectedOptions = $this->exportService->getDefaultOptions();
                break;

            case 'simple':
                $selectedOptions = $this->exportService->getSimpleOptions();
                break;
        }
        $this->exportService->getCSV($selectedOptions, $filter);
        $this->redirect("export");
    }
}
