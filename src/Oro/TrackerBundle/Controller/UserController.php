<?php

namespace Oro\TrackerBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;

class UserController extends Controller
{
    /**
     * @Route("/list", name="_tracking_user_list")
     * @Template()
     */
    public function listAction()
    {
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();

        return array('users' => $users);
    }

    /**
     * @Route("/edit/{id}", name="_tracking_user_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $formFactory = $this->get('fos_user.registration.form.factory');
        $form = $formFactory->createForm();

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array("id" => $id));
        $form->setData($user);

        return array('form' => $form->createView(), 'user' => $user, 'id' => $id);
    }

    /**
     * @Route("/update/{id}", name="_tracking_user_update")
     * @Template()
     */
    public function updateAction($id)
    {
        $request = $this->getRequest();
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserBy(array("id" => $id));
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
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
                'User has been updated!'
            );

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('_tracking_user_list');
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
