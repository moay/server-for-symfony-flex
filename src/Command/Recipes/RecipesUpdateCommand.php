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
 * Class RecipesUpdateCommand.
 *
 * @author moay <mv@moay.de>
 */
class RecipesUpdateCommand extends RecipeRepoManagerCommand
{
    /** @var string */
    protected $action = 'update';

    /** @var string */
    protected $description = 'Updates local recipe repos to match the current remote repo';
}
