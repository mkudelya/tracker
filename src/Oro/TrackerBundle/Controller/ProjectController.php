<?php

namespace Oro\TrackerBundle\Controller;

use Oro\TrackerBundle\Entity\Project;
use Oro\TrackerBundle\Form\ProjectType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ProjectController extends Controller
{
    /**
     * @Route("/list", name="_tracking_project_list")
     * @Template()
     */
    public function listAction()
    {
        $projects = $this->getDoctrine()
            ->getRepository('TrackerBundle:Project')
            ->findAll();

        return array('projects' => $projects);
    }

    /**
     * @Route("/edit/{id}", name="_tracking_project_edit")
     * @Template()
     */
    public function editAction($id = null)
    {
        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();

        if ($id) {
            $projectEntity = $manager->getRepository('TrackerBundle:Project')->find($id);
        } else {
            $projectEntity = new Project();
        }

        $form = $this->createForm(new ProjectType(), $projectEntity);
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' && $form->isValid()) {
            $manager->persist($projectEntity);
            $manager->flush();
            return $this->redirect($this->generateUrl('_tracking_project_list'));
        }

        return array(
            'form' => $form->createView(),
            'id' => $id
        );
    }
}
