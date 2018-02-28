<?php

/*
 * This file is part of the moay symfony-flex-server package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command\System;

use App\Service\Compiler\SystemStatusReportCompiler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class SystemStatusCommand
 * @package App\Command\System
 * @author moay <mv@moay.de>
 */
class SystemStatusCommand extends Command
{
    /** @var SystemStatusReportCompiler */
    private $reportCompiler;

    /**
     * SystemStatusCommand constructor.
     * @param SystemStatusReportCompiler $reportCompiler
     */
    public function __construct(SystemStatusReportCompiler $reportCompiler)
    {
        parent::__construct();
        $this->reportCompiler = $reportCompiler;
    }

    /**  */
    public function configure()
    {
        $this
            ->setName('system:status')
            ->setDescription('Provides an overview over the system status');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $report = $this->reportCompiler->getReport();

        $io->section('Configuration');
        $io->table([], [
            ['Proxy for official endpoint', $report['config']['enableProxy'] ? '<fg=green>enabled</>' : '<fg=red>disabled</>'],
            ['Caching for official endpoint', $report['config']['enableCache'] ? '<fg=green>enabled</>' : '<fg=red>disabled</>'],
            ['Local mirror for official recipes', $report['config']['mirrorOfficial'] ? '<fg=green>enabled</>' : '<fg=red>disabled</>'],
            ['Local mirror for contrib recipes', $report['config']['mirrorContrib'] ? '<fg=green>enabled</>' : '<fg=red>disabled</>'],
        ]);


        $io->section('Repo status');
        $io->table(['Repo', 'Url', 'Remote readable', 'Downloaded', 'Last Update'], array_map(function ($repo, $key) {
            return [
                $key,
                $repo['url'],
                $repo['remote_readable'] ? '<fg=green>Yes</>' : '<fg=red>No</>',
                $repo['downloaded'] ? '<fg=green>Yes</>' : '<fg=red>No</>',
                $repo['last_updated'] != null ? $repo['last_updated'] : ''
            ];
        }, $report['repos'], array_keys($report['repos'])));
    }
}