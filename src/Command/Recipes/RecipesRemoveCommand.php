<?php

namespace App\Command\Recipes;

/**
 * Class RecipesRemoveCommand
 * @package App\Command\Recipes
 * @author Manuel Voss <manuel.voss@i22.de>
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