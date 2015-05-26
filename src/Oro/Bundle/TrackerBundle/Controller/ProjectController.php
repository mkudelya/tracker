<?php

namespace Oro\Bundle\TrackerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\TrackerBundle\Entity\Project;
use Oro\Bundle\TrackerBundle\Form\ProjectType;

class ProjectController extends Controller
{
    /**
     * @Route("/list", name="_tracking_project_list")
     * @Template()
     * @return array
     */
    public function listAction()
    {
        $paginator  = $this->get('knp_paginator');
        $projects = $this->getDoctrine()->getRepository('OroTrackerBundle:Project')->findAll();

        $projects = $paginator->paginate(
            $projects,
            $this->getRequest()->query->getInt('page', 1),
            20
        );

        return array('projects' => $projects);
    }

    /**
     * @Route("/create", name="_tracking_project_create")
     * @Route("/edit/{projectCode}", name="_tracking_project_edit")
     * @Template()
     * @param string $projectCode
     * @return mixed
     */
    public function editAction($projectCode = null)
    {
        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();
        $isAdd = true;

        if ($projectCode) {
            $projectEntity = $manager->getRepository('OroTrackerBundle:Project')->findOneByCode($projectCode);
            $isAdd = false;

            if (!$projectEntity) {
                throw new ResourceNotFoundException(
                    $this->get('translator')->trans('layout.sorry_not_existing', array(), 'OroTrackerBundle')
                );
            }

        } else {
            $projectEntity = new Project();
        }

        if (false === $this->get('security.context')->isGranted('edit', $projectEntity)) {
            throw new AccessDeniedException(
                $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
            );
        }

        $form = $this->createForm(new ProjectType(), $projectEntity);
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' && $form->isValid()) {
            $manager->persist($projectEntity);
            $manager->flush();

            $flashId = $isAdd ? 'flash.add.project' : 'flash.update.project';
            $request->getSession()->getFlashBag()->add(
                'notice',
                $this->get('translator')->trans($flashId, array(), 'OroTrackerBundle')
            );

            return $this->redirect(
                $this->generateUrl('_tracking_project_show', array('projectCode' => $projectEntity->getCode()))
            );
        }

        return array(
            'form' => $form->createView(),
            'projectCode' => $projectCode,
            'isAdd' => $isAdd
        );
    }

    /**
     * @Route("/{projectCode}", name="_tracking_project_show")
     * @Template()
     * @param string $projectCode
     * @return array
     */
    public function showAction($projectCode)
    {
        $projectEntity = $this->getDoctrine()->getRepository('OroTrackerBundle:Project')->findOneByCode($projectCode);

        if (!$projectEntity) {
            throw new ResourceNotFoundException(
                $this->get('translator')->trans('layout.sorry_not_existing', array(), 'OroTrackerBundle')
            );
        }

        if (false === $this->get('security.context')->isGranted('view', $projectEntity)) {
            throw new AccessDeniedException(
                $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
            );
        }

        return array('project' => $projectEntity);
    }
}
