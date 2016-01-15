<?php

namespace Necktie\NewsletterBundle\Drivers\Maropost\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Class MailChimpSettingsType.
 */
class MaropostSettingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('accountId', 'text')->add('apiKey', 'text');
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
        return 'maropost_settings';
    }
}
