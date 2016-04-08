<?php
namespace Eshta\FixturesBundle\Repository;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

/**
 * Class DBALFixtureRepository
 * @package Eshta\Repository
 * @author Omar Shaban <omars@php.net>
 */
class DBALFixtureRepository implements FixtureRepositoryInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName = 'fixtures_log';

    /**
     * DBALFixtureRepository constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->initialize();
    }

    /**
     * Checks if the fixtures table exists
     *
     * @return boolean
     */
    public function initialized()
    {
        try {
            $this->connection->fetchAll(
                "
                SELECT * FROM {$this->tableName};
                LIMIT 1;
                "
            );
            return true;
        } catch (TableNotFoundException $e) {
            return false;
        }
    }

    /**
     * @todo revisit
     * Create table if it does not exist
     *
     * @return void
     */
    private function initialize()
    {
        $schemaManager = $this->connection->getSchemaManager();

        if (!$this->initialized()) {
            $columns = array(
                'id' => new Column('id', Type::getType('integer'), array(
                    'length' => 10,
                    'notnull' => true,
                )),
                'name' => new Column('name', Type::getType('string'), array(
                    'length' => 500
                )),
                'load_order' => new Column('load_order', Type::getType('integer'), array(
                    'length' => 3,
                    'notnull' => false,
                )),
                'date_loaded' => new Column('date_loaded', Type::getType('datetime'))
            );

            $columns['id']->setAutoincrement(true);

            $table = new Table($this->tableName, $columns);
            $table->setPrimaryKey(array('id'));
            $schemaManager->createTable($table);
        }
    }

    /**
     * @param FixtureInterface $fixture
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function exists(FixtureInterface $fixture)
    {
        $key = get_class($fixture);
        $sql = "
          SELECT count(id) AS found
          FROM {$this->tableName}
          WHERE name=?
          ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(1, (string)$key);
        $statement->execute();
        $row = $statement->fetchAll();
        return isset($row[0]['found']) ? $row[0]['found'] > 0 : false;
    }

    /**
     * @param FixtureInterface $fixture
     * @return void
     * @throws \Doctrine\DBAL\DBALException
     */
    public function add(FixtureInterface $fixture)
    {
        if ($this->exists($fixture)) {
            return;
        }

        $order = null;
        if ($fixture instanceof OrderedFixtureInterface) {
            $order = $fixture->getOrder();
        }

        $fixtureReflection = new \ReflectionClass($fixture);
        $className = $fixtureReflection->getName();

        $sql = "INSERT INTO {$this->tableName} (`name`, `load_order`, `date_loaded`)" .
            "VALUES (?, ?, ?)";
        $this->connection->executeQuery(
            $sql,
            [$className, $order, \date('Y-m-d H:i:s')]
        );
    }
}
