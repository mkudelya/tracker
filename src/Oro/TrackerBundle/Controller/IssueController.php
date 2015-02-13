<?php

namespace Oro\TrackerBundle\Controller;

use Oro\TrackerBundle\Entity\Issue;
use Oro\TrackerBundle\Form\IssueType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class IssueController extends Controller
{
    const IS_ADD_TASK = 1;
    const IS_EDIT_TASK = 2;
    const IS_ADD_SUBTASK = 3;
    const ROUTE_ADD_SUBTASK = '_tracking_issue_add_subtask';

    /**
     * @Route("/list", name="_tracking_issue_list")
     * @Template()
     */
    public function listAction($projectCode)
    {
        $projectEntity = $this->getDoctrine()->getRepository('TrackerBundle:Project')->findOneByCode($projectCode);
        $issues = $this->getDoctrine()
            ->getRepository('TrackerBundle:Issue')
            ->findBy(array('project' => $projectEntity, 'parent' => null));

        return array('issues' => $issues, 'projectCode' => $projectCode);
    }

    /**
     * @Route("/{issueCode}/sublist", name="_tracking_issue_sublist")
     * @Template("TrackerBundle:Issue:list.html.twig")
     */
    public function subtasksListAction($projectCode, $issueCode)
    {
        $manager = $this->getDoctrine()->getManager();
        $issueEntity = $manager->getRepository('TrackerBundle:Issue')->findOneByCode($issueCode);

        $issues = $this->getDoctrine()
            ->getRepository('TrackerBundle:Issue')
            ->findBy(array('parent' => $issueEntity));

        return array('issues' => $issues, 'projectCode' => $projectCode);
    }

    /**
     * @Route("/edit/{issueCode}", name="_tracking_issue_edit")
     * @Route("/addsubtask/{issueCode}", name="_tracking_issue_add_subtask")
     * @Template()
     * @param string $projectCode
     * @param int $issueCode
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction($projectCode, $issueCode = null)
    {
        $request = $this->getRequest();
        $routeName = $request->get('_route');
        $manager = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $projectEntity = $this->getDoctrine()->getRepository('TrackerBundle:Project')->findOneByCode($projectCode);

        if ($routeName == self::ROUTE_ADD_SUBTASK) {
            $methodType = self::IS_ADD_SUBTASK;
        } else {
            if ($issueCode) {
                $methodType = self::IS_EDIT_TASK;
            } else {
                $methodType = self::IS_ADD_TASK;
            }
        }

        if ($methodType == self::IS_EDIT_TASK) {
            $issueEntity = $manager->getRepository('TrackerBundle:Issue')->findOneByCode($issueCode);
        } else {
            $issueEntity = new Issue();
        }

        $issueFormType = new IssueType();
        $issueFormType->setProcessMethod($methodType);
        $form = $this->createForm($issueFormType, $issueEntity);
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' && $form->isValid()) {
            $issueEntity->setProject($projectEntity);

            if ($methodType != self::IS_EDIT_TASK) {
                $issueEntity->setReporter($user);
            }

            if ($methodType == self::IS_ADD_SUBTASK) {
                $parentIssueEntity = $manager->getRepository('TrackerBundle:Issue')->findOneByCode($issueCode);
                $issueEntity->setParent($parentIssueEntity);
            }

            $manager->persist($issueEntity);
            $manager->flush();

            return $this->redirect($this->generateUrl('_tracking_issue_show', array('projectCode' => $projectCode, 'issueCode' => $issueEntity->getCode())));
        }

        return array(
            'form' => $form->createView(),
            'issueCode' => $issueCode,
            'methodType' => $methodType,
            'project' => $projectEntity
        );
    }

    /**
     * @Route("/{issueCode}", name="_tracking_issue_show")
     * @Template()
     */
    public function showAction($projectCode, $issueCode)
    {
        $projectEntity = $this->getDoctrine()->getRepository('TrackerBundle:Project')->findOneByCode($projectCode);
        $issueEntity = $this->getDoctrine()->getRepository('TrackerBundle:Issue')->findOneByCode($issueCode);

        return array(
            'issue' => $issueEntity,
            'project' => $projectEntity,
            'isStory' => $issueEntity->getType() == 'story' ? true : false
        );
    }
}
