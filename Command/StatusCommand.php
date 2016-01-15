<?php

namespace Necktie\NewsletterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class SubscribeUserCommand
 * @package Necktie\AppBundle\Command
 */
class StatusCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('necktie:newsletter:status')->setDescription('Check config of newsletter services.');
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
        try {
            $driversStatuses = $this->getContainer()->get('necktie.newsletter')->checkDrivers();
            foreach ($driversStatuses as $name => $status) {
                $message = ($status === true) ? 'Configuration is correct.' : '<error>Configuration is incorrect.</error>';
                $output->writeln($name.': '.$message);
            }

        } catch (\Exception $ex) {
            //$this->getContainer()->get('logger')->addError($ex);
            $output->writeln('Error: '.$ex->getMessage());
        }
    }

}