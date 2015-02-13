<?php

namespace Oro\TrackerBundle\Form;

use Oro\TrackerBundle\Controller\IssueController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IssueType extends AbstractType
{
    /**
     * Add/edit issue
     * @var int
     */
    protected $processMethod;

    protected $types = array(
        'bug' => 'Bug',
        'task' => 'Task'
    );

    protected $status = array(
        'open' => 'Open',
        'in progress' => 'In Progress',
        'closed' => 'Closed'
    );

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('summary');
        $builder->add('code');
        $builder->add('description');

        if ($this->getProcessMethod() == IssueController::IS_ADD_TASK) {
            $this->types['story'] = 'Story';
        }

        if ($this->getProcessMethod() == IssueController::IS_ADD_TASK ||
            $this->getProcessMethod() == IssueController::IS_ADD_SUBTASK) {
            $builder->add('type', 'choice', array(
                'choices' => $this->types,
                'required' => true
            ));
        }

        $builder->add('priority');
        $builder->add('status', 'choice', array(
            'choices'   => $this->status,
            'required'  => true
        ));
        $builder->add('assignee');
        $builder->add('Save', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Oro\TrackerBundle\Entity\Issue'
        ));
    }

    public function getName()
    {
        return 'tracker_issue';
    }

    public function setProcessMethod($method)
    {
        $this->processMethod = $method;
    }

    public function getProcessMethod()
    {
        return $this->processMethod;
    }
}
