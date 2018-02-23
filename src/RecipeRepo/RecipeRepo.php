<?php

/*
 * This file is part of the moay symfony-flex-server package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\RecipeRepo;

use App\Event\RepoStatusChangedEvent;
use App\Service\Cache;
use Cz\Git\GitException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class RecipeRepo
 * @package App\Service\RecipeRepo
 * @author moay <mv@moay.de>
 */
abstract class RecipeRepo
{
    const REPO_PATH = '/var/repo/';

    /** @var GitRepo */
    private $repo;

    /** @var string */
    private $repoUrl;

    /** @var string */
    protected $repoDirName = '';

    /** @var string */
    private $fullRepoPath;

    /** @var FilesystemCache */
    private $cache;

    /** @var LoggerInterface */
    private $logger;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * RecipeRepo constructor.
     * @param string $repoUrl
     * @param string $projectDir
     * @param Cache $cache
     * @param LoggerInterface $logger
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        string $repoUrl,
        string $projectDir,
        Cache $cache,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->repoUrl = $repoUrl;
        $this->fullRepoPath = $projectDir . self::REPO_PATH . $this->repoDirName;
        $this->cache = $cache;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Deletes all repo contents and reclones it from remote
     *
     * @throws GitException
     */
    public function reset()
    {
        $this->remove();
        $this->initialize();
    }

    /**
     * Tries to pull the repo, initalizes it if it has not been setup yet.
     * Tries to backup before and restore in case of failure.
     *
     * @throws GitException
     */
    public function update()
    {
        if (!($this->repo instanceof GitRepo)) {
            $this->initialize();
        }
        try {
            $this->backup();
            $this->repo->pull();
            $this->repo->forceClean();
            $this->wipeBackup();
        } catch (GitException $e) {
            $this->logger->error('Repo pull failed (' . $this->repoUrl . ')');
            $this->restore();
        }

        $this->logger->info('Repo updated (' . $this->repoUrl . ')');
        $this->handleRepoStatusChange();
    }

    /**
     * Loads the repo, clones if needed
     *
     * @throws GitException
     */
    public function initialize()
    {
        if (!GitRepo::isRemoteUrlReadable($this->repoUrl)) {
            throw new GitException('The repo url ' . $this->repoUrl . ' is not readable');
        }
        if (!is_dir($this->fullRepoPath)) {
            try {
                $this->repo = GitRepo::cloneRepository($this->repoUrl, $this->fullRepoPath);
                $this->logger->info('Repo cloned (' . $this->repoUrl . ')');
            } catch (GitException $e) {
                $this->logger->error('Repo clone failed (' . $this->repoUrl . ')');
                throw $e;
            }
        } else {
            $this->repo = new GitRepo($this->fullRepoPath);
        }
        $this->handleRepoStatusChange();
    }

    /**
     * Removes the repo directory
     */
    public function remove()
    {
        if (is_dir($this->fullRepoPath)) {
            $filesystem = new Filesystem();
            $filesystem->remove($this->fullRepoPath);
            $this->logger->info('Repo deleted (' . $this->repoUrl . ')');
            $this->handleRepoStatusChange();
        }
    }

    /**
     * Diagnose method for the system health report
     *
     * @return array
     */
    public function getStatus()
    {
        try {
            $repo = new GitRepo($this->fullRepoPath);
            $loaded = true;
        } catch (GitException $e) {
            $loaded = false;
        }

        return [
            'url' => $this->repoUrl,
            'local_path' => $this->fullRepoPath,
            'remote_readable' => GitRepo::isRemoteUrlReadable($this->repoUrl),
            'downloaded' => $loaded,
            'last_updated' => $this->cache->get('repo-updated-' . $this->repoDirName)
        ];
    }

    /**
     * @return iterable|SplFileInfo[]
     */
    public function getRecipeDirectories()
    {
        if (!is_dir($this->fullRepoPath)) {
            return [];
        }

        $finder = new Finder();
        return $finder->ignoreUnreadableDirs()
            ->in($this->fullRepoPath . '/*/*')
            ->exclude('.git')
            ->directories();
    }

    /**
     * Restores a backup if there was one.
     *
     * If there is no backup, existing files won't be touched.
     */
    private function restore()
    {
        if (is_dir($this->fullRepoPath . '_backup')) {
            $filesystem = new Filesystem();
            $filesystem->rename($this->fullRepoPath . '_backup', $this->fullRepoPath, true);
            $this->logger->info('Repo backup restored (' . $this->repoUrl . ').');
        } else {
            $this->logger->warning('Could not restore repo backup (' . $this->repoUrl . '). There was no backup.');
        };
    }

    /**
     * Creates a backup of the current repo state
     */
    private function backup()
    {
        if (is_dir($this->fullRepoPath)) {
            $this->wipeBackup();
            $filesystem = new Filesystem();
            $filesystem->mirror($this->fullRepoPath, $this->fullRepoPath . '_backup');
            $this->logger->info('Repo backup created (' . $this->repoUrl . ').');
        }
    }

    /**
     * Wipes an existing backup folder if it exists
     */
    private function wipeBackup()
    {
        if (is_dir($this->fullRepoPath . '_backup')) {
            $filesystem = new Filesystem();
            $filesystem->remove($this->fullRepoPath . '_backup');
        }
    }

    /**
     * Triggers the status changed event
     */
    private function handleRepoStatusChange()
    {
        $this->cache->set('repo-updated-' . $this->repoDirName, date('Y-m-d H:i:s'));

        $statusChangedEvent = new RepoStatusChangedEvent($this);
        $this->eventDispatcher->dispatch(RepoStatusChangedEvent::NAME, $statusChangedEvent);
    }

    /**
     * @return string
     */
    public function getRepoUrl()
    {
        return $this->repoUrl;
    }

    /**
     * @return string
     */
    public function getRepoDirName()
    {
        return $this->repoDirName;
    }

    /**
     * @return string
     */
    public function getFullRepoPath()
    {
        return $this->fullRepoPath;
    }
}