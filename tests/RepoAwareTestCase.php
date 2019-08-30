<?php

namespace App\Tests;

use App\RecipeRepo\RecipeRepo;
use PHPUnit\Framework\TestCase;

/**
 * Class RepoAwareTestCase.
 *
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class RepoAwareTestCase extends TestCase
{
    const TEST_REPO_DIR = '/private';

    /**
     * Sets up the testing folder structure.
     */
    public static function setUpBeforeClass()
    {
        $testRepoFolder = static::getTestsFolder().static::TEST_REPO_DIR;
        if (!is_dir($testRepoFolder)) {
            mkdir($testRepoFolder);
        }
        foreach (static::getTestRepoFileStructure() as $author => $packages) {
            mkdir($testRepoFolder.$author);
            foreach ($packages as $package => $versions) {
                mkdir($testRepoFolder.$author.$package);
                foreach ($versions as $version => $files) {
                    mkdir($testRepoFolder.$author.$package.$version);
                    foreach ($files as $name => $contents) {
                        file_put_contents($testRepoFolder.$author.$package.$version.$name, $contents);
                    }
                }
            }
        }
    }

    /**
     * Removes the testing folder structure.
     */
    public static function tearDownAfterClass()
    {
        exec('rm -rf '.static::getTestsFolder().static::TEST_REPO_DIR);
    }

    /**
     * @return string
     */
    protected static function getTestsFolder()
    {
        return __DIR__.'/repo';
    }

    /**
     * @return array
     */
    public static function getTestRepoFileStructure()
    {
        return [
            '/author1' => [
                '/withManifest' => [
                    '/1.0' => [
                        '/manifest.json' => json_encode(['some' => 'thing']),
                    ],
                    '/1.1' => [
                        '/manifest.json' => json_encode(['some' => 'thing']),
                    ],
                ],
                '/withoutManifest' => [
                    '/1.0' => [
                        '/post-install.txt' => 'test',
                    ],
                    '/1.1' => [
                        '/post-install.txt' => 'test',
                        '/test.yml' => 'test',
                    ],
                ],
            ],
            '/author2' => [
                '/invalidManifest' => [
                    '/someversion' => [
                        '/manifest.json' => '{[}',
                    ],
                ],
            ],
            '/author3' => [
                '/noVersions' => [],
            ],
        ];
    }

    /**
     * @param string $author
     * @param string $package
     * @param string $version
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\SplFileInfo
     */
    protected function createRecipeFolderStub(string $author, string $package, string $version)
    {
        $recipeFolderStub = $this->createMock(\SplFileInfo::class);
        $recipeFolderStub->method('getPathname')
            ->willReturn(sprintf('tests/repo/private/%s/%s/%s', $author, $package, $version));

        return $recipeFolderStub;
    }

    /**
     * @return RecipeRepo|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createTestRepoStub()
    {
        $repoStub = $this->createMock(RecipeRepo::class);
        $repoStub->method('getRecipeDirectories')
            ->willReturn([
                $this->createRecipeFolderStub('author1', 'withManifest', '1.0'),
                $this->createRecipeFolderStub('author1', 'withManifest', '1.1'),
                $this->createRecipeFolderStub('author1', 'withoutManifest', '1.0'),
                $this->createRecipeFolderStub('author1', 'withoutManifest', '1.1'),
                $this->createRecipeFolderStub('author2', 'invalidManifest', 'someversion'),
            ]);
        $repoStub->method('getRepoDirName')
            ->willReturn('private');

        return $repoStub;
    }
}
