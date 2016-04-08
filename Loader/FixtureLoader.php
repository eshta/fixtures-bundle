<?php
namespace Eshta\FixturesBundle\Loader;

use Doctrine\Common\DataFixtures\Loader;
use Eshta\FixturesBundle\Repository\FixtureRepositoryInterface;

/**
 * Class FixtureLoader
 * @package Eshta\FixturesBundle\Loader
 * @author Omar Shaban <omars@php.net>
 */
class FixtureLoader
{
    /**
     * The file extension of fixture files.
     *
     * @var string
     */
    protected $fileExtension = '.php';

    /**
     * @var FixtureRepositoryInterface
     */
    protected $repository;

    /**
     * @var Loader
     */
    protected $loader;

    /**
     * FixtureLoader constructor.
     * @param FixtureRepositoryInterface $repository
     */
    public function __construct(FixtureRepositoryInterface $repository) {
        $this->loader = new Loader();
        $this->repository = $repository;
    }

    /**
     * Find fixtures classes in a given directory and load them.
     *
     * Include the file, instantiate each fixture and add it to
     * fixtures array
     *
     * @param  string $directory Directory to find fixture classes in.
     * @return array  $fixtures Array of loaded fixture object instances
     */
    public function loadFromDirectory($directory)
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid directory', $directory));
        }

        $fixtures = [];
        $includedFiles = $this->getIncludedFiles($directory);
        $declared = get_declared_classes();

        foreach ($declared as $className) {
            $reflectionClass = new \ReflectionClass($className);
            $sourceFile = $reflectionClass->getFileName();

            if (in_array($sourceFile, $includedFiles) && ! $this->loader->isTransient($className)) {
                $fixture = new $className;
                if ($this->repository->exists($fixture)) {
                    continue;
                }
                $fixtures[] = $fixture;
                $this->loader->addFixture($fixture);
            }
        }

        return $fixtures;
    }

    /**
     * @param $fileName
     * @param bool $force
     * @return array
     */
    public function loadFile($fileName, $force = false)
    {
        if (!file_exists($fileName)) {
            throw new \InvalidArgumentException(sprintf('File %s does not exist', $fileName));
        }

        $fixtures = [];

        $file = new \SplFileInfo($fileName);
        $sourceFile = realpath($file->getPathName());
        require_once $sourceFile;
        $declared = get_declared_classes();
        foreach ($declared as $className) {
            $reflectionClass = new \ReflectionClass($className);
            $classSourceFile = $reflectionClass->getFileName();

            if ($classSourceFile == $sourceFile && ! $this->loader->isTransient($className)) {
                $fixture = new $className;
                if ($this->repository->exists($fixture) && $force == false) {
                    continue;
                }
                $fixtures[] = $fixture;
                $this->loader->addFixture($fixture);
            }
        }

        return $fixtures;
    }

    /**
     * @return array
     */
    public function getFixtures()
    {
        return $this->loader->getFixtures();
    }

    /**
     * @param $directory
     * @return array
     */
    protected function getIncludedFiles($directory)
    {
        $includedFiles = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->getBasename($this->fileExtension) == $file->getBasename()) {
                continue; // skip directory curser
            }
            $sourceFile = realpath($file->getPathName());
            require_once $sourceFile;
            $includedFiles[] = $sourceFile;
        }
        return $includedFiles;
    }
}
