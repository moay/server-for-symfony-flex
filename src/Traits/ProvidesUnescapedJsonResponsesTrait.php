<?php

/*
 * This file is part of the i22 symfony-flex-server package.
 *
 * (c) i22 Digitalagentur GmbH <info@i22.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ProvidesUnescapedJsonResponsesTrait
 * @package App\Traits
 * @author Manuel Voss <manuel.voss@i22.de>
 */
trait ProvidesUnescapedJsonResponsesTrait
{
    /**
     * @param $data
     * @param int $status
     * @param array $headers
     * @param array $context
     * @return static
     */
    protected function json($data, int $status = 200, array $headers = array(), array $context = array()): JsonResponse
    {
        $json = json_encode($data, JSON_UNESCAPED_SLASHES);
        return JsonResponse::fromJsonString($json, $status, $headers);
    }
}