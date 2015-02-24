<?php

namespace Oro\TrackerBundle\Tests\Unit\Form\Type;

use Oro\TrackerBundle\Controller\IssueController;
use Oro\TrackerBundle\Form\IssueType;

class IssueTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var IssueType */
    protected $type;

    protected function setUp()
    {
        $this->type = new IssueType();
    }

    protected function tearDown()
    {
        unset($this->type);
    }

    public function testFields()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->exactly(9))->method('add');
        $this->type->setProcessMethod(IssueController::IS_ADD_TASK);
        $this->type->buildForm($builder, []);
    }

    public function testSetDefaultOptions()
    {
        $resolver = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolverInterface');
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with($this->isType('array'));
        $this->type->setDefaultOptions($resolver);
    }

    public function testHasName()
    {
        $this->assertEquals('tracker_issue', $this->type->getName());
    }
}
