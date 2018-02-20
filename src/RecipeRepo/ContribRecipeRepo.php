<?php

namespace App\RecipeRepo;

use App\Service\Cache;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        string $contribRepoUrl,
        string $projectDir,
        Cache $cache,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($contribRepoUrl, $projectDir, $cache, $logger, $eventDispatcher);
    }

}