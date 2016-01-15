<?php

namespace Necktie\NewsletterBundle\Form;

use Doctrine\ORM\EntityManager;
use Nette\Utils\Strings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * Class ListRulesType
 * @package Necktie\NewsletterBundle\Form
 */
class ListRulesType extends AbstractType
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
        $classesName = [];

        $entityClassNames = $this->entityManager->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        foreach ($entityClassNames as $entity) {
            $index = $this->entityManager->getMetadataFactory()->getMetadataFor($entity)->getName();
            $data = explode('\\', $index);
            $index = $data[0].$data[1].':'.$data[count($data) - 1];
            $value = (
            new \ReflectionClass(
                $this->entityManager->getMetadataFactory()->getMetadataFor($entity)->getName()
            )
            )->getShortName();

            if (Strings::startsWith($index, 'Necktie')) {
                $classesName[$index] = $value;
            }
        }

        $builder->add(
            'listRulesGroup',
            'collection',
            [
                'type' => new ListRulesGroupType($this->entityManager),
                'allow_add' => true,
                'allow_delete' => true,
            ]
        );

        $builder->add('submit', 'submit');
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'list_rules';
    }
}