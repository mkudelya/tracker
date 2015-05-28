<?php

namespace Oro\Bundle\TrackerBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Oro\Bundle\TrackerBundle\Controller\IssueController;
use Oro\Bundle\TrackerBundle\Entity\Project;

class IssueType extends AbstractType
{
    /**
     * @var integer
     */
    protected $processMethod;

    /**
     * @var array
     */
    protected $types = array(
        'Bug' => 'Bug',
        'Task' => 'Task'
    );

    /**
     * @var array
     */
    protected $status = array(
        'Open' => 'Open',
        'In Progress' => 'In Progress',
        'Closed' => 'Closed'
    );

    /**
     * @var array
     */
    protected $resolution = array(
        '' => '',
        'Fixed' => 'Fixed',
        'Won\'t Fix' => 'Won\'t Fix',
        'Duplicate' => 'Duplicate',
        'Incomplete' => 'Incomplete',
        'Cannot Reproduce' => 'Cannot Reproduce',
        'Done' => 'Done',
        'Won\'t Do' => 'Won\'t Do'
    );

    /**
     * @var Project
     */
    protected $project;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('summary');
        $builder->add('code');
        $builder->add('description');

        if (!$this->getProcessMethod()) {
            throw new LogicException('Please call setProcessMethod first');
        }

        if ($this->getProcessMethod() == IssueController::IS_ADD_TASK) {
            $this->types['story'] = 'Story';
        }

        if ($this->getProcessMethod() == IssueController::IS_ADD_TASK ||
            $this->getProcessMethod() == IssueController::IS_ADD_SUBTASK) {
            $builder->add(
                'type',
                'choice',
                array(
                    'choices' => $this->types,
                    'required' => true
                )
            );
        }

        $builder->add('priority');
        $builder->add(
            'status',
            'choice',
            array(
                'choices'   => $this->status,
                'required'  => true
            )
        );

        $builder->add(
            'resolution',
            'choice',
            array(
                'choices'   => $this->resolution,
                'required'  => false
            )
        );

        $project = $this->project;

        $builder->add(
            'assignee',
            'entity',
            array(
                'class' => 'Oro\Bundle\UserBundle\Entity\User',
                'query_builder' => function (EntityRepository $repository) use ($project) {
                    return $repository
                        ->createQueryBuilder('u')
                        ->join('u.projects', 'p')
                        ->where('p = :project')
                        ->setParameter('project', $project);
                }
            )
        );

        $builder->add('Save', 'submit');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'Oro\Bundle\TrackerBundle\Entity\Issue'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tracker_issue';
    }

    /**
     * @param integer $method
     */
    public function setProcessMethod($method)
    {
        $this->processMethod = $method;
    }

    /**
     * @return integer
     */
    public function getProcessMethod()
    {
        return $this->processMethod;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
    }
}
