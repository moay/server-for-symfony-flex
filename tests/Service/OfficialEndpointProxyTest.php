<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\Decoder\JsonResponseDecoder;
use App\Service\OfficialEndpointProxy;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class OfficialEndpointProxyTest extends TestCase
{
    /**
     * @var OfficialEndpointProxy
     */
    private $proxy;

    /**
     * @var JsonResponseDecoder|ObjectProphecy
     */
    private $decoder;

    protected function setUp()
    {
        $this->decoder = $this->prophesize(JsonResponseDecoder::class);

        $this->proxy = new OfficialEndpointProxy(
            'official_endpoint/',
            $this->decoder->reveal()
        );
    }

    public function testGetAliases()
    {
        $this
            ->decoder
            ->getDecodedResponse(
                Argument::allOf(
                    Argument::which('getMethod', 'GET'),
                    Argument::which('getUri', 'official_endpoint/aliases.json')
                )
            )
            ->willReturn('decoded response');

        $this->assertSame('decoded response', $this->proxy->getAliases());
    }

    public function testGetVersions()
    {
        $this
            ->decoder
            ->getDecodedResponse(
                Argument::allOf(
                    Argument::which('getMethod', 'GET'),
                    Argument::which('getUri', 'official_endpoint/versions.json')
                )
            )
            ->willReturn('decoded response');

        $this->assertSame('decoded response', $this->proxy->getVersions());
    }

    public function testGetPackages()
    {
        $this
            ->decoder
            ->getDecodedResponse(
                Argument::allOf(
                    Argument::which('getMethod', 'GET'),
                    Argument::which('getUri', 'official_endpoint/p/package_name')
                )
            )
            ->willReturn('decoded response');

        $this->assertSame('decoded response', $this->proxy->getPackages('package_name'));
    }
}
