<?php

/*
 * This file is part of the i22 symfony-flex-server package.
 *
 * (c) i22 Digitalagentur GmbH <info@i22.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Service\Provider\AliasesProvider;
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
     * @param AliasesProvider $provider
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function aliases(AliasesProvider $provider)
    {
        return $this->json($provider->provideAliases());
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