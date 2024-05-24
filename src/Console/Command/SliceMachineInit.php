<?php

namespace Elgentos\PrismicIO\Console\Command;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SliceMachineInit extends Command
{
    public const PRISMIC_SLICEMACHINE_DIRECTORY = 'prismicio';

    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly ConfigurationInterface $configuration,
        private readonly File $filesystem,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('elgentos:prismic:slice-machine:init');
        $this->setDescription('Initialze Slicemachine on this machine');


        $this->addOption('store-id', 's', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Give store id to use for the configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storeIds = array_filter(array_map(\abs(...), $input->getOption('store-id')));

        $filesystem = $this->filesystem;
        $configuration = $this->configuration;

        $linked = null;

        foreach ($storeIds as $storeId) {
            $store = $this->storeManager->getStore($storeId);

            if (! $configuration->getApiEnabled($store)) {
                continue;
            }

            // Create a directory
            $directory = self::PRISMIC_SLICEMACHINE_DIRECTORY . DIRECTORY_SEPARATOR . $store->getCode();
            $filesystem->checkAndCreateFolder($directory);

            // Write slicemachine json
            $sliceMachineJson = \json_encode([
                'apiEndpoint' => $configuration->getApiEndpoint($store),
                'repositoryName' => preg_replace('@^https://([^\.]+)\..*$@', '\\1', $configuration->getApiEndpoint($store)),
                'adapter' => '@slicemachine/adapter-next',
                'libraries' => [
                    './slices',
                ]
            ], JSON_THROW_ON_ERROR);

            $filesystem->write($directory . DIRECTORY_SEPARATOR . 'slicemachine.config.json', $sliceMachineJson);

            $ignoreFileContents = <<<IGNOREEOF
/node_modules
IGNOREEOF;

            // write gitignore
            $filesystem->write($directory . DIRECTORY_SEPARATOR . '.gitignore', $ignoreFileContents);

            if ($linked) {
                foreach ([
                    'node_modules',
                    'package.json',
                    'package-lock.json',

                    'prismicio-types.d.ts',
                    'slices',
                    'customtypes',
                         ] as $link) {
                    \is_dir($directory . DIRECTORY_SEPARATOR . $link)
                    || \is_link($directory . DIRECTORY_SEPARATOR . $link)
                    || \symlink($linked . DIRECTORY_SEPARATOR . $link, $directory . DIRECTORY_SEPARATOR . $link);
                }
                continue;
            }

            // Write minimum package lock for slicemachine
            $packageJson = \json_encode([
                'dependencies' => [
//                    '@prismicio/client' => '~7.5.0',
                    '@slicemachine/adapter-next' => '~0.3.39',
                    '@prismicio/types' => '~0.2.8',
                    'slice-machine-ui' => '2.0.0',
                ],
                'scripts' => [
                    'slicemachine' => 'start-slicemachine',
                ]
            ], JSON_THROW_ON_ERROR);

            $filesystem->write($directory . DIRECTORY_SEPARATOR . 'package.json', $packageJson);

            $filesystem->checkAndCreateFolder($directory . DIRECTORY_SEPARATOR . 'slices');
            $filesystem->checkAndCreateFolder($directory . DIRECTORY_SEPARATOR . 'customtypes');

            // If not a multirepo we just generate one slicemachine
            if (! $configuration->getMultiRepoEnabled($store)) {
                $output->writeln('This is not configured as a multi-repo so we can do with one repository.');
                $output->writeln('If you need different page-/custom types for different themes, run this command again for that store');
                break;
            }

            $linked =  '..' . DIRECTORY_SEPARATOR . $store->getCode();
        }

        $output->writeln('<comment>We created a directory prismicio with the bootstrap for running Slicemachine</comment>');

        return 0;
    }

}