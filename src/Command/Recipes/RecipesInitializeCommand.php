<?php

/*
 * This file is part of the moay symfony-flex-server package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command\Recipes;

/**
 * Class RecipesInitializeCommand
 * @package App\Command\Recipes
 * @author moay <mv@moay.de>
 */
class RecipesInitializeCommand extends RecipeRepoManagerCommand
{
    /** @var string */
    protected $action = 'initialize';

    /** @var string */
    protected $description = 'Initializes local recipe repos from remote repo';

}