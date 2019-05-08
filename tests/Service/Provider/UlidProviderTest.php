<?php

namespace App\Tests\Service\Provider;

use App\Service\Provider\UlidProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UlidProviderTest extends KernelTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::bootKernel();
    }

    public function testServiceIsAvailable()
    {
        $ulidProvider = self::$container->get(UlidProvider::class);
        $this->assertInstanceOf(UlidProvider::class, $ulidProvider);

        return $ulidProvider;
    }

    /**
     * @depends testServiceIsAvailable
     */
    public function testUlidLength(UlidProvider $ulidProvider)
    {
        $this->assertSame(26, strlen((string) $ulidProvider->provideUlid()));
    }

    /**
     * @depends testServiceIsAvailable
     */
    public function testUlidIsUppercased(UlidProvider $ulidProvider)
    {
        $this->assertRegExp('/[0-9][A-Z]/', (string) $ulidProvider->provideUlid());
    }
}
