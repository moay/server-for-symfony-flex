<?php

namespace App\RecipeRepo;

use App\Event\RepoStatusChangedEvent;
use App\Service\Cache;
use Cz\Git\GitException;
use Cz\Git\GitRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
        if (is_dir($this->fullRepoPath)) {
            array_map('unlink', glob($this->fullRepoPath . '/*.*'));
            unlink($this->fullRepoPath);

            $this->logger->info('Repo deleted (' . $this->repoUrl . ')');
        }
        $this->initialize();
    }

    /**
     * Tries to pull the repo, initalizes it if it has not been setup yet.
     *
     * @throws GitException
     */
    public function update()
    {
        if (!($this->repo instanceof GitRepository)) {
            $this->initialize();
        }
        try {
            $this->repo->pull();
        } catch (GitException $e) {
            $this->logger->error('Repo pull failed (' . $this->repoUrl . ')');
            throw $e;
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
        if (!GitRepository::isRemoteUrlReadable($this->repoUrl)) {
            throw new GitException('The repo url ' . $this->repoUrl . ' is not readable');
        }
        if (!is_dir($this->fullRepoPath)) {
            try {
                $this->repo = GitRepository::cloneRepository($this->repoUrl, $this->fullRepoPath);
                $this->logger->info('Repo cloned (' . $this->repoUrl . ')');
            } catch (GitException $e) {
                $this->logger->error('Repo clone failed (' . $this->repoUrl . ')');
                throw $e;
            }
        } else {
            $this->repo = new GitRepository($this->fullRepoPath);
        }
        $this->handleRepoStatusChange();
    }

    /**
     * Diagnose method for the system health report
     *
     * @return array
     */
    public function getStatus()
    {
        try {
            $repo = new GitRepository($this->fullRepoPath);
            $loaded = true;
        } catch (GitException $e) {
            $loaded = false;
        }

        return [
            'remote_readable' => GitRepository::isRemoteUrlReadable($this->repoUrl),
            'local_repo_loaded' => $loaded,
            'updated' => $this->cache->get('repo-updated-' . $this->repoDirName)
        ];
    }

    /**
     * Triggers the status changed event
     */
    private function handleRepoStatusChange()
    {
        $statusChangedEvent = new RepoStatusChangedEvent($this);
        $this->eventDispatcher->dispatch(RepoStatusChangedEvent::NAME, $statusChangedEvent);

        $this->cache->set('repo-updated-' . $this->repoDirName, new \DateTime);
    }
}