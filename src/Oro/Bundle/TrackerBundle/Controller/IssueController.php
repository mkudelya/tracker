<?php

namespace Oro\Bundle\TrackerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Oro\Bundle\TrackerBundle\Entity\Comment;
use Oro\Bundle\TrackerBundle\Entity\Issue;
use Oro\Bundle\TrackerBundle\Entity\Project;
use Oro\Bundle\TrackerBundle\Form\CommentType;
use Oro\Bundle\TrackerBundle\Form\IssueType;
use Oro\Bundle\UserBundle\Entity\User;

class IssueController extends Controller
{
    const IS_ADD_TASK = 1;
    const IS_EDIT_TASK = 2;
    const IS_ADD_SUBTASK = 3;
    const ROUTE_ADD_SUBTASK = '_tracking_issue_add_subtask';

    /**
     * @Route("/list/", name="_tracking_issue_list")
     * @Template()
     * @param string $projectCode
     * @return array
     */
    public function listAction($projectCode)
    {
        $issues = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OroTrackerBundle:Issue')
            ->getIssueListByProjectCode($projectCode);
        return array('issues' => $issues);
    }

    /**
     * @Route("/{issueCode}/sublist", name="_tracking_issue_sublist")
     * @Template("OroTrackerBundle:Issue:list.html.twig")
     * @param string $issueCode
     * @return array
     */
    public function subtasksListAction($issueCode)
    {
        $issues = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OroTrackerBundle:Issue')
            ->getIssueSubListByIssueCode($issueCode);
        return array('issues' => $issues);
    }

