<?php
namespace Eshta\FixturesBundle\Tests\Repository;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\ORM\EntityManager;
use Eshta\FixturesBundle\Repository\DBALFixtureRepository;
use Eshta\FixturesBundle\Tests\BaseTest;
use Eshta\FixturesBundle\Tests\TestFixture\Fixture1;
use Eshta\FixturesBundle\Tests\TestFixture\OrderedFixture;

/**
 * Class DBALRepositoryTest
 * @package Eshta\FixturesBundle\Tests
 * @author Omar Shaban <omars@php.net>
 */
class DBALRepositoryTest extends BaseTest
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function setUp()
    {
        $this->entityManager = $this->getMockSqliteEntityManager();
    }

    /**
     * @test
     */
    public function initialization()
    {
        $repository = new DBALFixtureRepository($this->entityManager->getConnection());
        $this->assertTrue($repository->initialized());
    }

    /**
     * @test
     */
    public function add()
    {
        $repository = new DBALFixtureRepository($this->entityManager->getConnection());
        $fixture = new Fixture1();
        $repository->add($fixture); // load
        $repository->add($fixture); // do nothing, since its already loaded
        return $fixture;
    }

    /**
     * @test
     */
    public function addOrderedFixture()
    {
        $repository = new DBALFixtureRepository($this->entityManager->getConnection());
        $fixture = new OrderedFixture();
        $repository->add($fixture); // load
        $this->assertTrue($repository->exists($fixture));
    }

    /**
     * @test
     */
    public function exists()
    {
        $fixture = new Fixture1();
        $repository = new DBALFixtureRepository($this->entityManager->getConnection());

        $this->assertFalse($repository->exists($fixture));

        $repository->add($fixture);
        $this->assertTrue($repository->exists($fixture));
    }
}
