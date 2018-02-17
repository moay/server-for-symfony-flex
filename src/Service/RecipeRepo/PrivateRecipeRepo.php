<?php

namespace App\Service\RecipeRepo;

/**
 * Class PrivateRecipeRepo
 * @package App\Service\RecipeRepo
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class PrivateRecipeRepo extends RecipeRepo
{
    /** @var string */
    protected $repoDirName = 'private';

    /** */
    public function __construct(string $privateRepoUrl, string $projectDir)
    {
        parent::__construct($privateRepoUrl, $projectDir);
    }

}