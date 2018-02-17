<?php

namespace App\Service\RecipeRepo;

use Cz\Git\GitException;
use Cz\Git\GitRepository;

/**
 * Class RecipeRepo
 * @package App\Service\RecipeRepo
 * @author Manuel Voss <manuel.voss@i22.de>
 */
abstract class RecipeRepo
{
    const REPO_PATH = '/var/repo/';

    /** @var GitRepository */
    private $repo;

    /** @var string */
    private $repoUrl;

    /** @var string */
    protected $repoDirName = '';

    /** @var string */
    private $fullRepoPath;

    /**
     * RecipeRepo constructor.
     * @param string $repoUrl
     * @param string $projectDir
     * @throws GitException
     */
    public function __construct(string $repoUrl, string $projectDir)
    {
        $this->repoUrl = $repoUrl;
        $this->fullRepoPath = $projectDir . self::REPO_PATH . $this->repoDirName;
        $this->initializeRepo();
    }

    /**
     * Deletes all repo contents and reclones it from remote
     *
     * @throws GitException
     */
    public function resetRepo()
    {
        if (is_dir($this->fullRepoPath)) {
            array_map('unlink', glob($this->fullRepoPath . '/*.*'));
            unlink($this->fullRepoPath);
        }
        $this->initializeRepo();
    }

    /**
     * Loads the repo, clones if needed
     *
     * @throws GitException
     */
    private function initializeRepo()
    {
        if (!GitRepository::isRemoteUrlReadable($this->repoUrl)) {
            throw new GitException('The repo url ' . $this->repoUrl . ' is not readable');
        }
        if (!is_dir($this->fullRepoPath)) {
            $this->repo = GitRepository::cloneRepository($this->repoUrl, $this->fullRepoPath);
        } else {
            $this->repo = new GitRepository($this->fullRepoPath);
        }
    }
}