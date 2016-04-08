<?php
namespace Eshta\FixturesBundle\DirectoryResolver;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class BundleResolver
 * @package Eshta\DirectoryResolver
 * @author Omar Shaban <omars@php.net>
 */
class BundleResolver
{
    /**
     * @var string
     */
    private $subDirectory = '/DataFixtures/ORM/';

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * BundleResolver constructor.
     * @param KernelInterface $kernel
     * @param null|string $subDirectory
     */
    public function __construct(KernelInterface $kernel, $subDirectory = null)
    {
        $this->kernel = $kernel;
        if ($subDirectory) {
            $this->subDirectory = $subDirectory;
        }
    }

    /**
     * @return array
     */
    public function getPaths()
    {
        $paths = [];
        foreach ($this->kernel->getBundles() as $bundle) {
            $paths[] = $bundle->getPath() . $this->subDirectory;
        }
        return $paths;
    }
}
