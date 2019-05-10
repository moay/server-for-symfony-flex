<?php

/*
 * This file is part of the moay server-for-symfony-flex package.
 *
 * (c) moay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Event;

use App\RecipeRepo\RecipeRepo;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class RepoStatusChangedEvent
 * @package App\Event
 * @author moay <mv@moay.de>
 */
class RepoStatusChangedEvent extends Event
{
    const NAME = "repo.statuschange";

    /** @var RecipeRepo */
    private $recipeRepo;

    /**
     * RepoStatusChangedEvent constructor.
     * @param RecipeRepo $recipeRepo
     */
    public function __construct(RecipeRepo $recipeRepo)
    {
        $this->recipeRepo = $recipeRepo;
    }

    /**
     * @return RecipeRepo
     */
    public function getRecipeRepo()
    {
        return $this->recipeRepo;
    }
}
