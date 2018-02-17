<?php

namespace App\RecipeRepo;

use App\Service\Cache;
use Psr\Log\LoggerInterface;

/**
 * Class PrivateRecipeRepo
 * @package App\Service\RecipeRepo
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class PrivateRecipeRepo extends RecipeRepo
{
    /** @var string */
    protected $repoDirName = 'private';

    /**
     * PrivateRecipeRepo constructor.
     * @param string $privateRepoUrl
     * @param string $projectDir
     * @param Cache $cache
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $privateRepoUrl,
        string $projectDir,
        Cache $cache,
        LoggerInterface $logger
    ) {
        parent::__construct($privateRepoUrl, $projectDir, $cache, $logger);
    }

}