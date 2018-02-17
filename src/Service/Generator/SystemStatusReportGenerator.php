<?php

namespace App\Service\Generator;

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

    /**
     * SystemStatusReportGenerator constructor.
     * @param string $projectDir
     */
    public function __construct(string $projectDir)
    {
        $this->reportFilePath = $projectDir . self::HEALTH_REPORT_FILE;
    }

    /**
     * @return array
     */
    public function getReport()
    {
        if (!file_exists($this->reportFilePath)) {
            $this->generateReport();
        }
        return json_decode(file_get_contents($this->reportFilePath));
    }

    public function generateReport()
    {
        $report = [];
    }
}