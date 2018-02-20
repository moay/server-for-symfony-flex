<?php

/*
 * This file is part of the i22 symfony-flex-server package.
 *
 * (c) i22 Digitalagentur GmbH <info@i22.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * Class Cache
 * @package App\Service
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class Cache
{
    const CACHE_DIR = '/var/cache/data';

    /**
     * @var FilesystemCache
     */
    private $cache;

    /**
     * Cache constructor.
     * @param string $projectDir
     */
    public function __construct(string $projectDir)
    {
        $cachePath = $projectDir . self::CACHE_DIR;

        if(!is_dir($cachePath)) {
            mkdir($cachePath);
        }
        $this->cache = new FilesystemCache('flex-server', 0, $cachePath);
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->cache, $method], $arguments);
    }
}