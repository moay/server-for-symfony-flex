<?php

/*
 * This file is part of the i22 symfony-flex-server package.
 *
 * (c) i22 Digitalagentur GmbH <info@i22.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\RecipeRepo\RecipeRepo;

class Recipe
{
    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $package;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $localPath;

    /**
     * @var RecipeRepo
     */
    private $repo;

    /**
     * @var string
     */
    private $repoSlug;

    /**
     * @var array
     */
    private $manifest;

    /**
     * @var bool
     */
    private $manifestValid;

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param string $package
     */
    public function setPackage(string $package)
    {
        $this->package = $package;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getLocalPath()
    {
        return $this->localPath;
    }

    /**
     * @param string $localPath
     */
    public function setLocalPath(string $localPath)
    {
        $this->localPath = $localPath;
    }

    /**
     * @return RecipeRepo
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * @param RecipeRepo $repo
     */
    public function setRepo(RecipeRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return string
     */
    public function getRepoSlug()
    {
        return $this->repoSlug;
    }

    /**
     * @param string $repoSlug
     */
    public function setRepoSlug(string $repoSlug)
    {
        $this->repoSlug = $repoSlug;
    }

    /**
     * @return array
     */
    public function getManifest()
    {
        return $this->manifest;
    }

    /**
     * @param array $manifest
     */
    public function setManifest(array $manifest)
    {
        $this->manifest = $manifest;
    }

    /**
     * @return bool
     */
    public function isManifestValid()
    {
        return $this->manifestValid;
    }

    /**
     * @param bool $manifestValid
     */
    public function setManifestValid(bool $manifestValid)
    {
        $this->manifestValid = $manifestValid;
    }
}