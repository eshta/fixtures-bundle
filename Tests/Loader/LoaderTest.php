<?php
namespace Eshta\FixturesBundle\Tests\Loader;

use Eshta\FixturesBundle\Loader\FixtureLoader;
use Eshta\FixturesBundle\Repository\DBALFixtureRepository;
use Eshta\FixturesBundle\Tests\BaseTest;
use Eshta\FixturesBundle\Tests\TestFixture\Fixture1;

/**
 * Class LoaderTest
 * @package Eshta\FixturesBundle\Tests\Loader
 * @author Omar Shaban <omars@php.net>
 */
class LoaderTest extends BaseTest
{
    /**
     * @var FixtureLoader
     */
    private $loader;

    const FIXTURES_DIR = __DIR__ . '/../TestFixture/';
    const FIXTURE_FILE = self::FIXTURES_DIR . '/Fixture1.php';

    public function setUp()
    {
        $connection = $this->getMockSqliteEntityManager()->getConnection();
        $dbalRepository = new DBALFixtureRepository($connection);
        $this->loader = new FixtureLoader($dbalRepository);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRepositoryMock()
    {
        return $this->getMockBuilder(DBALFixtureRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['exists'])
            ->getMock();
    }

    /**
     * @return FixtureLoader
     */
    private function getLoaderWithFixtureAlreadyLoaded()
    {
        $dbalRepository = $this->getRepositoryMock();
        $dbalRepository->method('exists')->willReturn(true);
        return new FixtureLoader($dbalRepository);
    }

    /**
     * @test
     */
    public function loadFile()
    {
        $this->loader->loadFile(self::FIXTURE_FILE);
        $this->assertInstanceOf(Fixture1::class, current($this->loader->getFixtures()));
    }

    /**
     * @test
     */
    public function loadFileDoesNotLoadALoadedFixture()
    {
        $loader = $this->getLoaderWithFixtureAlreadyLoaded();
        $loader->loadFile(self::FIXTURE_FILE, false);
        $this->assertCount(0, $loader->getFixtures());
    }

    /**
     * @test
     */
    public function loadFileForceLoad()
    {
        $loader = $this->getLoaderWithFixtureAlreadyLoaded();
        $loader->loadFile(self::FIXTURE_FILE, true);
        $this->assertCount(1, $loader->getFixtures());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function loadNonExistingFile()
    {
        $this->loader->loadFile('Fixture1.php');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function loadFromNonExistingDirectory()
    {
        $this->loader->loadFromDirectory('/tmp/'.mt_rand(1, 514).time());
    }

    /**
     * @test
     */
    public function loadFromDirectory()
    {
        $this->loader->loadFromDirectory(self::FIXTURES_DIR);
        $this->assertCount(2, $this->loader->getFixtures());
    }

    /**
     * @test
     */
    public function loadFromDirectorySkipLoadedFixtures()
    {
        $loader = $this->getLoaderWithFixtureAlreadyLoaded();
        $loader->loadFromDirectory(self::FIXTURES_DIR);
        $this->assertCount(0, $loader->getFixtures());
    }
}
