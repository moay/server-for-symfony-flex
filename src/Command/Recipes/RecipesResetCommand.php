<?php

/*
 * This file is part of the moay server-for-symfony-flex package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command\Recipes;

/**
 * Class RecipesResetCommand.
 *
 * @author moay <mv@moay.de>
 */
class RecipesResetCommand extends RecipeRepoManagerCommand
{
    /** @var string */
    protected $action = 'reset';

    /** @var string */
    protected $description = 'Resets local recipe repos by deleting and reinitalizing from remote repo';
}
