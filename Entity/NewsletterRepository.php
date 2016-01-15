<?php

namespace Necktie\NewsletterBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Necktie\AppBundle\Entity\User;


/**
 * Class NewsletterRepository.
 */
class NewsletterRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getListByUser(User $user)
    {
        return $this->createQueryBuilder('Newsletter')->andWhere('Newsletter.user = :user')->orderBy(
                'Newsletter.listID'
            )->setParameter('user', $user)->getQuery()->execute();
    }


    /**
     * @param User $user
     * @param string $list
     *
     * @return mixed
     */
    public function getUserIdFromList($user, $list)
    {
        return $this->createQueryBuilder('Newsletter')->where('Newsletter.user = :user')->andWhere(
                'Newsletter.listID = :list'
            )->setParameter('list', $list)->setParameter('user', $user)->getQuery()->execute();
    }


    /**
     * @param User $user
     * @return array
     */
    public function getUserLists(User $user)
    {
        $lists = [];
        $query = $this->createQueryBuilder('Newsletter')->where('Newsletter.user = :user')->setParameter(
                'user',
                $user
            )->getQuery()->execute();

        foreach ($query as $row) {
            /** @var Newsletter $row */
            $lists[$row->getService()][] = [$row->getMemberID(), $row->getListID()];
        }

        return $lists;
    }

}
