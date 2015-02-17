<?php

namespace Oro\TrackerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ActivityController extends Controller
{
    /**
     * @Route("/list_by_user/", name="_tracking_activity_list_by_user")
     * @Template("TrackerBundle:Activity:list.html.twig")
     * @return array
     */
    public function listByUserAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $activities = $this->get('activity')->getActivityIssueListByUser($user);
        return array('activities' => $activities);
    }

    /**
     * @Route("/list_by_project/{projectCode}", name="_tracking_activity_list_by_project")
     * @Template("TrackerBundle:Activity:list.html.twig")
     * @return array
     */
    public function listByProjectAction($projectCode)
    {
        $projectEntity = $this->getDoctrine()->getRepository('TrackerBundle:Project')->findOneByCode($projectCode);
        $activities = $this->get('activity')->getActivityIssueListByProject($projectEntity);
        return array('activities' => $activities);
    }

    /**
     * @Route("/list_by_issue/{issueCode}", name="_tracking_activity_list_by_issue")
     * @Template("TrackerBundle:Activity:list.html.twig")
     * @return array
     */
    public function listByIssueAction($issueCode)
    {
        $issueEntity = $this->getDoctrine()->getRepository('TrackerBundle:Issue')->findOneByCode($issueCode);
        $activities = $this->get('activity')->getActivityIssueListByIssue($issueEntity);
        return array('activities' => $activities);
    }
}
