<?php

namespace Oro\TrackerBundle\Tests\Unit\Form\Type;

use Oro\TrackerBundle\Form\CommentType;

class CommentTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var CommentType */
    protected $type;

    protected function setUp()
    {
        $this->type = new CommentType();
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

        $builder->expects($this->exactly(2))->method('add');
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
        $this->assertEquals('tracker_comment', $this->type->getName());
    }
}
