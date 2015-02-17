<?php

namespace Oro\TrackerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ActivityController extends Controller
{
    /**
     * @Route("/list/", name="_tracking_activity_list")
     * @Template()
     * @return array
     */
    public function listAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $activities = $this->get('activity')->getActivityIssueListByUser($user);
        return array('activities' => $activities);
    }
}
