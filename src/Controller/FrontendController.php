<?php

namespace App\Controller;

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
     * @Route("/", name="frontend_recipes_overview")
     *
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function recipes()
    {
        return $this->render('recipes.html.twig');
    }
}