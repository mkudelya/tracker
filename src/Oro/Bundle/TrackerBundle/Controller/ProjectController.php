<?php

namespace Oro\Bundle\TrackerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @Route("/edit/{projectCode}", name="_tracking_project_edit", defaults={"projectCode" = null})
     * @ParamConverter("project", options={"mapping": {"projectCode": "code"}})
     * @Template()
     * @param Project $project
     * @return mixed
     */
    public function editAction(Project $project = null)
    {
        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();
        $isAdd = true;

        if ($project) {
            $isAdd = false;
            if (!$project) {
                throw new ResourceNotFoundException(
                    $this->get('translator')->trans('layout.sorry_not_existing', array(), 'OroTrackerBundle')
                );
            }

        } else {
            $project = new Project();
        }

        if (false === $this->get('security.context')->isGranted('edit', $project)) {
            throw new AccessDeniedException(
                $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
            );
        }

        $form = $this->createForm(new ProjectType(), $project);
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' && $form->isValid()) {
            $manager->persist($project);
            $manager->flush();

            $flashId = $isAdd ? 'flash.add.project' : 'flash.update.project';
            $request->getSession()->getFlashBag()->add(
                'notice',
                $this->get('translator')->trans($flashId, array(), 'OroTrackerBundle')
            );

            return $this->redirect(
                $this->generateUrl('_tracking_project_show', array('projectCode' => $project->getCode()))
            );
        }

        return array(
            'form' => $form->createView(),
            'project' => $project,
            'isAdd' => $isAdd
        );
    }

    /**
     * @Route("/{projectCode}", name="_tracking_project_show")
     * @ParamConverter("project", options={"mapping": {"projectCode": "code"}})
     * @Template()
     * @param Project $project
     * @return array
     */
    public function showAction(Project $project)
    {
        if (!$project) {
            throw new ResourceNotFoundException(
                $this->get('translator')->trans('layout.sorry_not_existing', array(), 'OroTrackerBundle')
            );
        }

        if (false === $this->get('security.context')->isGranted('view', $project)) {
            throw new AccessDeniedException(
                $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
            );
        }

        return array('project' => $project);
    }
}
