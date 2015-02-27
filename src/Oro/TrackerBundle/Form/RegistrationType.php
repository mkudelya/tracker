<?php

namespace Oro\TrackerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use Oro\TrackerBundle\Entity\Role as Role;
use Oro\TrackerBundle\Entity\User;

class RegistrationType extends AbstractType
{
    /**
     * @var array
     */
    protected $timezones = array (
        'America/Los_Angeles' => 'America - Los Angeles',
        'Europe/Kiev' => 'Ukraine - Kiev',
        'Australia/Sydney' => 'Australia - Sydney',
    );

    /**
     * @var Container
     */
    protected $container = null;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isAdmin = false;

        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user instanceof User) {
            $isAdmin = $user->hasRole(Role::ROLE_ADMINISTRATOR);
        }

        $builder->add('fullname');
        $builder->add(
            'timezone',
            'choice',
            array(
                'choices' => $this->timezones,
                'multiple' => false,
                'required' => true
            )
        );
        $builder->add('avatarFile');

        if ($isAdmin) {
            $builder->add(
                'roles',
                'choice',
                array(
                    'choices' => (new Role())->getAvailableRoles(),
                    'multiple' => true,
                    'required' => true
                )
            );
        }

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($isAdmin) {
            $user = $event->getData();

                if ($user && $user->getId()) {
                    $form = $event->getForm();
                    $userRoles = $user->getRoles();

                    if ($isAdmin) {
                        $form->add(
                            'roles',
                            'choice',
                            array(
                                'choices' => (new Role())->getAvailableRoles(),
                                'data' => $userRoles,
                                'multiple' => true,
                                'required' => true
                            )
                        );
                    }

                    $form->add(
                        'plainPassword',
                        'repeated',
                        array(
                            'type' => 'password',
                            'required' => false,
                            'options' => array('translation_domain' => 'FOSUserBundle'),
                            'first_options' => array('label' => 'form.password'),
                            'second_options' => array('label' => 'form.password_confirmation'),
                            'invalid_message' => 'fos_user.password.mismatch',
                       )
                    );
                }
            }
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Oro\TrackerBundle\Entity\User',
                'intention'  => 'registration',
            )
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'fos_user_registration';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tracker_user_registration';
    }
}
