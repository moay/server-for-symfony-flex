<?php

/*
 * This file is part of the moay server-for-symfony-flex package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventSubscriber;

use App\Event\RepoStatusChangedEvent;
use App\Service\Compiler\SystemStatusReportCompiler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class StatusReportEventSubscriber.
 *
 * @author moay <mv@moay.de>
 */
class StatusReportEventSubscriber implements EventSubscriberInterface
{
    /** @var SystemStatusReportCompiler */
    private $reportCompiler;

    /**
     * StatusReportEventSubscriber constructor.
     *
     * @param SystemStatusReportCompiler $reportCompiler
     */
    public function __construct(SystemStatusReportCompiler $reportCompiler)
    {
        $this->reportCompiler = $reportCompiler;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            RepoStatusChangedEvent::NAME => 'onRepoStatusChange',
        ];
    }

    /**
     * @param RepoStatusChangedEvent $event
     */
    public function onRepoStatusChange(RepoStatusChangedEvent $event)
    {
        $this->reportCompiler->removeReport();
    }
}
