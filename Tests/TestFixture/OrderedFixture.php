<?php
namespace Eshta\FixturesBundle\Tests\TestFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class OrderedFixture
 * @package Eshta\FixturesBundle\Tests\TestFixture
 * @author Omar Shaban <omars@php.net>
 */
class OrderedFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}
