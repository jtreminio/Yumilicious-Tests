<?php

namespace Yumilicious\UnitTests\Domain;

use Yumilicious\UnitTests\Base;
use Yumilicious\Domain;
use Yumilicious\Entity;
use Yumilicious\Validator;

class FlavorTypeTest extends Base
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDaoFlavorType()
    {
        return $this->getMockBuilder('\Yumilicious\Dao\FlavorType')
            ->getMock();
    }

    /**
     * @return Domain\FlavorType|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDomainFlavorType()
    {
        return $this->getMockBuilder('\Yumilicious\Domain\FlavorType')
            ->setConstructorArgs(array($this->app))
            ->setMethods(null)
            ->getMock();
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::create
     */
    public function createThrowsExceptionOnParentNotExist()
    {
        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            'Selected flavor type parent does not exist'
        );

        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $dataset = array('parentId' => 123);

        $getOneByIdReturn = false;
        $daoFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($dataset['parentId'])
            ->will($this->returnValue($getOneByIdReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $domainFlavorType->create($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::create
     */
    public function createThrowsExceptionOnNameExists()
    {
        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            'Selected flavor type name is already in use'
        );

        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $dataset = array(
            'parentId' => 123,
            'name'     => 'test name'
        );

        $getOneByIdReturn = true;
        $daoFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($dataset['parentId'])
            ->will($this->returnValue($getOneByIdReturn));

        $getOneByNameReturn = true;
        $daoFlavorType->expects($this->once())
            ->method('getOneByName')
            ->with($dataset['name'])
            ->will($this->returnValue($getOneByNameReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $domainFlavorType->create($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::create
     */
    public function createThrowsExceptionOnValidateFailure()
    {
        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            'updatedBy - This value should not be blank.<br />'
        );

        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $dataset = array(
            'parentId' => 123,
            'name'     => 'test name'
        );

        $getOneByIdReturn = true;
        $daoFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($dataset['parentId'])
            ->will($this->returnValue($getOneByIdReturn));

        $getOneByNameReturn = false;
        $daoFlavorType->expects($this->once())
            ->method('getOneByName')
            ->with($dataset['name'])
            ->will($this->returnValue($getOneByNameReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $domainFlavorType->create($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::create
     */
    public function createReturnsFalseOnCreateFailure()
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $dataset = array(
            'parentId' => 123,
            'name'     => 'test name',
            'updatedBy' => 321,
        );

        $getOneByIdReturn = true;
        $daoFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($dataset['parentId'])
            ->will($this->returnValue($getOneByIdReturn));

        $getOneByNameReturn = false;
        $daoFlavorType->expects($this->once())
            ->method('getOneByName')
            ->with($dataset['name'])
            ->will($this->returnValue($getOneByNameReturn));

        $createReturn = false;
        $daoFlavorType->expects($this->once())
            ->method('create')
            ->will($this->returnValue($createReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $this->assertFalse(
            $domainFlavorType->create($dataset),
            'Expected ::create() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::create
     */
    public function createReturnsEntity()
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $dataset = array(
            'parentId' => 123,
            'name'     => 'test name',
            'updatedBy' => 321,
        );

        $getOneByIdReturn = true;
        $daoFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($dataset['parentId'])
            ->will($this->returnValue($getOneByIdReturn));

        $getOneByNameReturn = false;
        $daoFlavorType->expects($this->once())
            ->method('getOneByName')
            ->with($dataset['name'])
            ->will($this->returnValue($getOneByNameReturn));

        $createReturn = 456;
        $daoFlavorType->expects($this->once())
            ->method('create')
            ->will($this->returnValue($createReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $result = $domainFlavorType->create($dataset);

        $this->assertEquals(
            $createReturn,
            $result->getId(),
            'Expected entity getId to match'
        );

        $this->assertEquals(
            $dataset['name'],
            $result->getName(),
            'Expected entity getId to match'
        );
    }
}