<?php
namespace Eshta\FixturesBundle\Tests\Executor;

use Eshta\FixturesBundle\Executor\ORMExecutor;
use Eshta\FixturesBundle\Repository\DBALFixtureRepository;
use Eshta\FixturesBundle\Tests\BaseTest;
use Eshta\FixturesBundle\Tests\TestFixture\Fixture1;

/**
 * Class ExecutorTest
 * @package Eshta\FixturesBundle\Tests\Executor
 * @author Omar Shaban <omars@php.net>
 */
class ExecutorTest extends BaseTest
{
    /**
     * @test
     */
    public function execute()
    {
        $objectManager = $this->getMockSqliteEntityManager();
        $repository = new DBALFixtureRepository($objectManager->getConnection());
        $executor = new ORMExecutor($objectManager, $repository);
        $fixtures = [
            new Fixture1()
        ];

        $executor->execute($fixtures);
        $this->assertTrue($repository->exists($fixtures[0]));
    }

    /**
     * @test
     */
    public function executeSkipPreviouslyLoadedFixture()
    {
        $objectManager = $this->getMockSqliteEntityManager();
        $repository = new DBALFixtureRepository($objectManager->getConnection());
        $executor = new ORMExecutor($objectManager, $repository);

        $fixture = $this->getMockBuilder(Fixture1::class)
            ->setMethods(['load'])
            ->getMock();

        $fixture
            ->expects($this->once())
            ->method('load')
        ;
        $fixtures = [
            $fixture
        ];

        $executor->execute($fixtures);
        $this->assertTrue($repository->exists(current($fixtures)));
        $executor->execute($fixtures);
    }
}
