<?php
namespace Eshta\FixturesBundle\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use PHPUnit_Framework_TestCase;

/**
 * Class BaseTest
 * @package Eshta\FixturesBundle\Tests
 * @author Omar Shaban <omars@php.net>
 */
abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * EntityManager mock object together with
     * annotation mapping driver and pdo_sqlite
     * database in memory
     *
     * @return EntityManager
     */
    protected function getMockSqliteEntityManager()
    {
        $dbParams = ['driver' => 'pdo_sqlite', 'memory' => true];
        $config = Setup::createAnnotationMetadataConfiguration([], true);
        return EntityManager::create($dbParams, $config);
    }
}
