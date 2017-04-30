<?php

namespace CodingBerlin\LocalizationPlugin\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyliusLocalizationCommand extends ContainerAwareCommand {

    public function configure()
    {
        $this->setName("sylius:localization");
        $this->setDescription("wooo");
        $this->addUsage("test");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("woooo");
    }


}