<?php

/*
 * This file is part of the i22 symfony-flex-server package.
 *
 * (c) i22 Digitalagentur GmbH <info@i22.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Provider;


use App\Service\Compiler\LocalRecipeCompiler;
use App\Service\RecipeRepoManager;

class AliasesProvider
{
    /**
     * @var LocalRecipeCompiler
     */
    private $recipeCompiler;

    /**
     * AliasesProvider constructor.
     * @param LocalRecipeCompiler $recipeCompiler
     */
    public function __construct(LocalRecipeCompiler $recipeCompiler)
    {
        $this->recipeCompiler = $recipeCompiler;
    }

    public function provideAliases()
    {
        $recipes = $this->recipeCompiler->getLocalRecipes();
    }
}