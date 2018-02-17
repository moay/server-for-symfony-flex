<?php

namespace App\RecipeRepo;

use App\Service\Cache;
use Psr\Log\LoggerInterface;

/**
 * Class OfficialRecipeRepo
 * @package App\Service\RecipeRepo
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class OfficialRecipeRepo extends RecipeRepo
{
    /** @var string */
    protected $repoDirName = 'official';

    /**
     * OfficialRecipeRepo constructor.
     * @param string $officialRepoUrl
     * @param string $projectDir
     * @param Cache $cache
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $officialRepoUrl,
        string $projectDir,
        Cache $cache,
        LoggerInterface $logger
    ) {
        parent::__construct($officialRepoUrl, $projectDir, $cache, $logger);
    }

}