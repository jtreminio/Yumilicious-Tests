<?php

namespace Yumilicious\UnitTests;

use Yumilicious\UnitTests\Base;

class DomainTest extends Base
{
    /**
     * @test
     * @covers \Yumilicious\Domain::validate
     */
    public function validateReturnsTrueOnPass()
    {
        $domain = $this->getMockBuilder('\Yumilicious\Domain')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $entity = $this->getMockBuilder('\Yumilicious\Entity')
            ->disableOriginalConstructor()
            ->getMock();

        $expectedErrors = array();

        $entity->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($expectedErrors));

        $this->assertTrue(
            $domain->validate($entity),
            'Method expected to return true on no error count'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain::validate
     */
    public function validateReturnsExceptionOnFailure()
    {
        $errorOnePropertyPath = 'Property path 1';
        $errorOneMessage = 'Message 1';

        $errorTwoPropertyPath = 'Property path 2';
        $errorTwoMessage = 'Message 2';

        $expectedExceptionMessage =
            "{$errorOnePropertyPath} - {$errorOneMessage}<br />" .
            "{$errorTwoPropertyPath} - {$errorTwoMessage}<br />";

        $this->setExpectedException(
            '\Yumilicious\Exception\Domain',
            $expectedExceptionMessage
        );

        $domain = $this->getMockBuilder('\Yumilicious\Domain')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $entity = $this->getMockBuilder('\Yumilicious\Entity')
            ->disableOriginalConstructor()
            ->getMock();

        $errorOne = $this->getMockBuilder('\stdClass')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'getPropertyPath',
                    'getMessage',
                )
            )
            ->getMock();

        $errorTwo = $this->getMockBuilder('\stdClass')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'getPropertyPath',
                    'getMessage',
                )
            )
            ->getMock();

        $errorOne->expects($this->once())
            ->method('getPropertyPath')
            ->will($this->returnValue($errorOnePropertyPath));

        $errorOne->expects($this->once())
            ->method('getMessage')
            ->will($this->returnValue($errorOneMessage));

        $errorTwo->expects($this->once())
            ->method('getPropertyPath')
            ->will($this->returnValue($errorTwoPropertyPath));

        $errorTwo->expects($this->once())
            ->method('getMessage')
            ->will($this->returnValue($errorTwoMessage));

        $expectedErrors = array(
            $errorOne,
            $errorTwo
        );

        $entity->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($expectedErrors));

        $domain->validate($entity);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain::hydrateMultiple
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