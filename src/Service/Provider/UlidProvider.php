<?php

/*
 * This file is part of the moay server-for-symfony-flex package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Provider;

use Ulid\Ulid;

/**
 * Class UlidProvider
 * @package App\Service\Provider
 * @author moay <mv@moay.de>
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
