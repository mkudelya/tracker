<?php

namespace Oro\Bundle\TrackerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\TrackerBundle\Entity\Project;
use Oro\Bundle\TrackerBundle\Entity\Issue;

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
     * @Route("/list/user/{id}", name="_tracking_activity_list_by_user")
     * @Template("OroTrackerBundle:Activity:list.html.twig")
     * @param User $user
     * @return array
     */
    public function listByUserAction(User $user)
    {
        $activities = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OroTrackerBundle:Activity')
            ->getActivityIssueListByUser($user);
        return array('activities' => $activities);
    }

    /**
     * @Route("/list_by_project/{code}", name="_tracking_activity_list_by_project")
     * @Template("OroTrackerBundle:Activity:list.html.twig")
     * @param Project $project
     * @return array
     */
    public function listByProjectAction(Project $project)
    {
        $activities = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OroTrackerBundle:Activity')
            ->getActivityIssueListByProject($project);
        return array('activities' => $activities);
    }

    /**
     * @Route("/list_by_issue/{code}", name="_tracking_activity_list_by_issue")
     * @Template("OroTrackerBundle:Activity:list.html.twig")
     * @param Issue $issue
     * @return array
     */
    public function listByIssueAction(Issue $issue)
    {
        $activities = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OroTrackerBundle:Activity')
            ->getActivityIssueListByIssue($issue);
        return array('activities' => $activities);
    }
}
