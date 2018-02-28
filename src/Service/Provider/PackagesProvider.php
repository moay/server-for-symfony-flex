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
use App\Service\Compiler\LocalRecipeCompiler;
use App\Service\Compiler\PackagesCompiler;
use App\Service\OfficialEndpointProxy;

/**
 * Class AliasesProvider
 * @package App\Service\Provider
 * @author moay <mv@moay.de>
 */
class PackagesProvider
{
    /** @var LocalRecipeCompiler */
    private $recipeCompiler;

    /** @var OfficialEndpointProxy */
    private $officialEndpointProxy;

    /** @var PackagesCompiler */
    private $packagesCompiler;

    /**
     * AliasesProvider constructor.
     * @param LocalRecipeCompiler $recipeCompiler
     * @param bool $enableProxy
     * @param OfficialEndpointProxy $officialEndpointProxy
     * @param PackagesCompiler $packagesCompiler
     */
    public function __construct(
        LocalRecipeCompiler $recipeCompiler,
        bool $enableProxy,
        OfficialEndpointProxy $officialEndpointProxy,
        PackagesCompiler $packagesCompiler
    ) {
        $this->recipeCompiler = $recipeCompiler;
        if ($enableProxy) {
            $this->officialEndpointProxy = $officialEndpointProxy;
        }
        $this->packagesCompiler = $packagesCompiler;
    }

    /**
     * Provides data for requested packages
     *
     * @param string $packagesRequestString
     * @return array
     * @throws \Exception
     * @throws \Http\Client\Exception
     */
    public function providePackages(string $packagesRequestString)
    {
        $requestedPackages = $this->parseRequestedPackages($packagesRequestString);

        return $this->packagesCompiler->compilePackagesResponseArray(
            $requestedPackages,
            $this->getLocalRecipes($requestedPackages),
            $this->getOfficialProxyResponse($packagesRequestString)
        );
    }

    /**
     * @param array $requestedPackages
     * @return Recipe[]
     */
    private function getLocalRecipes(array $requestedPackages)
    {
        $recipes = [];

        foreach ($requestedPackages as $package) {
            $localRecipe = $this->getLocalRecipe($package);

            if ($localRecipe instanceof Recipe) {
                $recipes[implode('_', $package)] = $localRecipe;
            }
        }

        return $recipes;
    }

    /**
     * @param string $packagesRequestString
     * @return array|string
     * @throws \Exception
     * @throws \Http\Client\Exception
     */
    private function getOfficialProxyResponse(string $packagesRequestString)
    {
        if ($this->officialEndpointProxy instanceof OfficialEndpointProxy) {
            return $this->officialEndpointProxy->getPackages($packagesRequestString);
        }
        return [];
    }

    /**
     * @param array $package
     * @return Recipe|null
     */
    private function getLocalRecipe(array $package)
    {
        $localRecipes = $this->recipeCompiler->getLocalRecipesForPackageRequest($package['author'], $package['package'], $package['version']);
        if (count($localRecipes) > 1) {
            usort($localRecipes, function (Recipe $recipe1, Recipe $recipe2) {
                if ($recipe1->getVersion() == $recipe2->getVersion()) {
                    if ($recipe1->getRepoSlug() != $recipe2->getRepoSlug()) {
                        if ($recipe1->getRepoSlug() === 'private') {
                            return -1;
                        }
                        if ($recipe2->getRepoSlug() === 'private') {
                            return 1;
                        }
                        if ($recipe1->getRepoSlug() === 'official') {
                            return -1;
                        }
                        return 1;
                    }
                    return -1;
                }
                return version_compare($recipe1->getVersion(), $recipe2->getVersion()) * -1;
            });
        }
        if (count($localRecipes) > 0) {
            return $localRecipes[0];
        }
        return null;
    }

    /**
     * Parses the request string and provides an array of requested packages
     *
     * @param string $packagesRequestString
     * @return array
     */
    private function parseRequestedPackages(string $packagesRequestString)
    {
        $packages = [];
        foreach (explode(';', rtrim($packagesRequestString, ';')) as $requestedPackage) {
            [$author, $package, $version, $timestamp] = explode(',', $requestedPackage);
            $packages[] = [
                'author' => $author,
                'package' => $package,
                'version' => preg_replace('/^[iurv]+/', '', $version)
            ];
        }
        return $packages;
    }
}