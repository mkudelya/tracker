<?php

namespace Oro\Bundle\TrackerBundle\Tests\Unit\Entity;

use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractEntityTestCase extends \PHPUnit_Framework_TestCase
{
    const TEST_ID = 123;

    /**
     * @var mixed
     */
    protected $entity;

    protected function setUp()
    {
        $name         = $this->getEntityFQCN();
        $this->entity = new $name();
    }

    public function tearDown()
    {
        unset($this->entity);
    }

    /**
     * @dataProvider  getSetDataProvider
     *
     * @param string $property
     * @param mixed  $value
     * @param mixed  $expected
     */
    public function testSetGet($property, $value = null, $expected = null)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $accessor->setValue($this->entity, $property, $value);
        $this->assertEquals($expected, $accessor->getValue($this->entity, $property));
    }

    /**
     * @return array
     */
    abstract public function getSetDataProvider();

    /**
     * @return string
     */
    abstract public function getEntityFQCN();
}
