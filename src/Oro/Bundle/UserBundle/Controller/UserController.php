<?php

namespace Oro\Bundle\UserBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use FOS\UserBundle\Event\FormEvent;

use Oro\Bundle\UserBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/list", name="_oro_user_list")
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
     * @Route("/show/{username}", name="_oro_user_profile", defaults={"username" = null})
     * @ParamConverter("user")
     * @Template()
     * @param User $user
     * @return array
     */
    public function showAction(User $user = null)
    {
        $currentUser = $this->get('security.context')->getToken()->getUser();

        if (!$user) {
            $user = $currentUser;
        }

        return array('user' => $user, 'currentUser' => $currentUser);
    }

    /**
     * @Route("/edit/{id}", name="_oro_user_edit")
     * @ParamConverter("user")
     * @Template()
     * @param User $user
     * @return array
     */
    public function editAction(User $user)
    {
        $currentUserEntity = $this->get('security.context')->getToken()->getUser();

        $formFactory = $this->get('fos_user.registration.form.factory');
        $form = $formFactory->createForm();

        if ($user === null) {
            throw new NotFoundHttpException(
                $this->get('translator')->trans('layout.sorry_not_existing', array(), 'OroTrackerBundle')
            );
        }

        if (!$currentUserEntity->hasRole('ROLE_ADMINISTRATOR') && $currentUserEntity->getId() !== $user->getId()) {
            throw new AccessDeniedException(
                $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
            );
        }

        $form->setData($user);

        return array('form' => $form->createView(), 'user' => $user);
    }

    /**
     * @Route("/update/{id}", name="_oro_user_update")
     * @ParamConverter("user")
     * @Template()
     * @param User $user
     * @return mixed
     */
    public function updateAction(User $user)
    {
        $currentUserEntity = $this->get('security.context')->getToken()->getUser();

        $request = $this->getRequest();
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $user->setEnabled(true);

        if (!$currentUserEntity->hasRole('ROLE_ADMINISTRATOR') && $currentUserEntity->getId() !== $user->getId()) {
            throw new AccessDeniedException(
                $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
            );
        }

        $requestParams = $request->request->get('fos_user_registration_form');
        if (!$currentUserEntity->hasRole('ROLE_ADMINISTRATOR') && isset($requestParams['roles'])) {
            throw new AccessDeniedException(
                $this->get('translator')->trans('layout.unauthorised_access', array(), 'OroTrackerBundle')
            );
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
                $this->get('translator')->trans('flash.update.user', array(), 'OroTrackerBundle')
            );

            if (null === $response = $event->getResponse()) {
                if ($currentUserEntity->hasRole('ROLE_ADMINISTRATOR')) {
                    $url = $this->generateUrl('_oro_user_list');
                } else {
                    $url = $this->generateUrl('_oro_user_profile');
                }
                $response = new RedirectResponse($url);
            }

            return $response;
        }

        return $this->render(
            'OroUserBundle:User:edit.html.twig',
            array(
                'form' => $form->createView(),
                'user' => $user
            )
        );
    }
}
