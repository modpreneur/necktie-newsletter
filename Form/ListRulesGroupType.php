<?php

namespace Necktie\NewsletterBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * Class ListRulesGroupType
 * @package Necktie\NewsletterBundle\Form
 */
class ListRulesGroupType extends AbstractType
{
    /** @var EntityManager */
    private $entityManager;


    /**
     * RuleType constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'group',
            'collection',
            [
                'type' => new RuleType($this->entityManager),
                'allow_add' => true,
                'allow_delete' => true,
            ]
        );

    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'list_rules_group';
    }

}