<?php
declare(strict_types=1);

namespace App\Tests\Service\Decoder;

use App\Service\Cache;
use App\Service\Decoder\ResponseDecoder;
use Http\Client\Exception\NetworkException;
use Http\Client\HttpClient;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;

class ResponseDecoderTest extends TestCase
{
    /**
     * @var HttpClient|\Prophecy\Prophecy\ObjectProphecy
     */
    private $client;

    /**
     * @var Cache|\Prophecy\Prophecy\ObjectProphecy
     */
    private $cache;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|FilesystemCache
     */
    private $simpleCache;

    protected function setUp()
    {
        $this->client = $this->prophesize(HttpClient::class);
        $this->cache = $this->prophesize(Cache::class);
        $this->simpleCache = $this->prophesize(FilesystemCache::class);
        $this->cache->__invoke()->willReturn($this->simpleCache->reveal());
    }

    public function testGetDecodedResponse()
    {
        $decoder = new ResponseDecoder(false, $this->client->reveal(), $this->cache->reveal());

        $request = new Request('GET', 'endpoint/data.json');
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn(json_encode(['json' => 'data']));

        $this->client->sendRequest($request)->willReturn($response->reveal());

        $this->assertSame(
            ['json' => 'data'],
            $decoder->getDecodedResponse($request)
        );
    }

    public function testGetDecodedResponseEmptyOnRequestError()
    {
        $decoder = new ResponseDecoder(false, $this->client->reveal(), $this->cache->reveal());

        $request = new Request('GET', 'endpoint/data.json');
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(500);
        $response->getBody()->willReturn('');

        $this->client->sendRequest($request)->willReturn($response->reveal());

        $this->assertSame(
            [],
            $decoder->getDecodedResponse($request)
        );
    }

    public function testGetDecodedResponseFromCacheOnRequestError()
    {
        $decoder = new ResponseDecoder(true, $this->client->reveal(), $this->cache->reveal());

        $request = new Request('GET', 'endpoint/data.json');
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(500);
        $response->getBody()->willReturn('');

        $this->simpleCache->has('4429b090fd82239e188859ae626162e5e790b4db')->willReturn(true);
        $this->simpleCache->get('4429b090fd82239e188859ae626162e5e790b4db')->willReturn(['json' => 'cache']);

        $this->client->sendRequest($request)->willReturn($response->reveal());

        $this->assertSame(
            ['json' => 'cache'],
            $decoder->getDecodedResponse($request)
        );
    }

    public function testGetDecodedResponseCachesDataIfEnabled()
    {
        $decoder = new ResponseDecoder(true, $this->client->reveal(), $this->cache->reveal());

        $request = new Request('GET', 'endpoint/data.json');
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn(json_encode(['json' => 'cache']));

        $this->simpleCache->has('4429b090fd82239e188859ae626162e5e790b4db')->willReturn(false);
        $this->simpleCache->set('4429b090fd82239e188859ae626162e5e790b4db', ['json' => 'cache'])->shouldBeCalledOnce();

        $this->client->sendRequest($request)->willReturn($response->reveal());

        $this->assertSame(
            ['json' => 'cache'],
            $decoder->getDecodedResponse($request)
        );
    }

    public function testGetDecodedResponseFromCacheWhenNetworkFails()
    {
        $decoder = new ResponseDecoder(true, $this->client->reveal(), $this->cache->reveal());

        $request = new Request('GET', 'endpoint/data.json');

        $this->simpleCache->has('4429b090fd82239e188859ae626162e5e790b4db')->willReturn(true);
        $this->simpleCache->get('4429b090fd82239e188859ae626162e5e790b4db')->willReturn(['json' => 'cache']);

        $this->client->sendRequest($request)->willThrow(NetworkException::class);

        $this->assertSame(
            ['json' => 'cache'],
            $decoder->getDecodedResponse($request)
        );
    }

    /**
     * @throws \Http\Client\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @expectedException \Http\Client\Exception\NetworkException
     */
    public function testGetDecodedResponseThrowsNetworkExceptionWhenClientFailsAndNoCachedVersion()
    {
        $decoder = new ResponseDecoder(true, $this->client->reveal(), $this->cache->reveal());
        $request = new Request('GET', 'endpoint/data.json');

        $this->simpleCache->has('4429b090fd82239e188859ae626162e5e790b4db')->willReturn(false);
        $this->client->sendRequest($request)->willThrow(NetworkException::class);

        $decoder->getDecodedResponse($request);
    }

    /**
     * @todo this seems like a bug, we should not store in the cache invalid json? should we throw an error?
     * @throws \Http\Client\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testGetDecodedResponseReturnsBodyWhenJsonDecodingFails()
    {
        $decoder = new ResponseDecoder(true, $this->client->reveal(), $this->cache->reveal());

        $request = new Request('GET', 'endpoint/data.json');
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn('{invalid_json');

        $this->client->sendRequest($request)->willReturn($response->reveal());
        $this->simpleCache->set('4429b090fd82239e188859ae626162e5e790b4db', '{invalid_json')->shouldBeCalledOnce();

        $this->assertSame(
            '{invalid_json',
            $decoder->getDecodedResponse($request)
        );

        $this->markAsRisky();
    }
}
