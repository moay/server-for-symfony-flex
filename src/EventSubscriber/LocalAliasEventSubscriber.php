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
use App\Service\Cache;
use App\Service\Provider\AliasesProvider;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LocalAliasEventSubscriber
 * @package App\EventSubscriber
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class LocalAliasEventSubscriber implements EventSubscriberInterface
{
    /** @var FilesystemCache */
    private $cache;

    /**
     * LocalAliasEventSubscriber constructor.
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
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
        if ($this->cache->has(AliasesProvider::LOCAL_ALIASES_CACHE_KEY)) {
            $this->cache->delete(AliasesProvider::LOCAL_ALIASES_CACHE_KEY);
        }
    }

}