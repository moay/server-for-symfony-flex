<?php

/*
 * This file is part of the i22 symfony-flex-server package.
 *
 * (c) i22 Digitalagentur GmbH <info@i22.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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