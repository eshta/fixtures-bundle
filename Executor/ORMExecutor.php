<?php
namespace Eshta\FixturesBundle\Executor;

use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Doctrine\Common\Persistence\ObjectManager;
use Eshta\FixturesBundle\Repository\FixtureRepositoryInterface;

/**
 * Class ORMExecutor
 * @package Eshta\FixturesBundle\Executor
 * @author Omar Shaban <omars@php.net>
 */
class ORMExecutor
{
    /**
     * @var FixtureRepositoryInterface
     */
    protected $fixtureRepository;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Doctrine\Common\DataFixtures\Executor\ORMExecutor
     */
    protected $ormExecutor;

    /**
     * ORMExecutorDecorator constructor.
     * @param ObjectManager $objectManager
     * @param FixtureRepositoryInterface $fixtureRepository
     */
    public function __construct(ObjectManager $objectManager, FixtureRepositoryInterface $fixtureRepository)
    {
        $this->fixtureRepository = $fixtureRepository;
        $this->objectManager = $objectManager;
        $this->ormExecutor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor($objectManager);
    }

    /**
     * @param array $fixtures
     * @return void
     */
    public function execute(array $fixtures)
    {
        foreach ($fixtures as $fixture) {
            if ($this->fixtureRepository->exists($fixture)) {
                continue;
            }
            $this->ormExecutor->load($this->objectManager, $fixture);
            $this->fixtureRepository->add($fixture);
        }
    }

    /**
     * @param callable $logger
     */
    public function setLogger($logger)
    {
        return $this->ormExecutor->setLogger($logger);
    }
}
