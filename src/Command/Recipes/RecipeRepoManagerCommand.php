<?php

/*
 * This file is part of the moay symfony-flex-server package.
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
 * Class RecipeRepoManagerCommand
 * @package App\Command\Recipes
 * @author moay <mv@moay.de>
 */
abstract class RecipeRepoManagerCommand extends Command implements RecipeRepoManagerCommandInterface
{
    const ACTION_NAMESPACE = 'recipes:';

    /** @var RecipeRepoManager */
    private $repoManager;

    /**
     * RecipesInitializeCommand constructor.
     * @param RecipeRepoManager $repoManager
     */
    public function __construct(RecipeRepoManager $repoManager)
    {
        $this->repoManager = $repoManager;
        parent::__construct();
    }

    /** */
    protected function configure()
    {
        $this
            ->setName(self::ACTION_NAMESPACE . $this->getAction())
            ->setDescription($this->getDescription());

        $description = sprintf(
            '%s a single repo by selecting \'private\', \'official\' or \'contrib\'. Don\'t select any in order to %s all configured repos.',
            ucfirst($this->getAction()),
            $this->getAction()
        );

        $this->addArgument('repo', InputArgument::IS_ARRAY, $description, []);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $recipeRepos = [
            'private' => 'Private recipes repo',
            'official' => 'Official recipes repo',
            'contrib' => 'Contrib recipes repo'
        ];

        $repos = count($input->getArgument('repo')) > 0
            ? $input->getArgument('repo')
            : array_keys($recipeRepos);

        foreach ($repos as $repo) {
            if (!isset($recipeRepos[$repo])) {
                $io->error('Repo \'' . $repo . '\' does not exist. Use \'private\', \'official\' or \'contrib\'.');
            } else {
                if ($this->repoManager->isConfiguredByDirName($repo)) {
                    try {
                        $this->repoManager->executeOnRepo($this->getAction(), $repo);
                        $actionPast = $this->getAction() == 'reset' ? 'resetted' : $this->getAction() . 'd';
                        $io->success(sprintf('%s recipes repo %s.', ucfirst($repo), $actionPast));
                    } catch (RecipeRepoManagerException $e) {
                        $io->error($e->getMessage());
                    } catch (GitException $e) {
                        $io->error('Git error: ' . $e->getMessage());
                    }
                }
            }
        }

    }
}