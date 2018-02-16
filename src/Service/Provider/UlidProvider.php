<?php

namespace App\Service\Provider;

use Ulid\Ulid;

/**
 * Class UlidProvider
 * @package App\Service\Provider
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class UlidProvider
{
    /**
     * Provides a ulid. Symfony.sh behaviour of uppercasing the ulid is imitated.
     *
     * @return string
     */
    public function provideUlid()
    {
        return strtoupper(Ulid::generate());
    }
}