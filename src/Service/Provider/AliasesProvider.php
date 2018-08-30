<?php

/*
 * This file is part of the moay symfony-flex-server package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Provider;

use App\Entity\Recipe;
use App\Service\Cache;
use App\Service\Compiler\LocalRecipeCompiler;
use App\Service\OfficialEndpointProxy;
use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * Class AliasesProvider
 * @package App\Service\Provider
 * @author moay <mv@moay.de>
 */
class AliasesProvider
{
    const LOCAL_ALIASES_CACHE_KEY = 'aliases-local';

    /** @var LocalRecipeCompiler */
    private $recipeCompiler;

    /** @var OfficialEndpointProxy */
    private $officialEndpointProxy;

    /** @var FilesystemCache */
    private $cache;

    /**
     * AliasesProvider constructor.
     * @param LocalRecipeCompiler $recipeCompiler
     * @param bool $enableProxy
     * @param OfficialEndpointProxy $officialEndpointProxy
     * @param Cache $cache
     */
    public function __construct(
        LocalRecipeCompiler $recipeCompiler,
        bool $enableProxy,
        OfficialEndpointProxy $officialEndpointProxy,
        Cache $cache
    ) {
        $this->recipeCompiler = $recipeCompiler;
        if ($enableProxy) {
            $this->officialEndpointProxy = $officialEndpointProxy;
        }
        $this->cache = $cache();
    }

    /**
     * Provides all available aliases. Official aliases are merged if proxy is enabled
     *
     * @return array
     */
    public function provideAliases()
    {
        $aliases = $this->getLocalAliases();
        if ($this->officialEndpointProxy instanceof OfficialEndpointProxy) {
            $officialAliases = $this->officialEndpointProxy->getAliases();
            if (is_array($officialAliases)) {
                $aliases = array_merge($officialAliases, $aliases);
            }
        }
        ksort($aliases);
        return $aliases;
    }

    /**
     * Returns an array of all locally available aliases
     *
     * @return array
     */
    public function getLocalAliases()
    {
        if ($this->cache->has(self::LOCAL_ALIASES_CACHE_KEY)) {
            return $this->cache->get(self::LOCAL_ALIASES_CACHE_KEY);
        }

        $aliases = [];
        $recipes = $this->recipeCompiler->getLocalRecipes();

        foreach ($recipes as $recipe) {
            if ($recipe->getManifest() !== null && isset($recipe->getManifest()['aliases'])) {
                foreach ($recipe->getManifest()['aliases'] as $alias) {
                    if (isset($aliases[$alias])) {
                        $recipe = $this->resolveAliasConflict($aliases[$alias], $recipe);
                    }

                    $aliases[$alias] = $recipe;
                }
            }
        }

        $aliases =  array_map(function (Recipe $recipe) {
            return $recipe->getOfficialPackageName();
        }, $aliases);

        $this->cache->set(self::LOCAL_ALIASES_CACHE_KEY, $aliases);
        return $aliases;

    }

    /**
     * If one of the recipes is local, it will be returned.
     * If not, the recipe with an official alias will be returned.
     * If there is none, $recipe1 will be returned.
     *
     * @param Recipe $recipe1
     * @param Recipe $recipe2
     * @return Recipe
     */
    private function resolveAliasConflict(Recipe $recipe1, Recipe $recipe2)
    {
        if ($recipe1->getRepoSlug() != $recipe2->getRepoSlug()) {
            if ($recipe1->getRepoSlug() === 'private') {
                return $recipe1;
            }
            if ($recipe2->getRepoSlug() === 'private') {
                return $recipe2;
            }
            if ($recipe1->getRepoSlug() === 'official') {
                return $recipe1;
            }
            return $recipe2;
        }
        return $recipe1;
    }
}
