<?php
/*
 * This file is part of the moay symfony-flex-server package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\RecipeRepo;

use Cz\Git\GitException;
use Cz\Git\GitRepository;

/**
 * Class GitRepo
 * @package App\RecipeRepo
 * @author moay <mv@moay.de>
 */
class GitRepo extends GitRepository
{
    /**
     * @return GitRepository
     * @throws GitException
     */
    public function forceClean()
    {
        return $this->begin()
            ->run('git clean -fd')
            ->end();
    }
}