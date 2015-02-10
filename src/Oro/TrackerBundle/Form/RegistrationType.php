<?php

namespace Oro\TrackerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
