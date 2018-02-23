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

use GuzzleHttp\Psr7\Request;
use Http\Client\Exception\NetworkException;
use Http\Client\HttpClient;
use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * Class OfficialEndpointProxy
 * @package App\Proxy
 * @author moay <mv@moay.de>
 */
class OfficialEndpointProxy
{
    /** @var string */
    private $endpoint;

    /** @var bool */
    private $cacheEndpoint;

    /** @var HttpClient */
    private $client;

    /** @var FilesystemCache */
    private $cache;

    /**
     * OfficialEndpointProxy constructor.
     * @param string $officialEndpoint
     * @param bool $cacheEndpoint
     * @param HttpClient $client
     * @param Cache $cache
     */
    public function __construct(
        string $officialEndpoint,
        bool $cacheEndpoint,
        HttpClient $client,
        Cache $cache
    ) {
        $this->cacheEndpoint = $cacheEndpoint;
        $this->client = $client;
        $this->endpoint = $officialEndpoint;
        $this->cache = $cache;
    }

    /**
     * Provides a proxy for the aliases.json call, which provides official Symfony aliases.
     *
     * @return array
     */
    public function getAliases()
    {
        $request = new Request('GET', $this->endpoint . 'aliases.json');
        return $this->getDecodedResponse($request);
    }

    /**
     * Provides a proxy for the versions.json call, which provides version information for Symfony.
     *
     * @return array
     */
    public function getVersions()
    {
        $request = new Request('GET', $this->endpoint . 'versions.json');
        return $this->getDecodedResponse($request);
    }

    /**
     * Provides the official response for the packages call
     *
     * @param string $packagesRequestString
     * @return array|string
     */
    public function getPackages(string $packagesRequestString)
    {
        $request = new Request('GET', $this->endpoint . 'p/' . $packagesRequestString);
        return $this->getDecodedResponse($request);
    }

    /**
     * @param Request $request
     * @return array|string
     */
    private function getDecodedResponse(Request $request)
    {
        try {
            $response = $this->client->sendRequest($request);
            $decodedResponse = json_decode($response->getBody(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $decodedResponse = $response->getBody();
            }

            if ($this->cacheEndpoint) {
                $this->cache->set($this->getCacheId($request), $decodedResponse);
            }

            return $decodedResponse;
        } catch (NetworkException $e) {
            if ($this->cacheEndpoint) {
                if ($this->cache->has($this->getCacheId($request))) {
                    return $this->cache->get($this->getCacheId($request));
                }
            }
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    private function getCacheId(Request $request)
    {
        $id = $request->getMethod() . $request->getUri();
        return sha1(preg_replace('/[^A-Za-z0-9\.\- ]/', '', $id));
    }

}