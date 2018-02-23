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
 * Class RecipesUpdateCommand
 * @package App\Command\Recipes
 * @author moay <mv@moay.de>
 */
class RecipesUpdateCommand extends RecipeRepoManagerCommand
{
    /** @var string */
    private $action = 'update';

    /** @var string */
    private $description = 'Updates local recipe repos to match the current remote repo';

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