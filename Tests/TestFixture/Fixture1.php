<?php
namespace Eshta\FixturesBundle\Tests\TestFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class Fixture1
 * @package Eshta\Tests\TestFixture
 * @author Omar Shaban <omars@php.net>
 */
class Fixture1 implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
    }
}
