<?php

namespace App\Controller;

use App\Service\Generator\SystemStatusReportGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FrontendController
 * @package App\Controller
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class FrontendController extends Controller
{
    /**
     * @Route("/", name="frontend_dashboard")
     *
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function dashboard()
    {
        return $this->render('recipes.html.twig');
    }

    /**
     * @Route("/ui/data", name="frontend_dashboard_data")
     *
     * @param SystemStatusReportGenerator $reportGenerator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function dashboardData(SystemStatusReportGenerator $reportGenerator)
    {
        return $this->json([
            'status' => $reportGenerator->getReport()
        ]);
    }
}