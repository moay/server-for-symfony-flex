<?php

namespace App\EventSubscriber;

use App\Event\RepoStatusChangedEvent;
use App\Service\Generator\SystemStatusReportGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class RepoEventSubscriber
 * @package App\EventSubscriber
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class RepoEventSubscriber implements EventSubscriberInterface
{
    /** @var SystemStatusReportGenerator */
    private $reportGenerator;

    /**
     * RepoEventSubscriber constructor.
     * @param SystemStatusReportGenerator $reportGenerator
     */
    public function __construct(SystemStatusReportGenerator $reportGenerator)
    {
        $this->reportGenerator = $reportGenerator;
    }


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
        $this->reportGenerator->removeRemove();
    }

}