    /**
     * @Template("OroTrackerBundle:Issue:list.html.twig")
     * @return array
     */
    public function listByCollaboratorAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $issues = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OroTrackerBundle:Issue')
            ->getIssueListByCollaborationUser($user);
        return array('issues' => $issues);
    }

    /**
     * @Route("/{issueCode}/assignee/{id}", name="_tracking_issue_by_assignee_user")
     * @ParamConverter("user", options={"mapping": {"id": "id"}})
     * @Template("OroTrackerBundle:Issue:list.html.twig")
     * @param User $user
     * @return array
     */
    public function listByAssigneeAction(User $user)
    {
        $issues = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OroTrackerBundle:Issue')
            ->getIssueListByAssigneeUser($user);
        return array('issues' => $issues);
    }

    /**
     * @Route("/{issueCode}/collaborators", name="_tracking_issue_sublist")
     * @ParamConverter("issue", options={"mapping": {"issueCode": "code"}})
     * @Template("OroTrackerBundle:Issue:collaborators.list.html.twig")
     * @param Issue $issue
     * @return array
     */
    public function listCollaboratorByIssueAction(Issue $issue)
    {
        $users = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OroTrackerBundle:Issue')
            ->getCollaborationListByIssue($issue);
        return array('users' => $users);
    }

    /**
     * @Route("/{issueCode}/comments", name="_tracking_project_comment_list")
     * @Template("OroTrackerBundle:Comment:list.html.twig")
     * @param string $issueCode
     * @return array
     */
    public function listOfCommentsAction($issueCode)
    {
        $comments = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OroTrackerBundle:Issue')
            ->getCommentListByIssueCode($issueCode);
        $commentFormType = new CommentType();
        $commentEntity = new Comment();
        $form = $this->createForm($commentFormType, $commentEntity);

        return array(
            'comment_form' => $form->createView(),
            'comments' => $comments
        );
    }

    /**
     * @Route("/create", name="_tracking_issue_create")
     * @Route("/edit/{issueCode}", name="_tracking_issue_edit", defaults={"issueCode" = null})
     * @ParamConverter("project", options={"mapping": {"projectCode": "code"}})
     * @ParamConverter("issue", options={"mapping": {"issueCode": "code"}})
     * @Template()
     * @param Project $project
     * @param Issue $issue
     * @return mixed
     */
    public function editAction(Project $project, Issue $issue = null)
    {
        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        if ($issue) {
            $methodType = self::IS_EDIT_TASK;
        } else {
            $methodType = self::IS_ADD_TASK;
        }

        if ($methodType === self::IS_EDIT_TASK) {
            if (!$issue) {
                throw new ResourceNotFoundException(
                    $this->get('translator')->trans('layout.sorry_not_existing', array(), 'OroTrackerBundle')
                );
            }

            if (false === $this->get('security.context')->isGranted('edit', $issue)) {
                throw new AccessDeniedException(
                    $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
                );
            }
        } else {
            if (false === $this->get('security.context')->isGranted('add_issue', $project)) {
                throw new AccessDeniedException(
                    $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
                );
            }
            $issue = new Issue();
        }

        $issueFormType = new IssueType();
        $issueFormType->setProcessMethod($methodType);
        $issueFormType->setProject($project);
        $form = $this->createForm($issueFormType, $issue);
        $form->handleRequest($request);

        if ($request->getMethod() === 'POST' && $form->isValid()) {
            $issue->setProject($project);

            if ($methodType !== self::IS_EDIT_TASK) {
                $issue->setReporter($user);
            }

            $manager->persist($issue);
            $manager->flush();

            if ($methodType === self::IS_ADD_TASK) {
                $flashId = 'flash.add.issue';
            } else {
                $flashId = 'flash.update.issue';
            }

            $request->getSession()->getFlashBag()->add(
                'notice',
                $this->get('translator')->trans($flashId, array(), 'OroTrackerBundle')
            );

            return $this->redirect(
                $this->generateUrl(
                    '_tracking_issue_show',
                    array(
                        'projectCode' => $project->getCode(), 'issueCode' => $issue->getCode()
                    )
                )
            );
        }

        return array(
            'form' => $form->createView(),
            'issue' => $issue,
            'methodType' => $methodType,
            'project' => $project
        );
    }

    /**
     * @Route("/addsubtask/{issueCode}", name="_tracking_issue_add_subtask")
     * @ParamConverter("project", options={"mapping": {"projectCode": "code"}})
     * @ParamConverter("parentIssue", options={"mapping": {"issueCode": "code"}})
     * @Template("OroTrackerBundle:Issue:edit.html.twig")
     * @param Project $project
     * @param Issue $parentIssue
     * @return mixed
     */
    public function addSubAction(Project $project, Issue $parentIssue)
    {
        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$parentIssue) {
            throw new ResourceNotFoundException(
                $this->get('translator')->trans('layout.sorry_not_existing', array(), 'OroTrackerBundle')
            );
        }

        if ($parentIssue->getType() !== 'story') {
            throw new AccessDeniedException(
                $this->get('translator')->trans('layout.issue_is_not_story', array(), 'OroTrackerBundle')
            );
        }

        if (false === $this->get('security.context')->isGranted('add_issue', $project)) {
            throw new AccessDeniedException(
                $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
            );
        }

        $issue = new Issue();
        $issueFormType = new IssueType();
        $issueFormType->setProcessMethod(self::IS_ADD_TASK);
        $issueFormType->setProject($project);
        $form = $this->createForm($issueFormType, $issue);
        $form->handleRequest($request);

        if ($request->getMethod() === 'POST' && $form->isValid()) {
            $issue->setProject($project);
            $issue->setReporter($user);
            $issue->setParent($parentIssue);
            $manager->persist($issue);
            $manager->flush();
            $request->getSession()->getFlashBag()->add(
                'notice',
                $this->get('translator')->trans('flash.add.issue', array(), 'OroTrackerBundle')
            );

            return $this->redirect(
                $this->generateUrl(
                    '_tracking_issue_show',
                    array(
                        'projectCode' => $project->getCode(), 'issueCode' => $issue->getCode()
                    )
                )
            );
        }

        return array(
            'form' => $form->createView(),
            'issue' => $parentIssue,
            'methodType' => self::IS_ADD_SUBTASK,
            'project' => $project
        );
    }

    /**
     * @Route("/{issueCode}/", name="_tracking_issue_show")
     * @ParamConverter("project", options={"mapping": {"projectCode": "code"}})
     * @ParamConverter("issue", options={"mapping": {"issueCode": "code"}})
     * @Template()
     * @param Project $project
     * @param Issue $issue
     * @return array
     */
    public function showAction(Project $project, Issue $issue)
    {
        if (!$issue) {
            throw new ResourceNotFoundException(
                $this->get('translator')->trans('layout.sorry_not_existing', array(), 'OroTrackerBundle')
            );
        }

        if (false === $this->get('security.context')->isGranted('view', $issue)) {
            throw new AccessDeniedException(
                $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
            );
        }

        $commentEntity = new Comment();
        $commentFormType = new CommentType();
        $form = $this->createForm($commentFormType, $commentEntity);

        return array(
            'comment_form' => $form->createView(),
            'issue' => $issue,
            'project' => $project,
            'isStory' => $issue->getType() == 'story' ? true : false,
            'isSubtask' => $issue->getParent() ? true : false
        );
    }

    /**
     * @Route("/{issueCode}/edit_comment/{commentId}", name="_tracking_edit_comment", defaults={"commentId" = null})
     * @ParamConverter("project", options={"mapping": {"projectCode": "code"}})
     * @ParamConverter("issue", options={"mapping": {"issueCode": "code"}})
     * @ParamConverter("comment", options={"mapping": {"commentId": "id"}})
     * @Template("OroTrackerBundle:Issue:show.html.twig")
     * @param Project $project
     * @param Issue $issue
     * @param Comment $comment
     * @return mixed
     */
    public function editCommentAction(Project $project, Issue $issue, Comment $comment = null)
    {
        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $isAdd = false;

        if ($comment) {
            if (false === $this->get('security.context')->isGranted('edit', $comment)) {
                throw new AccessDeniedException(
                    $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
                );
            }
        } else {
            $isAdd = true;
            $comment = new Comment();
            if (false === $this->get('security.context')->isGranted('add_comment', $issue)) {
                throw new AccessDeniedException(
                    $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
                );
            }
        }

        $commentFormType = new CommentType();
        $form = $this->createForm($commentFormType, $comment);

        $form->handleRequest($request);

        if ($request->getMethod() === 'POST' && $form->isValid()) {
            $comment->setIssue($issue);
            $comment->setUser($user);
            $manager->persist($comment);
            $manager->flush();

            if ($isAdd) {
                $flashId = 'flash.add.comment';
            } else {
                $flashId = 'flash.update.comment';
            }

            $request->getSession()->getFlashBag()->add(
                'notice',
                $this->get('translator')->trans($flashId, array(), 'OroTrackerBundle')
            );

            return $this->redirect(
                $this->generateUrl(
                    '_tracking_issue_show',
                    array(
                        'projectCode' => $project->getCode(), 'issueCode' => $issue->getCode()
                    )
                )
            );
        }

        return array(
            'comment_form' => $form->createView(),
            'issue' => $issue,
            'project' => $project,
            'isStory' => $issue->getType() == 'story' ? true : false
        );
    }

    /**
     * @Route("/{issueCode}/remove_comment/{commentId}", name="_tracking_remove_comment")
     * @ParamConverter("project", options={"mapping": {"projectCode": "code"}})
     * @ParamConverter("issue", options={"mapping": {"issueCode": "code"}})
     * @ParamConverter("comment", options={"mapping": {"commentId": "id"}})
     * @Template("OroTrackerBundle:Issue:show.html.twig")
     * @param Project $project
     * @param Issue $issue
     * @param Comment $comment
     * @return mixed
     */
    public function removeCommentAction(Project $project, Issue $issue, Comment $comment)
    {
        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();

        if ($comment) {
            if (false === $this->get('security.context')->isGranted('delete', $comment)) {
                throw new AccessDeniedException(
                    $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
                );
            }

            $manager->remove($comment);
            $manager->flush();

            $request->getSession()->getFlashBag()->add(
                'notice',
                $this->get('translator')->trans('flash.delete.comment', array(), 'OroTrackerBundle')
            );
        }

        return $this->redirect(
            $this->generateUrl(
                '_tracking_issue_show',
                array(
                    'projectCode' => $project->getCode(), 'issueCode' => $issue->getCode()
                )
            )
        );
    }
}
