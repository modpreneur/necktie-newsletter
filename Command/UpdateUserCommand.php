<?php

namespace Necktie\NewsletterBundle\Command;

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
class UpdateUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('necktie:user:newsletter:update')->setDescription('Update user info.')->addArgument(
                'userID',
                InputArgument::REQUIRED,
                'User id'
            );
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

        if (!is_int($userId) || $userId === 0) {
            throw new InvalidArgumentException('UserID must be integer.');
        }

        $user = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('NecktieAppBundle:User')->find(
                $userId
            );

        $this->getContainer()->get('necktie.newsletter')->updateUser($user);
    }

}