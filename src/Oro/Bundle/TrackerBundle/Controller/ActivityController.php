<?php

namespace Oro\Bundle\TrackerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ActivityController extends Controller
{
    /**
     * @Route("/list_where_user_project_member", name="_tracking_activity_list_where_user_project_member")
     * @Template("OroTrackerBundle:Activity:list.html.twig")
     * @return array
     */
    public function listWhereUserIsProjectMemberAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $activities = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OroTrackerBundle:Activity')
            ->getActivityIssueListWhereUserIsProjectMember($user);
        return array('activities' => $activities);
    }

    /**
     * @Route("/list/user{id}", name="_tracking_activity_list_by_user")
     * @Template("OroTrackerBundle:Activity:list.html.twig")
     * @param integer $id
     * @return array
     */
    public function listByUserAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $manager->getRepository('OroUserBundle:User')->findOneById($id);
        $activities = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OroTrackerBundle:Activity')
            ->getActivityIssueListByUser($user);
        return array('activities' => $activities);
    }

    /**
     * @Route("/list_by_project/{projectCode}", name="_tracking_activity_list_by_project")
     * @Template("OroTrackerBundle:Activity:list.html.twig")
     * @param string $projectCode
     * @return array
     */
    public function listByProjectAction($projectCode)
    {
        $projectEntity = $this->getDoctrine()->getRepository('OroTrackerBundle:Project')->findOneByCode($projectCode);
        $activities = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OroTrackerBundle:Activity')
            ->getActivityIssueListByProject($projectEntity);
        return array('activities' => $activities);
    }

    /**
     * @Route("/list_by_issue/{issueCode}", name="_tracking_activity_list_by_issue")
     * @Template("OroTrackerBundle:Activity:list.html.twig")
     * @param string $issueCode
     * @return array
     */
    public function listByIssueAction($issueCode)
    {
        $issueEntity = $this->getDoctrine()->getRepository('OroTrackerBundle:Issue')->findOneByCode($issueCode);
        $activities = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OroTrackerBundle:Activity')
            ->getActivityIssueListByIssue($issueEntity);
        return array('activities' => $activities);
    }
}
