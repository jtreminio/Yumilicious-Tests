<?php

namespace Yumilicious\UnitTests;

use Yumilicious\UnitTests\Base;
use Yumilicious\Domain;
use Yumilicious\Entity;
use Yumilicious\Validator;

class DomainTest extends Base
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDomain()
    {
        return $this->getMockBuilder('\Yumilicious\Domain')
            ->setConstructorArgs(array($this->app))
            ->getMockForAbstractClass();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEntity()
    {
        return $this->getMockBuilder('\Yumilicious\Entity')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     * @covers \Yumilicious\Domain::validate
     */
    public function validateReturnsTrueOnPass()
    {
        $domain = $this->getDomain();
        $entity = $this->getEntity();

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

        $domain = $this->getDomain();
        $entity = $this->getEntity();

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
        $domain = $this->getDomain();
        $entity = $this->getEntity();

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
        $this->setService($entityName, $entity);

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

    /**
     * @test
     * @dataProvider providerToggleActivationReturnsExpected
     * @covers \Yumilicious\Domain::toggleActivation
     */
    public function toggleActivationReturnsExpected(
        $isActiveStatus,
        $expectedIsActiveResult
    ){
        $entityFlavor = new \Yumilicious\Entity\Flavor();
        $daoFlavor    = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $entityFlavor->setIsActive($isActiveStatus);

        $updateReturn = true;
        $daoFlavor->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

        $result = $domainFlavor->toggleActivation($entityFlavor);

        $this->assertEquals(
            $expectedIsActiveResult,
            $result->getIsActive()
        );
    }

    /**
     * Provider for toggleActivationReturnsExpected()
     *
     * @return array
     */
    public function providerToggleActivationReturnsExpected()
    {
        return array(
            array(1, 0),
            array(0, 1),
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain::buildTree
     */
    public function buildTreeReturnsExpected()
    {
        $domain = $this->getDomain();

        $entity1 = new Entity\FlavorType();
        $entity2 = new Entity\FlavorType();
        $entity3 = new Entity\FlavorType();
        $entity4 = new Entity\FlavorType();
        $entity5 = new Entity\FlavorType();
        $entity6 = new Entity\FlavorType();

        $entity1->setId(1);
        $entity2->setId(2);
        $entity3->setId(3);
        $entity4->setId(4);
        $entity5->setId(5);
        $entity6->setId(6);

        $entity1->setName('entity1');
        $entity2->setName('entity2');
        $entity3->setName('entity3');
        $entity4->setName('entity4');
        $entity5->setName('entity5');
        $entity6->setName('entity6');

        $entity3->setParentId(1);
        $entity4->setParentId(2);
        $entity5->setParentId(3);
        $entity6->setParentId(2);

        $elements = array(
            $entity1,
            $entity2,
            $entity3,
            $entity4,
            $entity5,
            $entity6,
        );

        $result = $this->invokeMethod(
            $domain,
            'buildTree',
            array($elements)
        );

        // entity1
        $children1 = $result[0]->getChildren();
        // entity2
        $children2 = $result[1]->getChildren();
        // entity3
        $children3 = $children1['3']->getChildren();

        $this->assertEquals(
            $entity1->getId(),
            $result[0]->getId()
        );

        $this->assertEquals(
            $entity2->getId(),
            $result[1]->getId()
        );

        $this->assertEquals(
            $entity3->getId(),
            $children1['3']->getId()
        );

        $this->assertEquals(
            $entity5->getId(),
            $children3['5']->getId()
        );
    }
}