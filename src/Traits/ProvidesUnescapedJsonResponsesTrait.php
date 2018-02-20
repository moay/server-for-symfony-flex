<?php

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