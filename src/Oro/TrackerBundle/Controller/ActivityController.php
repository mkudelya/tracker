<?php

namespace Oro\TrackerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ActivityController extends Controller
{
    /**
     * @Route("/list_where_user_project_member", name="_tracking_activity_list_where_user_project_member")
     * @Template("TrackerBundle:Activity:list.html.twig")
     * @return array
     */
    public function listWhereUserIsProjectMemberAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $activities = $this->get('activity')->getActivityIssueListWhereUserIsProjectMember($user);
        return array('activities' => $activities);
    }

    /**
     * @Route("/list/user{id}", name="_tracking_activity_list_by_user")
     * @Template("TrackerBundle:Activity:list.html.twig")
     * @param integer $id
     * @return array
     */
    public function listByUserAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $manager->getRepository('TrackerBundle:User')->findOneById($id);
        $activities = $this->get('activity')->getActivityIssueListByUser($user);
        return array('activities' => $activities);
    }

    /**
     * @Route("/list_by_project/{projectCode}", name="_tracking_activity_list_by_project")
     * @Template("TrackerBundle:Activity:list.html.twig")
     * @param string $projectCode
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
     * @param string $issueCode
     * @return array
     */
    public function listByIssueAction($issueCode)
    {
        $issueEntity = $this->getDoctrine()->getRepository('TrackerBundle:Issue')->findOneByCode($issueCode);
        $activities = $this->get('activity')->getActivityIssueListByIssue($issueEntity);
        return array('activities' => $activities);
    }
}
