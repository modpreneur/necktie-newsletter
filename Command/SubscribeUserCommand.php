<?php

namespace Necktie\NewsletterBundle\Command;

use Necktie\NewsletterBundle\Exception\NewsletterException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;


/**
 * Class SubscribeUserCommand
 * @package Necktie\AppBundle\Command
 */
class SubscribeUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('necktie:user:subscribe')->setDescription('Subscribe user to newsletter list.')->addArgument(
                'userID',
                InputArgument::REQUIRED,
                'User id'
            )->addArgument(
                'list',
                InputArgument::REQUIRED,
                'List name.'
            );;
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
        $userId = intval($input->getArgument('userID'));
        $list = $input->getArgument('list');

        if (!is_int($userId) || $userId === 0) {
            throw new InvalidArgumentException('UserID must be integer.');
        }

        $user = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('NecktieAppBundle:User')->find(
                $userId
            );

        try {
            $this->getContainer()->get('necktie.newsletter')->subscribeUser($user, $list);
        } catch (NewsletterException $ex) {
            $this->getContainer()->get('logger')->addError($ex);
            $output->writeln('<error>Error - user not added to list: '.$ex->getMessage().'</error>');
        }
    }

}