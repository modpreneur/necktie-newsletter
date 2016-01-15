<?php

namespace Necktie\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Necktie\AppBundle\Entity\User;


/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Necktie\NewsletterBundle\Entity\NewsletterRepository")
 */
class Newsletter
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var
     * @ORM\Column(type="string")
     */
    private $memberID;

    /**
     * @var
     * @ORM\Column(type="string", nullable=true)
     */
    private $service;

    /**
     * @var
     * @ORM\Column(type="string")
     */
    private $listID;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Necktie\AppBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;


    /**
     * @return mixed
     */
    public function getMemberID()
    {
        return $this->memberID;
    }


    /**
     * @param mixed $memberID
     */
    public function setMemberID($memberID)
    {
        $this->memberID = $memberID;
    }


    /**
     * @return mixed
     */
    public function getListID()
    {
        return $this->listID;
    }


    /**
     * @param mixed $listID
     */
    public function setListID($listID)
    {
        $this->listID = $listID;
    }


    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }


    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }
}
