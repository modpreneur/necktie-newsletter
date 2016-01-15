<?php

namespace Necktie\NewsletterBundle\Drivers\MailChimp\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Class MailChimpSettingsType.
 */
class MailChimpSettingsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('apiKey', 'text');
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'mailChimp_settings';
    }
}
