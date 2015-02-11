<?php

namespace Oro\TrackerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Oro\TrackerBundle\Entity\Role as Role;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roles = new Role();
        $builder->add('fullname');
        $builder->add('avatarFile');
        $builder->add('roles', 'choice', array(
            'choices'   => $roles->getAvailableRoles(),
            'multiple'  => true,
            'required'  => true
        ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $user = $event->getData();

            if ($user && $user->getId()) {
                $form = $event->getForm();
                $userRoles = $user->getRoles();

                $form->add('roles', 'choice', array(
                    'choices'   => (new Role)->getAvailableRoles(),
                    'data' => array_map('strtolower', $userRoles),
                    'multiple'  => true,
                    'required'  => true
                ));

                $form->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'required' => false,
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'form.password'),
                    'second_options' => array('label' => 'form.password_confirmation'),
                    'invalid_message' => 'fos_user.password.mismatch',
                ));
            }
        });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oro\TrackerBundle\Entity\User',
            'intention'  => 'registration',
        ));
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'tracker_user_registration';
    }
}
