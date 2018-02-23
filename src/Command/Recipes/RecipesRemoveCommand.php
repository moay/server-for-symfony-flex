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
 * Class RecipesRemoveCommand
 * @package App\Command\Recipes
 * @author moay <mv@moay.de>
 */
class RecipesRemoveCommand extends RecipeRepoManagerCommand
{
    /** @var string */
    private $action = 'remove';

    /** @var string */
    private $description = 'Deletes local recipe repos';

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}