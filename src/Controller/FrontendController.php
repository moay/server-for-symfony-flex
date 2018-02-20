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
        return $this->render('dashboard.html.twig');
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