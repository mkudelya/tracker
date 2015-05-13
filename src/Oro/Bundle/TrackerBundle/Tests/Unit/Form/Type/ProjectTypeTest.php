<?php

namespace Oro\Bundle\TrackerBundle\Tests\Unit\Form\Type;

use Oro\Bundle\TrackerBundle\Form\ProjectType;

class ProjectTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProjectType */
    protected $type;

    protected function setUp()
    {
        $this->type = new ProjectType();
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

        $builder->expects($this->exactly(5))->method('add');
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
        $this->assertEquals('tracker_project', $this->type->getName());
    }
}
