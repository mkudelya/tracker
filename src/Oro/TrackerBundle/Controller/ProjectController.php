<?php

namespace Oro\TrackerBundle\Controller;

use Oro\TrackerBundle\Entity\Project;
use Oro\TrackerBundle\Form\ProjectType;
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
        $projects = $this->get('project')->getList();
        return array('projects' => $projects);
    }

    /**
     * @Route("/edit/{projectCode}", name="_tracking_project_edit")
     * @Template()
     */
    public function editAction($projectCode = null)
    {
        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();
        $isAdd = true;

        if ($projectCode) {
            $projectEntity = $manager->getRepository('TrackerBundle:Project')->findOneByCode($projectCode);
            $isAdd = false;
        } else {
            $projectEntity = new Project();
        }

        $form = $this->createForm(new ProjectType(), $projectEntity);
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' && $form->isValid()) {
            $manager->persist($projectEntity);
            $manager->flush();
            return $this->redirect($this->generateUrl('_tracking_project_show', array('projectCode' => $projectEntity->getCode())));
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
     */
    public function showAction($projectCode)
    {
        $projectEntity = $this->getDoctrine()->getRepository('TrackerBundle:Project')->findOneByCode($projectCode);

        return array('project' => $projectEntity);
    }
}
