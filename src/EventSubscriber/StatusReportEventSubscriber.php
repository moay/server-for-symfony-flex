<?php

/*
 * This file is part of the i22 symfony-flex-server package.
 *
 * (c) i22 Digitalagentur GmbH <info@i22.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventSubscriber;

use App\Event\RepoStatusChangedEvent;
use App\Service\Generator\SystemStatusReportGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class StatusReportEventSubscriber
 * @package App\EventSubscriber
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class StatusReportEventSubscriber implements EventSubscriberInterface
{
    /** @var SystemStatusReportGenerator */
    private $reportGenerator;

    /**
     * StatusReportEventSubscriber constructor.
     * @param SystemStatusReportGenerator $reportGenerator
     */
    public function __construct(SystemStatusReportGenerator $reportGenerator)
    {
        $this->reportGenerator = $reportGenerator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            RepoStatusChangedEvent::NAME => 'onRepoStatusChange'
        ];
    }

    /**
     * @param RepoStatusChangedEvent $event
     */
    public function onRepoStatusChange(RepoStatusChangedEvent $event)
    {
        $this->reportGenerator->removeReport();
    }

}