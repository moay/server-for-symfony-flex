<?php

namespace App\Command\Recipes;

/**
 * Interface RecipeRepoManagerCommandInterface
 * @package App\Command\Recipes
 * @author Manuel Voss <manuel.voss@i22.de>
 */
interface RecipeRepoManagerCommandInterface
{
    /**
     * @return string
     */
    function getAction();

    /**
     * @return string
     */
    function getDescription();
}