<?php

/*
 * This file is part of the moay server-for-symfony-flex package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ProvidesUnescapedJsonResponsesTrait.
 *
 * @author moay <mv@moay.de>
 */
trait ProvidesUnescapedJsonResponsesTrait
{
    /**
     * @param $data
     * @param int   $status
     * @param array $headers
     * @param array $context
     *
     * @return static
     */
    protected function unescapedSlashesJson($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        $json = json_encode($data, JSON_UNESCAPED_SLASHES);

        return JsonResponse::fromJsonString($json, $status, $headers);
    }
}
