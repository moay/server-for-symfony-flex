<?php

/*
 * This file is part of the moay symfony-flex-server package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Compiler;

use App\Entity\Recipe;
use App\Service\RecipePublicUrlResolver;
use App\Service\RecipeRepoManager;

/**
 * Class LocalRecipeCompiler
 * @package App\Service\Compiler
 * @author moay <mv@moay.de>
 */
class LocalRecipeCompiler
{
    /**
     * @var RecipeRepoManager
     */
    private $repoManager;

    /**
     * @var RecipePublicUrlResolver
     */
    private $urlResolver;

    /**
     * @var Recipe[]
     */
    private $recipes = [];

    /**
     * LocalRecipeCompiler constructor.
     * @param RecipeRepoManager $repoManager
     */
    public function __construct(RecipeRepoManager $repoManager, RecipePublicUrlResolver $urlResolver)
    {
        $this->repoManager = $repoManager;
        $this->urlResolver = $urlResolver;
    }

    /**
     * @return Recipe[]
     */
    public function getLocalRecipes()
    {
        if (count($this->recipes) == 0) {
            $this->loadLocalRecipes();
        }

        return $this->recipes;
    }

    /**
     * @param string $author
     * @param string $package
     * @param string $version
     * @return Recipe[]
     */
    public function getLocalRecipesForPackageRequest(string $author, string $package, string $version)
    {
        if (count($this->recipes) == 0) {
            $this->loadLocalRecipes();
        }

        $possibleRecipes = array_filter($this->recipes, function (Recipe $recipe) use ($author, $package, $version) {
            if ($recipe->getAuthor() != $author ||
                $recipe->getPackage() != $package ||
                version_compare($recipe->getVersion(), $version) == 1) {
                return false;
            }
            return true;
        });

        return $possibleRecipes;
    }

    /**
     * Loads local recipes
     */
    private function loadLocalRecipes()
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

                $recipe->setPublicUrl($this->urlResolver->resolveUrl($recipe));

                $manifestFile = $recipeFolder->getPathname() . '/manifest.json';
                if (file_exists($manifestFile)) {
                    $manifest = json_decode(file_get_contents($manifestFile), true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $recipe->setManifest($manifest);
                        $recipe->setManifestValid(true);
                    } else {
                        $recipe->setManifestValid(false);
                    }
                }

                $this->recipes[] = $recipe;
            }
        }
    }
}
