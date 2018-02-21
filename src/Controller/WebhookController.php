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

use App\Service\RecipeRepoManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WebhookController
 * @package App\Controller
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class WebhookController extends Controller
{
    /**
     * @Route("/webhook/update", name="webhook_update", methods={"POST"})
     *
     * @param RecipeRepoManager $repoManager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Cz\Git\GitException
     */
    public function update(RecipeRepoManager $repoManager)
    {
        foreach ($repoManager->getConfiguredRepos() as $repo) {
            $repo->update();
        }
        return $this->json(['success' => true]);
    }

}