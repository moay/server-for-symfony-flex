<?php
/*
 * This file is part of the moay server-for-symfony-flex package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Compiler;

use App\Entity\Recipe;
use Symfony\Component\Finder\Finder;

/**
 * Class PackagesCompiler.
 *
 * @author moay <mv@moay.de>
 */
class PackagesCompiler
{
    /**
     * @param array    $requestedPackages
     * @param Recipe[] $localRecipes
     * @param array    $officialEndpointResponse
     *
     * @return array
     */
    public function compilePackagesResponseArray(array $requestedPackages, array $localRecipes, array $officialEndpointResponse)
    {
        return [
            'locks' => $this->getPackageLocks($requestedPackages),
            'manifests' => $this->getManifests($localRecipes, $officialEndpointResponse),
            'vulnerabilities' => $this->getVulnerabilities($officialEndpointResponse),
        ];
    }

    /**
     * @param array $requestedPackages
     *
     * @return array
     */
    private function getPackageLocks(array $requestedPackages)
    {
        $locks = [];
        foreach ($requestedPackages as $package) {
            $locks[implode('/', [$package['author'], $package['package']])] = ['version' => $package['version']];
        }

        return $locks;
    }

    /**
     * @param Recipe[] $localRecipes
     * @param array    $officialResponse
     *
     * @return array
     */
    private function getManifests(array $localRecipes, array $officialResponse)
    {
        $manifests = [];
        foreach ($localRecipes as $recipe) {
            $manifest = [
                'repository' => 'private',
                'package' => $recipe->getOfficialPackageName(),
                'version' => $recipe->getVersion(),
                'manifest' => $this->buildManifest($recipe),
                'files' => $this->getRecipeFiles($recipe),
                'origin' => $recipe->getOfficialPackageName().':'.$recipe->getVersion().'@private:master',
                'not_installable' => false === $recipe->isManifestValid(),
                'is_contrib' => 'contrib' === $recipe->getRepoSlug(),
            ];

            if (empty($manifest['files'])) {
                unset($manifest['files']);
            }

            $manifests[$recipe->getOfficialPackageName()] = $manifest;
        }

        if (is_array($officialResponse) && isset($officialResponse['manifests'])) {
            $manifests = array_merge($officialResponse['manifests'], $manifests);
        }

        return $manifests;
    }

    /**
     * @param Recipe $recipe
     *
     * @return array
     */
    private function buildManifest(Recipe $recipe)
    {
        $manifest = $recipe->getManifest() ?? [];
        $postInstallPath = $recipe->getLocalPath().'/post-install.txt';
        if (file_exists($postInstallPath)) {
            $manifest['post-install-output'] = file($postInstallPath, FILE_IGNORE_NEW_LINES);
        }
        if (empty($manifest)) {
            return [];
        }

        return $manifest;
    }

    /**
     * @param Recipe $recipe
     *
     * @return array
     */
    private function getRecipeFiles(Recipe $recipe)
    {
        $files = [];
        $finder = new Finder();
        $finder->ignoreUnreadableDirs()
            ->in($recipe->getLocalPath())
            ->followLinks()
            ->ignoreDotFiles(false);

        foreach ($finder->files() as $file) {
            if (in_array($file->getRelativePathName(), ['manifest.json', 'post-install.txt'])) {
                continue;
            }
            $files[$file->getRelativePathName()] = [
                'contents' => $file->getContents(),
                'executable' => is_executable($file->getPathname()),
            ];
        }

        return $files;
    }

    /**
     * @param array $officialResponse
     *
     * @return mixed
     */
    private function getVulnerabilities(array $officialResponse)
    {
        if (is_array($officialResponse) && isset($officialResponse['vulnerabilities'])) {
            return $officialResponse['vulnerabilities'];
        }

        return [];
    }
}
