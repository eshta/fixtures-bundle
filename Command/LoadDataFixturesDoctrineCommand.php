<?php
namespace Eshta\FixturesBundle\Command;

use Eshta\FixturesBundle\DirectoryResolver\BundleResolver;
use Eshta\FixturesBundle\Executor\ORMExecutor;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Eshta\FixturesBundle\Loader\FixtureLoader;
use Eshta\FixturesBundle\Repository\DBALFixtureRepository;
use InvalidArgumentException;

/**
 * Load persistent data fixtures from bundles.
 * @package Eshta\FixturesBundle\Command
 * @author Omar Shaban <omars@php.net>
 */
class LoadDataFixturesDoctrineCommand extends DoctrineCommand
{
    /**
     * @var FixtureLoader
     */
    protected $loader;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('eshta:fixtures:load')
            ->setDescription('Load data fixtures to your database.')
            ->addArgument('file', InputArgument::OPTIONAL, 'Used to load single file')
            ->addOption(
                'fixtures',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'The directory or file to load data fixtures from.'
            )
            ->addOption(
                'em',
                null,
                InputOption::VALUE_REQUIRED,
                'The entity manager to use for this command.'
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Used in conjunction with file to force load a fixture'
            )
            ->setHelp(
                <<<EOT
                <fg=blue;options=bold>Eshta Fixtures Bundle</fg=blue;options=bold>

The <info>eshta:fixtures:load</info> command loads outstanding  data fixtures from your bundles:

  <info>./app/console eshta:fixtures:load</info>

You can also optionally specify the path to fixtures with the <info>--fixtures</info> option:

  <info>./app/console eshta:fixtures:load --fixtures=/path/to/fixtures1 --fixtures=/path/to/fixtures2</info>
EOT
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $doctrine \Doctrine\Common\Persistence\ManagerRegistry */
        $doctrine = $this->getContainer()->get('doctrine');
        $entityManager = $doctrine->getManager($input->getOption('em'));
        $repository = new DBALFixtureRepository($entityManager->getConnection());

        $this->loader = new FixtureLoader($repository);

        $file = $input->getArgument('file');
        $forceLoad = $input->getOption('force');

        if ($file) {
            $this->loader->loadFile($file, $forceLoad);
        } else {
            $this->loadFromFixturesOption($input->getOption('fixtures'));
        }

        $fixtures = $this->loader->getFixtures();
        if (!$fixtures) {
            if ($file) {
                throw new InvalidArgumentException(
                    'Fixture file is already loaded use --force to force load the fixture'
                );
            } else {
                $output->writeln("\n<info>The database is up to date, no outstanding fixtures to load</info>\n", 'info');
                return;
            }
        }
        
        $executor = new ORMExecutor($entityManager, $repository);

        $executor->setLogger(function ($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });
        $executor->execute($fixtures);
    }

    /**
     * Process directory, and if its not defined, get bundles directory paths
     *
     * @param $directory
     * @return void
     */
    protected function loadFromFixturesOption($directory)
    {
        if ($directory) {
            $paths = is_array($directory) ? $directory : [$directory];
        } else {
            $paths = (new BundleResolver($this->getApplication()->getKernel()))->getPaths();
        }
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $this->loader->loadFromDirectory($path);
            }
        }
    }
}
