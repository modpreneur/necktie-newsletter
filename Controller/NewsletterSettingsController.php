<?php

namespace Necktie\NewsletterBundle\Controller;


use Necktie\NewsletterBundle\Drivers\MailChimp\Form\MailChimpSettingsType;
use Necktie\NewsletterBundle\Drivers\Maropost\Form\MaropostSettingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class NewsletterSettingsController
 *
 * @Route("/admin/settings/newsletter")
 */
class NewsletterSettingsController extends Controller
{


    /**
     * @Route("/", name="tabs_settings_newsletters")
     *
     * @Security("is_granted('ROLE_ADMIN_SETTING_NEWSLETTER')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $mailchimpApiKey = $this->get('app.config')->getSetting('newsletter', 'mailchimp', 'apiKey');
        $mailchimpForm = $this->createMailchimpForm(['apiKey' => $mailchimpApiKey]);

        $maropostAccountId = $this->get('app.config')->getSetting('newsletter', 'maropost', 'accountId');
        $maropostApiKey = $this->get('app.config')->getSetting('newsletter', 'maropost', 'apiKey');
        $maropostForm = $this->createMaropostForm(['accountId' => $maropostAccountId, 'apiKey' => $maropostApiKey]);

        return $this->render(
            'NecktieNewsletterBundle:Admin/Setting:index.html.twig',
            [
                'mailchimpForm' => $mailchimpForm->createView(),
                'maropostForm' => $maropostForm->createView(),
            ]
        );
    }


    /**
     * @Route("/mailchimp", name="settings_newsletters_mailchimp")
     * @Method({"POST", "PUT"})
     *
     * @Security("is_granted('ROLE_ADMIN_SETTING_NEWSLETTER')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mailchimpAction(Request $request)
    {
        $form = $this->createMailchimpForm(null);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $apiKey = $form->get('apiKey')->getData();
            try {
                $this->get('app.config')->setSetting('newsletter', 'mailchimp', 'apiKey', $apiKey);

                return new JsonResponse(['message' => 'Successfully save']);
            } catch (\Exception $e) {
                $this->get('logger')->addError($e);

                return new JsonResponse(['message' => $e->getMessage()], 500);
            }
        }

        $return = $this->get('form_error_serializer')->serializeFormErrors($form, true, true);

        return new JsonResponse(array('error' => $return), 400);
    }


    /**
     * @Route("/maropost", name="settings_newsletters_maropost")
     * @Method({"POST", "PUT"})
     *
     * @Security("is_granted('ROLE_ADMIN_SETTING_NEWSLETTER')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function MaropostAction(Request $request)
    {
        $form = $this->createMaropostForm(null);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $accountId = $form->get('accountId')->getData();
            $apiKey = $form->get('apiKey')->getData();
            try {
                $this->get('app.config')->setSetting('newsletter', 'maropost', 'accountId', $accountId);
                $this->get('app.config')->setSetting('newsletter', 'maropost', 'apiKey', $apiKey);

                return new JsonResponse(['message' => 'Successfully save']);
            } catch (\Exception $e) {
                $this->get('logger')->addError($e);

                return new JsonResponse(['message' => $e->getMessage()], 500);
            }
        }

        $return = $this->get('form_error_serializer')->serializeFormErrors($form, true, true);

        return new JsonResponse(array('error' => $return), 400);
    }


    private function createMailchimpForm($entity)
    {
        $form = $this->createForm(
            new MailChimpSettingsType(),
            $entity,
            array(
                'action' => $this->generateUrl('settings_newsletters_mailchimp'),
                'method' => 'PUT',
                'attr' => ['class' => 'edit-form'],
            )
        );
        $form->add('submit', 'submit', array('label' => 'Save', 'attr' => array('class' => 'button button-success')));

        return $form;
    }


    private function createMaropostForm($entity)
    {
        $form = $this->createForm(
            new MaropostSettingType(),
            $entity,
            array(
                'action' => $this->generateUrl('settings_newsletters_maropost'),
                'method' => 'PUT',
                'attr' => ['class' => 'edit-form'],
            )
        );
        $form->add('submit', 'submit', array('label' => 'Save', 'attr' => array('class' => 'button button-success')));

        return $form;
    }


}