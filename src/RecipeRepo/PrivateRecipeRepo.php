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
 * Class PrivateRecipeRepo.
 *
 * @author moay <mv@moay.de>
 */
class PrivateRecipeRepo extends RecipeRepo
{
    /** @var string */
    protected $repoDirName = 'private';

    /**
     * PrivateRecipeRepo constructor.
     *
     * @param string          $privateRepoUrl
     * @param string          $projectDir
     * @param Cache           $cache
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $privateRepoUrl,
        string $projectDir,
        Cache $cache,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($privateRepoUrl, $projectDir, $cache, $logger, $eventDispatcher);
    }
}
