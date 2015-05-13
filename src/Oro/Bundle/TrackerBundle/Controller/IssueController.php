<?php

namespace Oro\Bundle\TrackerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\TrackerBundle\Entity\Comment;
use Oro\Bundle\TrackerBundle\Entity\Issue;
use Oro\Bundle\TrackerBundle\Form\CommentType;
use Oro\Bundle\TrackerBundle\Form\IssueType;

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
        $issues = $this->get('issue')->getIssueListByProjectCode($projectCode);
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
        $issues = $this->get('issue')->getIssueSubListByIssueCode($issueCode);
        return array('issues' => $issues);
    }

    /**
     * @Template("OroTrackerBundle:Issue:list.html.twig")
     * @return array
     */
    public function listByCollaboratorAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $issues = $this->get('issue')->getIssueListByCollaborationUser($user);
        return array('issues' => $issues);
    }

    /**
     * @Route("/{issueCode}/assignee/{id}", name="_tracking_issue_by_assignee_user")
     * @Template("OroTrackerBundle:Issue:list.html.twig")
     * @param integer $id
     * @return array
     */
    public function listByAssigneeAction($id)
    {
        $user = $this->getDoctrine()->getRepository('OroTrackerBundle:User')->find($id);
        $issues = $this->get('issue')->getIssueListByAssigneeUser($user);
        return array('issues' => $issues);
    }

    /**
     * @Route("/{issueCode}/collaborators", name="_tracking_issue_sublist")
     * @Template("OroTrackerBundle:Issue:collaborators.list.html.twig")
     * @param string $issueCode
     * @return array
     */
    public function listCollaboratorByIssueAction($issueCode)
    {
        $issueEntity = $this->getDoctrine()->getRepository('OroTrackerBundle:Issue')->findOneByCode($issueCode);
        $users = $this->get('issue')->getCollaborationListByIssue($issueEntity);
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
        $comments = $this->get('issue')->getCommentListByIssueCode($issueCode);
        $commentFormType = new CommentType();
        $commentEntity = new Comment();
        $form = $this->createForm($commentFormType, $commentEntity);

        return array(
            'comment_form' => $form->createView(),
            'comments' => $comments
        );
    }

    /**
     * @Route("/edit/{issueCode}", name="_tracking_issue_edit")
     * @Route("/addsubtask/{issueCode}", name="_tracking_issue_add_subtask")
     * @Template()
     * @param string $projectCode
     * @param integer $issueCode
     * @return mixed
     */
    public function editAction($projectCode, $issueCode = null)
    {
        $request = $this->getRequest();
        $routeName = $request->get('_route');
        $manager = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $projectEntity = $this->getDoctrine()->getRepository('OroTrackerBundle:Project')->findOneByCode($projectCode);

        if ($routeName == self::ROUTE_ADD_SUBTASK) {
            $methodType = self::IS_ADD_SUBTASK;
        } else {
            if ($issueCode) {
                $methodType = self::IS_EDIT_TASK;
            } else {
                $methodType = self::IS_ADD_TASK;
            }
        }

        if ($methodType == self::IS_EDIT_TASK) {
            $issueEntity = $manager->getRepository('OroTrackerBundle:Issue')->findOneByCode($issueCode);

            if (false === $this->get('security.context')->isGranted('edit', $issueEntity)) {
                throw new AccessDeniedException(
                    $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
                );
            }
        } else {
            if (false === $this->get('security.context')->isGranted('add_issue', $projectEntity)) {
                throw new AccessDeniedException(
                    $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
                );
            }
            $issueEntity = new Issue();
        }

        $issueFormType = new IssueType();
        $issueFormType->setProcessMethod($methodType);
        $form = $this->createForm($issueFormType, $issueEntity);
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' && $form->isValid()) {
            $issueEntity->setProject($projectEntity);

            if ($methodType != self::IS_EDIT_TASK) {
                $issueEntity->setReporter($user);
            }

            if ($methodType == self::IS_ADD_SUBTASK) {
                $parentIssueEntity = $manager->getRepository('OroTrackerBundle:Issue')->findOneByCode($issueCode);
                $issueEntity->setParent($parentIssueEntity);
            }

            $manager->persist($issueEntity);
            $manager->flush();

            if ($methodType == self::IS_ADD_TASK || $methodType == self::IS_ADD_SUBTASK) {
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
                        'projectCode' => $projectCode, 'issueCode' => $issueEntity->getCode()
                    )
                )
            );
        }

        return array(
            'form' => $form->createView(),
            'issueCode' => $issueCode,
            'methodType' => $methodType,
            'project' => $projectEntity
        );
    }

    /**
     * @Route("/{issueCode}/", name="_tracking_issue_show")
     * @Template()
     * @param string $projectCode
     * @param string $issueCode
     * @return array
     */
    public function showAction($projectCode, $issueCode)
    {
        $projectEntity = $this->getDoctrine()->getRepository('OroTrackerBundle:Project')->findOneByCode($projectCode);
        $issueEntity = $this->getDoctrine()->getRepository('OroTrackerBundle:Issue')->findOneByCode($issueCode);

        if (false === $this->get('security.context')->isGranted('view', $issueEntity)) {
            throw new AccessDeniedException(
                $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
            );
        }

        $commentEntity = new Comment();
        $commentFormType = new CommentType();
        $form = $this->createForm($commentFormType, $commentEntity);

        return array(
            'comment_form' => $form->createView(),
            'issue' => $issueEntity,
            'project' => $projectEntity,
            'isStory' => $issueEntity->getType() == 'story' ? true : false,
            'isSubtask' => $issueEntity->getParent() ? true : false
        );
    }

    /**
     * @Route("/{issueCode}/edit_comment/{commentId}", name="_tracking_edit_comment")
     * @Template("OroTrackerBundle:Issue:show.html.twig")
     * @param string $projectCode
     * @param string $issueCode
     * @param integer $commentId
     * @return mixed
     */
    public function editCommentAction($projectCode, $issueCode, $commentId = null)
    {
        $request = $this->getRequest();
        $projectEntity = $this->getDoctrine()->getRepository('OroTrackerBundle:Project')->findOneByCode($projectCode);
        $issueEntity = $this->getDoctrine()->getRepository('OroTrackerBundle:Issue')->findOneByCode($issueCode);
        $manager = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $isAdd = false;

        if ($commentId) {
            $commentEntity = $this->getDoctrine()->getRepository('OroTrackerBundle:Comment')->find($commentId);
            if (false === $this->get('security.context')->isGranted('edit', $commentEntity)) {
                throw new AccessDeniedException(
                    $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
                );
            }
        } else {
            $isAdd = true;
            $commentEntity = new Comment();
            if (false === $this->get('security.context')->isGranted('add_comment', $issueEntity)) {
                throw new AccessDeniedException(
                    $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
                );
            }
        }

        $commentFormType = new CommentType();
        $form = $this->createForm($commentFormType, $commentEntity);

        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' && $form->isValid()) {
            $commentEntity->setIssue($issueEntity);
            $commentEntity->setUser($user);
            $manager->persist($commentEntity);
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
                        'projectCode' => $projectCode, 'issueCode' => $issueCode
                    )
                )
            );
        }

        return array(
            'comment_form' => $form->createView(),
            'issue' => $issueEntity,
            'project' => $projectEntity,
            'isStory' => $issueEntity->getType() == 'story' ? true : false
        );
    }

    /**
     * @Route("/{issueCode}/remove_comment/{commentId}", name="_tracking_remove_comment")
     * @Template("OroTrackerBundle:Issue:show.html.twig")
     * @param string $projectCode
     * @param string $issueCode
     * @param integer $commentId
     * @return mixed
     */
    public function removeCommentAction($projectCode, $issueCode, $commentId = null)
    {
        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();

        if ($commentId) {
            $commentEntity = $this->getDoctrine()->getRepository('OroTrackerBundle:Comment')->find($commentId);

            if (false === $this->get('security.context')->isGranted('delete', $commentEntity)) {
                throw new AccessDeniedException(
                    $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
                );
            }

            $manager->remove($commentEntity);
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
                    'projectCode' => $projectCode, 'issueCode' => $issueCode
                )
            )
        );
    }
}
