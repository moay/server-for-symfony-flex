<?php

/*
 * This file is part of the moay symfony-flex-server package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Service\Decoder\JsonResponseDecoder;
use Nyholm\Psr7\Request;

/**
 * Class OfficialEndpointProxy
 * @package App\Proxy
 * @author moay <mv@moay.de>
 */
class OfficialEndpointProxy
{
    /** @var string */
    private $endpoint;

    /** @var JsonResponseDecoder */
    private $decoder;

    /**
     * OfficialEndpointProxy constructor.
     * @param string $officialEndpoint
     * @param JsonResponseDecoder $decoder
     */
    public function __construct(
        string $officialEndpoint,
        JsonResponseDecoder $decoder
    ) {
        $this->endpoint = $officialEndpoint;
        $this->decoder = $decoder;
    }

    /**
     * Provides a proxy for the aliases.json call, which provides official Symfony aliases.
     *
     * @return array
     * @throws \Exception
     * @throws \Http\Client\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getAliases()
    {
        $request = new Request('GET', $this->endpoint . 'aliases.json');
        return $this->decoder->getDecodedResponse($request);
    }

    /**
     * Provides a proxy for the versions.json call, which provides version information for Symfony.
     *
     * @return array
     * @throws \Exception
     * @throws \Http\Client\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getVersions()
    {
        $request = new Request('GET', $this->endpoint . 'versions.json');
        return $this->decoder->getDecodedResponse($request);
    }

    /**
     * Provides the official response for the packages call
     *
     * @param string $packagesRequestString
     * @return array|string
     * @throws \Exception
     * @throws \Http\Client\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getPackages(string $packagesRequestString)
    {
        $request = new Request('GET', $this->endpoint . 'p/' . $packagesRequestString);
        return $this->decoder->getDecodedResponse($request);
    }
}
