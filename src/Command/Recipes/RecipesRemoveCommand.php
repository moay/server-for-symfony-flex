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
 * Class RecipesRemoveCommand
 * @package App\Command\Recipes
 * @author moay <mv@moay.de>
 */
class RecipesRemoveCommand extends RecipeRepoManagerCommand
{
    /** @var string */
    protected $action = 'remove';

    /** @var string */
    protected $description = 'Deletes local recipe repos';
}
