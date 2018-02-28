<?php

namespace App\Tests\Service\Compiler;

use App\Entity\Recipe;
use App\Service\Compiler\LocalRecipeCompiler;
use App\Service\RecipeRepoManager;
use App\Tests\RepoAwareTestCase;

/**
 * Class LocalRecipeCompilerTest
 * @package App\Tests\Service\Compiler
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class LocalRecipeCompilerTest extends RepoAwareTestCase
{
    /** @var LocalRecipeCompiler */
    private $compiler;

    function setUp()
    {
        $repoStub = $this->createTestRepoStub();
        $repoStub->expects($this->once())->method('getRecipeDirectories');

        $repoManagerStub = $this->createMock(RecipeRepoManager::class);
        $repoManagerStub->method('getConfiguredRepos')
            ->willReturn([$repoStub]);
        $repoManagerStub->expects($this->once())->method('getConfiguredRepos');

        $this->compiler = new LocalRecipeCompiler($repoManagerStub);
    }

    function testLocalRecipesAreLoadedProperly()
    {
        foreach ($this->compiler->getLocalRecipes() as $recipe) {
            $this->assertInstanceOf(Recipe::class, $recipe);
        }
        $this->assertCount(5, $this->compiler->getLocalRecipes());
    }

    function testManifestFilesAreCheckedForValidJson()
    {
        $countInvalid = 0;
        $countValid = 0;
        foreach ($this->compiler->getLocalRecipes() as $recipe) {
            if ($recipe->getPackage() === 'invalidManifest') {
                $this->assertFalse($recipe->isManifestValid());
                $countInvalid++;
            } elseif ($recipe->getPackage() === 'withManifest') {
                $this->assertTrue($recipe->isManifestValid());
                $countValid++;
            } elseif ($recipe->getPackage() === 'withoutManifest') {
                $this->assertNull($recipe->isManifestValid());
                $this->assertNull($recipe->getManifest());
            }
        }
        $this->assertEquals(1, $countInvalid);
        $this->assertEquals(2, $countValid);
    }

    /**
     * @dataProvider packageResolvingTestProvider
     */
    function testPackageVersionsAreResolvedProperly($author, $package, $version, $expectedCount)
    {
        $recipes = $this->compiler->getLocalRecipesForPackageRequest($author, $package, $version);
        $this->assertCount($expectedCount, $recipes);
        foreach ($recipes as $recipe) {
            $this->assertInstanceOf(Recipe::class, $recipe);
            $this->assertEquals($author, $recipe->getAuthor());
            $this->assertEquals($package, $recipe->getPackage());
            $this->assertLessThanOrEqual(0, version_compare($recipe->getVersion(), $version));
        }
    }

    function packageResolvingTestProvider()
    {
        return [
            ['author1', 'withManifest', '1.0', 1],
            ['author1', 'withManifest', '1.1', 2],
            ['author1', 'withManifest', '1', 0],
            ['author1', 'withManifest', '2', 2],
            ['author1', 'withManifest', '0.7', 0],
            ['author1', 'withManifest', 'dev', 0],
            ['author1', 'withManifest', 'master', 0],
            ['author1', 'withoutManifest', '1.0', 1],
            ['author1', 'withoutManifest', '1.1', 2],
            ['author2', 'withoutManifest', '1.1', 0],
            ['author2', 'withManifest', '1', 0],
            ['author2', 'invalidManifest', '1', 1],
            ['author3', 'noVersions', 'any', 0]
        ];
    }

}