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
 * Interface RecipeRepoManagerCommandInterface
 * @package App\Command\Recipes
 * @author moay <mv@moay.de>
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