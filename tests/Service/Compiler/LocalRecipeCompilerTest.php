<?php

namespace App\Tests\Service\Compiler;

use App\Entity\Recipe;
use App\Service\Compiler\LocalRecipeCompiler;
use App\Service\RecipePublicUrlResolver;
use App\Service\RecipeRepoManager;
use App\Tests\RepoAwareTestCase;

/**
 * Class LocalRecipeCompilerTest.
 *
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class LocalRecipeCompilerTest extends RepoAwareTestCase
{
    /** @var LocalRecipeCompiler */
    private $compiler;

    public function setUp(): void
    {
        $repoStub = $this->createTestRepoStub();
        $repoStub->expects($this->once())->method('getRecipeDirectories');

        $repoManagerStub = $this->createMock(RecipeRepoManager::class);
        $repoManagerStub->method('getConfiguredRepos')
            ->willReturn([$repoStub]);
        $repoManagerStub->expects($this->once())->method('getConfiguredRepos');

        $urlResolverStub = $this->createMock(RecipePublicUrlResolver::class);
        $urlResolverStub->method('resolveUrl')
            ->willReturn('testUrl');
        $urlResolverStub->expects($this->exactly(5))->method('resolveUrl');

        $this->compiler = new LocalRecipeCompiler($repoManagerStub, $urlResolverStub);
    }

    public function testLocalRecipesAreLoadedProperly()
    {
        foreach ($this->compiler->getLocalRecipes() as $recipe) {
            $this->assertInstanceOf(Recipe::class, $recipe);
        }
        $this->assertCount(5, $this->compiler->getLocalRecipes());
    }

    public function testManifestFilesAreCheckedForValidJson()
    {
        $countInvalid = 0;
        $countValid = 0;
        foreach ($this->compiler->getLocalRecipes() as $recipe) {
            if ('invalidManifest' === $recipe->getPackage()) {
                $this->assertFalse($recipe->isManifestValid());
                ++$countInvalid;
            } elseif ('withManifest' === $recipe->getPackage()) {
                $this->assertTrue($recipe->isManifestValid());
                ++$countValid;
            } elseif ('withoutManifest' === $recipe->getPackage()) {
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
    public function testPackageVersionsAreResolvedProperly($author, $package, $version, $expectedCount)
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

    public function packageResolvingTestProvider()
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
            ['author3', 'noVersions', 'any', 0],
        ];
    }
}
