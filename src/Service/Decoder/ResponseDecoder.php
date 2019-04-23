<?php
declare(strict_types=1);

namespace App\Service\Decoder;

use App\Service\Cache;
use Http\Client\Exception\NetworkException;
use Http\Client\HttpClient;
use Nyholm\Psr7\Request;
use Symfony\Component\Cache\Simple\FilesystemCache;

class ResponseDecoder
{
    /**
     * @var bool
     */
    private $cacheEndpoint;

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var FilesystemCache
     */
    private $cache;

    public function __construct(bool $cacheEndpoint, HttpClient $client, Cache $cache)
    {
        $this->cacheEndpoint = $cacheEndpoint;
        $this->client = $client;
        $this->cache = $cache();
    }

    /**
     * @param Request $request
     * @return array|mixed|\Psr\Http\Message\StreamInterface
     * @throws \Http\Client\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getDecodedResponse(Request $request)
    {
        try {
            $response = $this->client->sendRequest($request);
            $decodedResponse = json_decode($response->getBody(), true);

            if(!in_array($response->getStatusCode(), range(200, 299))) {
                if ($this->cacheEndpoint && $this->cache->has($this->getCacheId($request))) {
                    return $this->cache->get($this->getCacheId($request));
                }
                return [];
            }

            if (json_last_error() !== JSON_ERROR_NONE) {
                $decodedResponse = $response->getBody();
            }

            if ($this->cacheEndpoint) {
                $this->cache->set($this->getCacheId($request), $decodedResponse);
            }

            return $decodedResponse;
        } catch (NetworkException $e) {
            if ($this->cacheEndpoint && $this->cache->has($this->getCacheId($request))) {
                return $this->cache->get($this->getCacheId($request));
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
        return sha1(
            preg_replace(
                '/[^A-Za-z0-9\.\- ]/',
                '',
                $request->getMethod().$request->getUri()
            )
        );
    }
}