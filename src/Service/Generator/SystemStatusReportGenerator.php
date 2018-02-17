<?php

namespace App\Service\Generator;

use App\RecipeRepo\ContribRecipeRepo;
use App\RecipeRepo\OfficialRecipeRepo;
use App\RecipeRepo\PrivateRecipeRepo;


/**
 * Class SystemStatusReportGenerator
 * @package App\Service\Generator
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class SystemStatusReportGenerator
{
    const HEALTH_REPORT_FILE = '/var/status.json';

    /** @var string */
    private $reportFilePath;

    /** @var array */
    private $repos;

    /** @var array */
    private $config;

    /**
     * SystemStatusReportGenerator constructor.
     * @param string $projectDir
     * @param PrivateRecipeRepo $privateRecipeRepo
     * @param OfficialRecipeRepo $officialRecipeRepo
     * @param ContribRecipeRepo $contribRecipeRepo
     * @param bool $enableProxy
     * @param bool $cacheEndpoint
     * @param bool $mirrorOfficialRepo
     * @param bool $mirrorContribRepo
     */
    public function __construct(
        string $projectDir,
        PrivateRecipeRepo $privateRecipeRepo,
        OfficialRecipeRepo $officialRecipeRepo,
        ContribRecipeRepo $contribRecipeRepo,
        bool $enableProxy,
        bool $cacheEndpoint,
        bool $mirrorOfficialRepo,
        bool $mirrorContribRepo
    ) {
        $this->reportFilePath = $projectDir . self::HEALTH_REPORT_FILE;
        $this->repos = [
            'privateRecipeRepo' => $privateRecipeRepo,
            'officialRecipeRepo' => $officialRecipeRepo,
            'contribRecipeRepo' => $contribRecipeRepo
        ];
        $this->config = [
            'enableProxy' => $enableProxy,
            'enableCache' => $cacheEndpoint,
            'mirrorOfficial' => $mirrorOfficialRepo,
            'mirrorContrib' => $mirrorContribRepo
        ];

    }

    /**
     * Gets the report as array, creates the report if it doesn't exist yet.
     *
     * @return array
     */
    public function getReport()
    {
        if (!file_exists($this->reportFilePath)) {
            $this->generateReport();
        }
        return json_decode(file_get_contents($this->reportFilePath));
    }

    /**
     * Generates system status report file
     */
    public function generateReport()
    {
        $report = [
            'config' => $this->config
        ];

        foreach ($this->repos as $key => $repo) {
            $report['repos'][$key] = $repo->getStatus();
        }

        file_put_contents($this->reportFilePath, json_encode($report));
    }

    /**
     * Deletes the report file
     */
    public function removeReport()
    {
        if (file_exists($this->reportFilePath)) {
            unlink($this->reportFilePath);
        }
    }
}