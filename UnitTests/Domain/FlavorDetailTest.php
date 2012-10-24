<?php

namespace Yumilicious\UnitTests\Domain;

use Yumilicious\UnitTests\Base;
use Yumilicious\Domain;
use Yumilicious\Entity;
use Yumilicious\Validator;

class FlavorDetailTest extends Base
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDaoFlavorDetail()
    {
        return $this->getMockBuilder('\Yumilicious\Dao\FlavorDetail')
            ->getMock();
    }

    /**
     * @return Domain\FlavorDetail|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDomainFlavorDetail()
    {
        return $this->getMockBuilder('\Yumilicious\Domain\FlavorDetail')
            ->setConstructorArgs(array($this->app))
            ->setMethods(null)
            ->getMock();
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorDetail::createMultipleFromArray
     * @covers \Yumilicious\Domain\FlavorDetail::getMultipleDetailsEntitiesFromArray
     */
    public function createMultipleFromArrayReturnsFalseOnEmptyArrayParameter()
    {
        $domainFlavorDetail = $this->getDomainFlavorDetail();

        $flavorId = 123;
        $dataset = array();

        $this->assertFalse(
            $domainFlavorDetail->createMultipleFromArray($flavorId, $dataset),
            'Expected ::createMultipleFromArray() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorDetail::createMultipleFromArray
     * @covers \Yumilicious\Domain\FlavorDetail::createMultipleContainsTypeEntities
     * @covers \Yumilicious\Domain\FlavorDetail::isTypeArray
     */
    public function createMultipleFromArrayReturnsArrayOfEntities()
    {
        $domainFlavorDetail = $this->getDomainFlavorDetail();
        $daoFlavorDetail = $this->getDaoFlavorDetail();

        $daoFlavorDetail->expects($this->once())
            ->method('createMultipleDetails');

        $this->setService('daoFlavorDetail', $daoFlavorDetail);

        $flavorId = 123;
        $dataset = array(
            'glutenFree'  => 1,
            'nonFat'      => 1,
            'vegetarian'  => 1,
            'calories'    => 150,
            'description' => 'test description',
            'contains'    => 'contains 1,contains 2, contains 3',
        );

        $result = $domainFlavorDetail->createMultipleFromArray($flavorId, $dataset);

        $expectedCount = 8;
        $this->assertCount(
            $expectedCount,
            $result,
            'Expected result to contain 8 keys'
        );

        $this->assertTrue(
            array_key_exists($result[0]->getName(), $dataset),
            'Expected first result key getName() to exist in $dataset'
        );

        $this->assertTrue(
            array_key_exists($result[4]->getName(), $dataset),
            'Expected fourth result key getName() to exist in $dataset'
        );
    }
}