<?php
/*
 * This file is part of the i22 symfony-flex-server package.
 *
 * (c) i22 Digitalagentur GmbH <info@i22.de>
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
 * @author Manuel Voss <manuel.voss@i22.de>
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