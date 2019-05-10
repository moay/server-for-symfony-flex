<?php

/*
 * This file is part of the moay server-for-symfony-flex package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\RecipeRepo;

use App\Service\Cache;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ContribRecipeRepo
 * @package App\Service\RecipeRepo
 * @author moay <mv@moay.de>
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
