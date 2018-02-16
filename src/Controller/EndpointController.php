<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EndpointController
 * @package App\Controller
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class EndpointController extends Controller
{
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
    public function versions()
    {
        return $this->json([]);
    }

    /**
     * @Route("/ulid", name="endpoint_ulid")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ulid()
    {
        return $this->json([]);
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