<?php
namespace Eshta\FixturesBundle\Repository;

use Doctrine\Common\DataFixtures\FixtureInterface;

/**
 * Interface FixtureRepositoryInterface
 * @package Eshta\Repository
 * @author Omar Shaban <omars@php.net>
 */
interface FixtureRepositoryInterface
{
    /**
     * @param FixtureInterface $fixture
     * @return bool
     */
    public function exists(FixtureInterface $fixture);

    /**
     * @param FixtureInterface $fixtureInterface
     * @return mixed
     */
    public function add(FixtureInterface $fixtureInterface);

    /**
     * Checks if the fixtures table exists
     *
     * @return boolean
     */
    public function initialized();
}
