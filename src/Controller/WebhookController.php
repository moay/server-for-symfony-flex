<?php

/*
 * This file is part of the moay server-for-symfony-flex package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Service\RecipeRepoManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WebhookController
 * @package App\Controller
 * @author moay <mv@moay.de>
 */
class WebhookController extends AbstractController
{
    /**
     * @Route("/webhook/update", name="webhook_update", methods={"POST"})
     *
     * @param RecipeRepoManager $repoManager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Cz\Git\GitException
     */
    public function update(RecipeRepoManager $repoManager): JsonResponse
    {
        foreach ($repoManager->getConfiguredRepos() as $repo) {
            $repo->update();
        }
        return $this->json(['success' => true]);
    }

}
