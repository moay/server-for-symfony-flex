<?php

namespace App\Controller;

use App\Service\Provider\UlidProvider;
use App\Service\Provider\VersionsProvider;
use App\Traits\ProvidesUnescapedJsonResponsesTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EndpointController
 * @package App\Controller
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class EndpointController extends Controller
{
    use ProvidesUnescapedJsonResponsesTrait;

    /**
     * @Route("/aliases.json", name="endpoint_aliases")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function aliases()
    {
        return $this->json([]);
    }

    /**
     * @Route("/versions.json", name="endpoint_versions")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function versions(VersionsProvider $provider)
    {
        return $this->json($provider->provideVersions());
    }

    /**
     * @Route("/ulid", name="endpoint_ulid")
     *
     * @param UlidProvider $provider
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ulid(UlidProvider $provider)
    {
        return $this->json(['ulid' => $provider->provideUlid()]);
    }

    /**
     * @Route("/p/{packages}", name="endpoint_packages")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function packages()
    {
        return $this->json([]);
    }
}