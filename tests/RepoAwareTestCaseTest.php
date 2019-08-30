<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Class RepoAwareTestCaseTest.
 *
 * @author Manuel Voss <manuel.voss@i22.de>
 */
class RepoAwareTestCaseTest extends TestCase
{
    public function testRepoFolderDoesntExistOutsideTestCase()
    {
        $this->assertFalse(is_dir(__DIR__.'/repo/private'));
    }

    public function testTestRepoIsSetupProperly()
    {
        RepoAwareTestCase::setUpBeforeClass();
        $this->assertTrue(is_dir(__DIR__.'/repo/private'));
        $this->assertTrue(file_exists(__DIR__.'/repo/private/author1/withManifest/1.0/manifest.json'));
        $this->assertTrue(file_exists(__DIR__.'/repo/private/author1/withoutManifest/1.1/post-install.txt'));
        $this->assertTrue(file_exists(__DIR__.'/repo/private/author2/invalidManifest/someversion/manifest.json'));
    }

    public function testRepoIsProperlyDestroyed()
    {
        RepoAwareTestCase::tearDownAfterClass();
        $this->testRepoFolderDoesntExistOutsideTestCase();
    }
}
