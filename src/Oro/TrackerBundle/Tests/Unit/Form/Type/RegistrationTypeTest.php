<?php

namespace Oro\TrackerBundle\Tests\Unit\Form\Type;

use Oro\TrackerBundle\Form\RegistrationType;

class RegistrationTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var RegistrationType */
    protected $type;

    protected function setUp()
    {
        $mock = $this->getMockBuilder('\stdClass')
            ->setMethods(array('getToken', 'getUser', 'hasRole'))
            ->getMock();

        $mock->expects($this->any())->method('getToken')->will($this->returnSelf());
        $mock->expects($this->any())->method('getUser')->will($this->returnSelf());
        $mock->expects($this->any())->method('hasRole')->will($this->returnValue(true));

        $mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->getMock();
        $mockContainer->expects($this->any())
            ->method('get')
            ->with('security.context')
            ->will($this->returnValue($mock));

        $this->type = new RegistrationType($mockContainer);
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

        $builder->expects($this->exactly(3))->method('add');
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
        $this->assertEquals('tracker_user_registration', $this->type->getName());
    }

    public function testParentName()
    {
        $this->assertEquals('fos_user_registration', $this->type->getParent());
    }
}
