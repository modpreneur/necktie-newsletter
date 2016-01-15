<?php

namespace Necktie\NewsletterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class NewsletterListCommand
 * @package Necktie\AppBundle\Command
 */
class NewsletterListCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('necktie:newsletter:lists')->setDescription('Return all lists.');
    }


    /**
     * Execute the command
     * The environment option is automatically handled.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;

        $servicesLists = $this->getContainer()->get('necktie.newsletter')->getLists();

        $output->writeln('');
        $output->writeln('Key:        Value:');
        $output->writeln('------------------------');
        foreach ($servicesLists as $service => $lists) {
            $output->writeln(ucfirst($service).":");
            foreach ($lists as $index => $value) {
                $output->writeln($index.'  '.$value);
            }
        }

        $output->writeln('');
    }

}