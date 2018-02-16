<?php

namespace App\Service\Provider;

use App\Service\OfficialEndpointProxy;

/**
 * Class VersionsProvider
 * @package App\Service\Provider
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class VersionsProvider
{
    /** @var bool */
    private $enableProxy;

    /** @var OfficialEndpointProxy */
    private $proxy;

    /**
     * VersionsProvider constructor.
     * @param bool $enableProxy
     * @param OfficialEndpointProxy $proxy
     */
    public function __construct(bool $enableProxy, OfficialEndpointProxy $proxy)
    {
        $this->enableProxy = $enableProxy;
        $this->proxy = $proxy;
    }

    /**
     * @return array
     */
    public function provideVersions()
    {
        if ($this->enableProxy) {
            return $this->proxy->getVersions();
        }
        return [];
    }
}