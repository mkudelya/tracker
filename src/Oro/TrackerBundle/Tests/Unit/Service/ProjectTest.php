<?php

namespace Oro\TrackerBundle\Tests\Unit\Service;

use Oro\TrackerBundle\Service\Project;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    protected $service;
    protected $mockContainer;
    protected $mockDoctrine;

    protected function setUp()
    {
        //container
        $this->mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->getMock();

        //doctrine
        $this->mockDoctrine = $this->getMockBuilder('\stdClass')
            ->setMethods(array('getRepository', 'findAll'))
            ->getMock();

        $this->mockDoctrine->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->mockDoctrine));

        $this->mockDoctrine->expects($this->any())
            ->method('findAll')
            ->will($this->returnValue(array('test1', 'test2')));

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->with('doctrine')
            ->will($this->returnValue($this->mockDoctrine));

        $this->service = new Project($this->mockContainer);
    }

    public function testGetList()
    {
        $this->assertCount(2, $this->service->getList());
    }
}
