<?php

namespace App\Service;

use GuzzleHttp\Psr7\Request;
use Http\Client\Curl\Client;
use Http\Client\Exception\NetworkException;
use Http\Client\HttpClient;
use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * Class OfficialEndpointProxy
 * @package App\Proxy
 * @author Manuel Voss <manuel.voss@i22.de>
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
     * @param Client $client
     */
    public function __construct(string $officialEndpoint, bool $cacheEndpoint, HttpClient $client)
    {
        $this->cacheEndpoint = $cacheEndpoint;
        $this->client = $client;
        $this->endpoint = $officialEndpoint;
        $this->cache = new FilesystemCache();
    }

    /**
     * @return array
     */
    public function getVersions()
    {
        $request = new Request('GET', $this->endpoint . 'versions.json');
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
            $decodedResponse = json_decode($response->getBody());

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
     * @return null|string|string[]
     */
    private function getCacheId(Request $request)
    {
        $id = $request->getMethod() . $request->getUri();
        return preg_replace('/[^A-Za-z0-9\.\- ]/', '', $id);
    }

}