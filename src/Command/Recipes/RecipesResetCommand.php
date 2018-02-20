<?php

namespace App\Command\Recipes;

/**
 * Class RecipesResetCommand
 * @package App\Command\Recipes
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class RecipesResetCommand extends RecipeRepoManagerCommand
{
    /** @var string */
    private $action = 'reset';

    /** @var string */
    private $description = 'Resets local recipe repos by deleting and reinitalizing from remote repo';

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