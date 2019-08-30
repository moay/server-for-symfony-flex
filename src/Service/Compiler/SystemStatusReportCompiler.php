<?php

/*
 * This file is part of the moay server-for-symfony-flex package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Compiler;

use App\RecipeRepo\ContribRecipeRepo;
use App\RecipeRepo\OfficialRecipeRepo;
use App\RecipeRepo\PrivateRecipeRepo;

/**
 * Class SystemStatusReportCompiler.
 *
 * @author moay <mv@moay.de>
 */
class SystemStatusReportCompiler
{
    const HEALTH_REPORT_FILE = '/var/status.json';

    /** @var string */
    private $reportFilePath;

    /** @var array */
    private $repos;

    /** @var array */
    private $config;

    /**
     * SystemStatusReportCompiler constructor.
     *
     * @param string             $projectDir
     * @param PrivateRecipeRepo  $privateRecipeRepo
     * @param OfficialRecipeRepo $officialRecipeRepo
     * @param ContribRecipeRepo  $contribRecipeRepo
     * @param bool               $enableProxy
     * @param bool               $cacheEndpoint
     * @param bool               $mirrorOfficialRepo
     * @param bool               $mirrorContribRepo
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
        $this->reportFilePath = $projectDir.self::HEALTH_REPORT_FILE;
        $this->repos = [
            'private' => $privateRecipeRepo,
            'official' => $officialRecipeRepo,
            'contrib' => $contribRecipeRepo,
        ];
        $this->config = [
            'enableProxy' => $enableProxy,
            'enableCache' => $cacheEndpoint,
            'mirrorOfficial' => $mirrorOfficialRepo,
            'mirrorContrib' => $mirrorContribRepo,
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
            $this->compileReport();
        }

        return json_decode(file_get_contents($this->reportFilePath), true);
    }

    /**
     * Compiles system status report file.
     */
    public function compileReport()
    {
        $report = [
            'config' => $this->config,
        ];

        foreach ($this->repos as $key => $repo) {
            $report['repos'][$key] = $repo->getStatus();
        }

        file_put_contents($this->reportFilePath, json_encode($report));
    }

    /**
     * Deletes the report file.
     */
    public function removeReport()
    {
        if (file_exists($this->reportFilePath)) {
            unlink($this->reportFilePath);
        }
    }
}
