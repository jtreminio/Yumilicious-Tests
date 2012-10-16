<?php

namespace Yumilicious\UnitTests;

use Yumilicious\UnitTests\Base;
use Yumilicious\Domain;
use Yumilicious\Entity;
use Yumilicious\Validator;

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

        $this->setAttribute($domain, 'app', $this->app);

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
        $entityFlavor->setIsActive($isActiveStatus);

        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $updateReturn = true;
        $daoFlavor->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
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
        $domain = $this->getMockBuilder('\Yumilicious\Domain')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $entity1 = new Entity\FlavorDetail();
        $entity2 = new Entity\FlavorDetail();
        $entity3 = new Entity\FlavorDetail();
        $entity4 = new Entity\FlavorDetail();
        $entity5 = new Entity\FlavorDetail();
        $entity6 = new Entity\FlavorDetail();

        $entity1->setId(1);
        $entity2->setId(2);
        $entity3->setId(3);
        $entity4->setId(4);
        $entity5->setId(5);
        $entity6->setId(6);

        $entity1->setSlug('entity1');
        $entity2->setSlug('entity2');
        $entity3->setSlug('entity3');
        $entity4->setSlug('entity4');
        $entity5->setSlug('entity5');
        $entity6->setSlug('entity6');

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
        $children3 = $children1['entity3']->getChildren();

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
            $children1['entity3']->getId()
        );

        $this->assertEquals(
            $entity5->getId(),
            $children3['entity5']->getId()
        );
    }
}