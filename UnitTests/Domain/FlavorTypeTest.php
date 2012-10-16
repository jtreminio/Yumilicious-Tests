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
}