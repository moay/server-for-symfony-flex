<?php

namespace App\Service;

use App\Entity\Recipe;

/**
 * Class RecipePublicUrlResolver.
 *
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class RecipePublicUrlResolver
{
    const RECIPE_PREFIXES = [
        'bitbucket.org' => '/src/master/',
        'default' => '/tree/master/',
    ];

    /**
     * @param Recipe $recipe
     *
     * @return string
     */
    public function resolveUrl(Recipe $recipe)
    {
        if (null === $recipe->getRepo()) {
            throw new \InvalidArgumentException('Recipes must have a repo in order to have their url resolved.');
        }

        $repoUrl = $recipe->getRepo()->getRepoUrl();
        $urlProtocol = parse_url($repoUrl, PHP_URL_SCHEME);

        switch ($urlProtocol) {
            case 'http':
            case 'https':
                return $this->resolveHttpUrl($repoUrl, $recipe->getOfficialPackageName());
            case 'ssh':
                return $this->resolveSshUrl($repoUrl, $recipe->getOfficialPackageName());
            case null:
                if ('git@' === substr($repoUrl, 0, 4)) {
                    return $this->resolveSshUrl($repoUrl, $recipe->getOfficialPackageName());
                }
        }

        return $this->buildFallbackUrl($repoUrl, $recipe->getOfficialPackageName());
    }

    /**
     * @param string $repoUrl
     * @param string $packageName
     *
     * @return string
     */
    private function resolveHttpUrl(string $repoUrl, string $packageName)
    {
        if (preg_match('/^(?:https?(?:\:\/\/)?)?([a-zA-Z0-9-_\.]+\.[a-z]+)(?:\:([0-9]+))?\/([a-zA-Z0-9-_]+)\/([a-zA-Z0-9-_]+)(?:\.git)?$/', $repoUrl, $urlParts)) {
            $host = $urlParts[1];
            $port = empty($urlParts[2]) ? null : $urlParts[2];
            $user = $urlParts[3];
            $repo = $urlParts[4];

            return $this->buildUrl($host, implode('/', [$user, $repo]), $packageName, $port);
        }

        return $this->buildFallbackUrl($repoUrl, $packageName);
    }

    /**
     * @param string $repoUrl
     * @param string $packageName
     *
     * @return string
     */
    private function resolveSshUrl(string $repoUrl, string $packageName)
    {
        if (preg_match('/^(?:ssh(?:\:\/\/)?)?(?:git)?\@?([a-zA-Z0-9-_\.]+\.[a-z]+)[:\/](?:([0-9]+)(?:\/))?([a-zA-Z0-9-_\.]+)\/([a-zA-Z0-9-_]+)(?:\.git)?$/', $repoUrl, $urlParts)) {
            $host = $urlParts[1];
            $port = empty($urlParts[2]) ? null : $urlParts[2];
            $user = $urlParts[3];
            $repo = $urlParts[4];

            return $this->buildUrl($host, implode('/', [$user, $repo]), $packageName, $port);
        }

        return $this->buildFallbackUrl($repoUrl, $packageName);
    }

    /**
     * Returns a fallback URL based on best practices used by f.i. github or gitlab.
     *
     * @param string $repoUrl
     * @param string $packageName
     *
     * @return string
     */
    private function buildFallbackUrl(string $repoUrl, string $packageName)
    {
        $host = parse_url($repoUrl, PHP_URL_HOST);
        $port = parse_url($repoUrl, PHP_URL_PORT);
        $path = parse_url($repoUrl, PHP_URL_PATH);

        return $this->buildUrl($host, str_replace('.git', '', $path), $packageName, $port);
    }

    /**
     * @param string   $host
     * @param string   $repo
     * @param string   $packageName
     * @param int|null $port
     * @param bool     $secure
     *
     * @return string
     */
    private function buildUrl(string $host, string $repo, string $packageName, int $port = null, bool $secure = true)
    {
        $url = 'http'.($secure ? 's' : '').'://';
        $url .= rtrim($host, '/');
        if (null !== $port) {
            $url .= ':'.$port;
        }
        $url .= '/'.$repo.(self::RECIPE_PREFIXES[$host] ?? self::RECIPE_PREFIXES['default']).$packageName;

        return $url;
    }
}
