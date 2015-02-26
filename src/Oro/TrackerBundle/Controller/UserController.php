<?php

namespace Oro\TrackerBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use FOS\UserBundle\Event\FormEvent;

class UserController extends Controller
{
    /**
     * @Route("/list", name="_tracking_user_list")
     * @Template()
     * @return array
     */
    public function listAction()
    {
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();

        return array('users' => $users);
    }

    /**
     * @Route("/show/{username}", name="_tracking_user_profile")
     * @Template()
     * @param string $username
     * @return array
     */
    public function showAction($username = null)
    {
        $currentUser = $this->get('security.context')->getToken()->getUser();

        if (!empty($username)) {
            $manager = $this->getDoctrine()->getManager();
            $userEntity = $manager->getRepository('TrackerBundle:User')->findOneByUsername($username);

            if ($userEntity == null) {
                throw new NotFoundHttpException('Sorry not existing!');
            }
        } else {
            $userEntity = $this->get('security.context')->getToken()->getUser();
        }

        return array('user' => $userEntity, 'currentUser' => $currentUser);
    }

    /**
     * @Route("/edit/{id}", name="_tracking_user_edit")
     * @Template()
     * @param integer $id
     * @return array
     */
    public function editAction($id)
    {
        $currentUserEntity = $this->get('security.context')->getToken()->getUser();

        $formFactory = $this->get('fos_user.registration.form.factory');
        $form = $formFactory->createForm();

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array("id" => $id));

        if ($user == null) {
            throw new NotFoundHttpException('Sorry not existing!');
        }

        if (!$currentUserEntity->hasRole('ROLE_ADMINISTRATOR') && $currentUserEntity->getId() != $user->getId()) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $form->setData($user);

        return array('form' => $form->createView(), 'user' => $user, 'id' => $id);
    }

    /**
     * @Route("/update/{id}", name="_tracking_user_update")
     * @Template()
     * @param integer $id
     * @return mixed
     */
    public function updateAction($id)
    {
        $currentUserEntity = $this->get('security.context')->getToken()->getUser();

        $request = $this->getRequest();
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */

        $user = $userManager->findUserBy(array("id" => $id));
        $user->setEnabled(true);

        if (!$currentUserEntity->hasRole('ROLE_ADMINISTRATOR') && $currentUserEntity->getId() != $user->getId()) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $requestParams = $request->request->get('fos_user_registration_form');
        if (!$currentUserEntity->hasRole('ROLE_ADMINISTRATOR') && isset($requestParams['roles'])) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);

            $user->upload();

            $userManager->updateUser($user);

            $request->getSession()->getFlashBag()->add(
                'notice',
                $this->get('translator')->trans('flash.update.user', array(), 'TrackerBundle')
            );

            if (null === $response = $event->getResponse()) {
                if ($currentUserEntity->hasRole('ROLE_ADMINISTRATOR')) {
                    $url = $this->generateUrl('_tracking_user_list');
                } else {
                    $url = $this->generateUrl('_tracking_user_profile');
                }
                $response = new RedirectResponse($url);
            }

            return $response;
        }

        return $this->render('TrackerBundle:User:edit.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
            'id' => $id
        ));
    }
}
