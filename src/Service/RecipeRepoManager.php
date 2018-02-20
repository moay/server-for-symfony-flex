<?php

/*
 * This file is part of the i22 symfony-flex-server package.
 *
 * (c) i22 Digitalagentur GmbH <info@i22.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Exception\RecipeRepoManagerException;
use App\RecipeRepo\ContribRecipeRepo;
use App\RecipeRepo\OfficialRecipeRepo;
use App\RecipeRepo\PrivateRecipeRepo;
use App\RecipeRepo\RecipeRepo;
use Psr\Log\LoggerInterface;

/**
 * Class RecipeRepoManager
 * @package App\Service
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class RecipeRepoManager
{
    /** @var RecipeRepo[] */
    private $repos;

    /** @var LoggerInterface */
    private $logger;

    /**
     * RecipeRepoManager constructor.
     * @param bool $mirrorOfficialRepo
     * @param bool $mirrorContribRepo
     * @param PrivateRecipeRepo $privateRecipeRepo
     * @param OfficialRecipeRepo $officialRecipeRepo
     * @param ContribRecipeRepo $contribRecipeRepo
     * @param LoggerInterface $logger
     */
    public function __construct(
        bool $mirrorOfficialRepo,
        bool $mirrorContribRepo,
        PrivateRecipeRepo $privateRecipeRepo,
        OfficialRecipeRepo $officialRecipeRepo,
        ContribRecipeRepo $contribRecipeRepo,
        LoggerInterface $logger
    ) {
        $this->repos = [
            'private' => $privateRecipeRepo
        ];
        if ($mirrorOfficialRepo) {
            $this->repos['official'] = $officialRecipeRepo;
        }
        if ($mirrorContribRepo) {
            $this->repos['contrib'] = $contribRecipeRepo;
        }
        $this->logger = $logger;
    }

    /**
     * @param RecipeRepo $repo
     * @return bool
     */
    public function isConfigured(RecipeRepo $repo)
    {
        return isset($this->repos[$repo->getRepoDirName()]);
    }

    /**
     * @param string $repoDirName
     * @return bool
     */
    public function isConfiguredByDirName(string $repoDirName)
    {
        return isset($this->repos[$repoDirName]);
    }

    /**
     * @param string $action
     * @param string $repoDirName
     * @throws RecipeRepoManagerException
     */
    public function executeOnRepo(string $action, string $repoDirName)
    {
        if (!isset($this->repos[$repoDirName])) {
            throw new RecipeRepoManagerException(sprintf('Repo \'%s\' does not exist or is not configured.', $repoDirName));
        }
        $this->repos[$repoDirName]->{$action}();
    }

}