<?php

namespace Necktie\NewsletterBundle\Drivers\MailChimp\Form;

use Necktie\NewsletterBundle\Service\NewsletterInterfaceService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * Class SubscribeType.
 */
class SubscribeType extends AbstractType
{
    /** @var  NewsletterInterfaceService */
    private $newsletter;

    /** @var string */
    private $service = 'mailchimp';


    public function __construct($newsletter)
    {
        $this->newsletter = $newsletter;
    }


    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
                'lists',
                'choice',
                [
                    'choices' => $this->newsletter->getLists(),
                ]
            )->add(
                'save',
                'submit',
                array('label' => 'Subscribe', 'attr' => ['class' => 'button button-success'])
            );
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'mailchimp_subscribe';
    }
}
