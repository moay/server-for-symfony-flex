<?php

/*
 * This file is part of the moay symfony-flex-server package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Service\Compiler\LocalRecipeCompiler;
use App\Service\Compiler\SystemStatusReportCompiler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FrontendController
 * @package App\Controller
 * @author moay <mv@moay.de>
 */
class FrontendController extends AbstractController
{
    /**
     * @Route("/", name="frontend_dashboard")
     *
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function dashboard(): Response
    {
        return $this->render('dashboard.html.twig');
    }

    /**
     * @Route("/ui/data", name="frontend_dashboard_data")
     *
     * @param SystemStatusReportCompiler $reportGenerator
     * @param LocalRecipeCompiler $recipeCompiler
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function dashboardData(SystemStatusReportCompiler $reportGenerator, LocalRecipeCompiler $recipeCompiler): JsonResponse
    {
        return $this->json([
            'status' => $reportGenerator->getReport(),
            'recipes' => $recipeCompiler->getLocalRecipes()
        ]);
    }
}