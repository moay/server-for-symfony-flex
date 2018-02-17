<?php

namespace App\Event;

use App\RecipeRepo\RecipeRepo;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class RepoStatusChangedEvent
 * @package App\Event
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class RepoStatusChangedEvent extends Event
{
    const NAME="repo.statuschange";

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