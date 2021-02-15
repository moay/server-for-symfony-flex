<?php

/*
 * This file is part of the moay server-for-symfony-flex package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command\Recipes;

use App\Exception\RecipeRepoManagerException;
use App\Service\RecipeRepoManager;
use Cz\Git\GitException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class RecipeRepoManagerCommand.
 *
 * @author moay <mv@moay.de>
 */
abstract class RecipeRepoManagerCommand extends Command
{
    const ACTION_NAMESPACE = 'recipes:';

    /** @var RecipeRepoManager */
    private $repoManager;

    /** @var string */
    protected $action;

    /** @var string */
    protected $description;

    /**
     * RecipesInitializeCommand constructor.
     *
     * @param RecipeRepoManager $repoManager
     */
    public function __construct(RecipeRepoManager $repoManager)
    {
        $this->repoManager = $repoManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::ACTION_NAMESPACE.$this->action)
            ->setDescription($this->description);

        $description = sprintf(
            '%s a single repo by selecting \'private\', \'official\' or \'contrib\'. Don\'t select any in order to %s all configured repos.',
            ucfirst($this->action),
            $this->action
        );

        $this->addArgument('repo', InputArgument::IS_ARRAY, $description, []);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $recipeRepos = [
            'private' => 'Private recipes repo',
            'official' => 'Official recipes repo',
            'contrib' => 'Contrib recipes repo',
        ];

        $repos = count($input->getArgument('repo')) > 0
            ? $input->getArgument('repo')
            : array_keys($recipeRepos);

        foreach ($repos as $repo) {
            if (isset($recipeRepos[$repo])) {
                if ($this->repoManager->isConfiguredByDirName($repo)) {
                    try {
                        $this->repoManager->executeOnRepo($this->action, $repo);
                        $actionPast = 'reset' === $this->action ? 'resetted' : $this->action.'d';
                        $io->success(sprintf('%s recipes repo %s.', ucfirst($repo), $actionPast));
                    } catch (RecipeRepoManagerException $e) {
                        $io->error($e->getMessage());
                    } catch (GitException $e) {
                        $io->error('Git error: '.$e->getMessage());
                    }
                }
            } else {
                $io->error('Repo \''.$repo.'\' does not exist. Use \'private\', \'official\' or \'contrib\'.');
            }
        }
    }
}
