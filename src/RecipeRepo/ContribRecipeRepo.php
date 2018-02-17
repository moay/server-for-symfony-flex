<?php

namespace App\RecipeRepo;

use App\Service\Cache;
use Psr\Log\LoggerInterface;

/**
 * Class ContribRecipeRepo
 * @package App\Service\RecipeRepo
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class ContribRecipeRepo extends RecipeRepo
{
    /** @var string */
    protected $repoDirName = 'contrib';

    /**
     * ContribRecipeRepo constructor.
     * @param string $contribRepoUrl
     * @param string $projectDir
     * @param Cache $cache
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $contribRepoUrl,
        string $projectDir,
        Cache $cache,
        LoggerInterface $logger
    ) {
        parent::__construct($contribRepoUrl, $projectDir, $cache, $logger);
    }

}