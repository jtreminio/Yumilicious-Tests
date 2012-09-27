<?php

namespace Yumilicious\UnitTests;

use Yumilicious\UnitTests\Base;

class DomainTest extends Base
{
    /**
     * @test
     */
    public function hydrateMultipleReturnsArrayOfEntities()
    {
        $domain = $this->getMockBuilder('\Yumilicious\Domain')
            ->disableOriginalConstructor()
            ->getMock();

        $entity = $this->getMockBuilder('\Yumilicious\Entity')
            ->disableOriginalConstructor()
            ->setMethods(array('hydrate'))
            ->getMockForAbstractClass();

        $resultsArray = array(
            array('Result one'),
            array('Result Two'),
            array('Result Three'),
        );

        $entityOneHydrateResult = array('Entity One');
        $entity->expects($this->at(0))
            ->method('hydrate')
            ->with($resultsArray[0])
            ->will($this->returnValue($entityOneHydrateResult));

        $entityTwoHydrateResult = array('Entity Two');
        $entity->expects($this->at(1))
            ->method('hydrate')
            ->with($resultsArray[1])
            ->will($this->returnValue($entityTwoHydrateResult));

        $entityThreeHydrateResult = array('Entity Three');
        $entity->expects($this->at(2))
            ->method('hydrate')
            ->with($resultsArray[2])
            ->will($this->returnValue($entityThreeHydrateResult));

        $entityName = 'entityLocation';
        $this->app[$entityName] = $entity;

        $this->setAttribute(
            $domain,
            'app',
            $this->app
        );

        $expectedEntityResults = array($entityOneHydrateResult, $entityTwoHydrateResult, $entityThreeHydrateResult);

        $results = $this->invokeMethod(
            $domain,
            'hydrateMultiple',
            array($entityName, $resultsArray)
        );

        $this->assertEquals(
            $expectedEntityResults,
            $results,
            'Resulting array of entities does not match expected'
        );
    }
}