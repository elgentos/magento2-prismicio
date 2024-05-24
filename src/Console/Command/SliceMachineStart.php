<?php

namespace Elgentos\PrismicIO\Console\Command;

use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SliceMachineStart extends Command
{

    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('elgentos:prismic:slice-machine:start');
        $this->setDescription('Start Slicemachine on this machine for a given store');

        $this->addOption('store-id', 's', InputOption::VALUE_REQUIRED, 'Give store id to use for the configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storeId = abs($input->getOption('store-id'));

        $store = $this->storeManager->getStore($storeId);

        $directory = 'prismicio' . DIRECTORY_SEPARATOR . $store->getCode();

        $output->writeln('<comment>Starting Slice Machine</comment>');
        $output->writeln('<comment>Store Code: ' . $store->getCode() . '</comment>');

        $output->writeln('npm --prefix ' . $directory . ' install');
        $output->writeln('npm --prefix ' . $directory . ' run slicemachine');

        return 0;
    }
}