<?php

namespace Necktie\NewsletterBundle\Controller;

use Doctrine\ORM\EntityManager;
use Necktie\AppBundle\Entity\FilterRule;
use Necktie\AppBundle\Entity\RuleList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/admin/newsletter")
 */
class NewsletterController extends Controller
{
    /**
     * @Route("/", name="newsletter")
     *
     * @Security("is_granted('ROLE_ADMIN_NEWSLETTER_VIEW')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('admin_home'));
        $breadcrumbs->addItem('Newsletter', $this->get('router')->generate('newsletter'));

        $lists = $this->getDoctrine()->getRepository("NecktieAppBundle:RuleList");


        $newsletterList = $this->get('necktie.newsletter')->getLists();

        return $this->render(
            'NecktieNewsletterBundle:Admin:index.html.twig',
            [
                'newsletterList' => $newsletterList,
                'lists' => $lists->findBy([], ['priority' => 'ASC']),
            ]
        );
    }


    /**
     * @Route("/list/", name="new_list")
     * @Route("/list/{service}/{listId}/{type}/", name="edit_list")
     *
     * @Security("is_granted('ROLE_ADMIN_NEWSLETTER')")
     *
     * @param Request $request
     *
     * @param int $listId
     * @param $service
     * @param $type
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, $listId, $service, $type)
    {

        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('admin_home'));
        $breadcrumbs->addItem('Newsletter', $this->get('router')->generate('newsletter'));
        $breadcrumbs->addItem($listId ? 'Edit' : 'New');


        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $entityName = $service.'_'.$listId.'_'.$type;
        $listEntity = $em->getRepository('NecktieAppBundle:RuleList')->findOneBy(['name' => $entityName]);

        if (!$listEntity) {
            $listEntity = new RuleList();
            $listEntity->setMainEntity('NecktieAppBundle:User');
            $listEntity->setName($entityName);
            $listEntity->setPriority(0);
            $listEntity->setCommand('necktie:user:'.$type.' ? '.$listId);

            $em->persist($listEntity);
            $em->flush();
            $this->redirectToRoute('edit_list', ['listId' => $listId, 'service' => $service, 'type' => $type]);
        }

        $ruleIds = $em->getRepository('NecktieAppBundle:FilterRule')->getIdForList($listEntity->getId());
        $form = $this->createForm('list_rules');
        $form->handleRequest($request);


        if ($form->isSubmitted()) {

            $data = $form->getData();
            $groups = $data['listRulesGroup'];

            if ($groups) {
                foreach ($groups as $groupId => $group) {
                    foreach ($group['group'] as $ruleId => $rule) {
                        if ($rule['ruleId'] == null) {
                            $filterRule = new FilterRule();
                        } else {
                            $filterRule = $em->getRepository('NecktieAppBundle:FilterRule')->find($rule['ruleId']);
                            if (isset($ruleIds)) {
                                unset($ruleIds[$rule['ruleId']]);
                            }
                        }

                        $filterRule->setList($listEntity);
                        $filterRule->setGroupId($groupId);
                        $filterRule->setConsequent($rule['consequent']);
                        $filterRule->setAntecedent($rule['antecedent']);
                        $filterRule->setRule($rule['rule']);
                        $em->persist($filterRule);

                        $em->flush();
                    }
                }

                foreach ($ruleIds as $id) {
                    $rule = $em->getRepository('NecktieAppBundle:FilterRule')->find($id);
                    $em->remove($rule);
                }

                $em->flush();
            }

            return $this->redirectToRoute('edit_list', ['listId' => $listId, 'service' => $service, 'type' => $type]);
        } else {
            if (isset($listEntity) && $listEntity != null) {
                $form->setData(
                    ['listRulesGroup' => $this->prepareFormData($listEntity->getId()),]
                );
            }
        }

        return $this->render(
            'NecktieNewsletterBundle:Admin:list.html.twig',
            [
                'form' => $form->createView(),
                'listId' => $listId,
            ]
        );
    }


    /**
     * @Route("/list/remove/{listId}/", name="remove_list")
     *
     * @Security("is_granted('ROLE_ADMIN_NEWSLETTER')")
     *
     * @param int $listId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeListAction($listId)
    {

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $list = $em->getRepository('NecktieAppBundle:RuleList');
        $filterList = $list->find($listId);

        if ($filterList) {
            $em->remove($filterList);
            $em->flush();
            //@todo $this->get('session.flash_bag')->add('success', $this->getParameter('success_remove'));
        }

        return $this->redirectToRoute("newsletter");
    }


    /**
     * @param  int $listId
     *
     * @return array
     */
    private function prepareFormData($listId)
    {

        $listRulesGroup = [];

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');


        $rules = $em->getRepository('NecktieAppBundle:FilterRule')->findBy(['list' => $listId], ['groupId' => 'ASC']);

        foreach ($rules as $rule) {
            $listRulesGroup[$rule->getGroupId()]['group'][$rule->getId()] = [
                'ruleId' => $rule->getId(),
                'rule' => $rule->getRule(),
                'antecedent' => $rule->getAntecedent(),
                'consequent' => $rule->getConsequent(),
            ];
        }

        return $listRulesGroup;
    }


}


