<?php

/*
 * This file is part of the i22 symfony-flex-server package.
 *
 * (c) i22 Digitalagentur GmbH <info@i22.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Compiler;

use App\Entity\Recipe;
use App\Service\RecipeRepoManager;

class LocalRecipeCompiler
{
    /**
     * @var RecipeRepoManager
     */
    private $repoManager;

    /**
     * @var Recipe[]
     */
    private $recipes = [];

    /**
     * LocalRecipeCompiler constructor.
     * @param RecipeRepoManager $repoManager
     */
    public function __construct(RecipeRepoManager $repoManager)
    {
        $this->repoManager = $repoManager;
    }

    /**
     * @return array
     */
    public function getLocalRecipes() {
        if (count($this->recipes) == 0) {
           $this->compileRepos();
        }

        return [];
    }

    /**
     * Loads local recipes
     */
    private function compileRepos()
    {
        foreach ($this->repoManager->getConfiguredRepos() as $repo) {
            $recipeFolders = $repo->getRecipeDirectories();
            foreach ($recipeFolders as $recipeFolder) {
                $explodedPath = explode('/', $recipeFolder->getPathname());
                [$author, $package, $version] = array_slice($explodedPath, -3);

                $recipe = new Recipe();
                $recipe->setAuthor($author);
                $recipe->setPackage($package);
                $recipe->setVersion($version);
                $recipe->setRepo($repo);
                $recipe->setRepoSlug($repo->getRepoDirName());
                $recipe->setLocalPath($recipeFolder->getPathname());

                $manifestFile = $recipeFolder->getPathname() . '/manifest.json';
                if (file_exists($manifestFile)) {
                    $recipe->setManifest(json_decode(file_get_contents($manifestFile), true));
                    $recipe->setManifestValid(json_last_error() === JSON_ERROR_NONE);
                }

                $this->recipes[] = $recipe;
            }
        }
    }
}