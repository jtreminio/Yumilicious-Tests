<?php

namespace Yumilicious\UnitTests\Domain;

use Yumilicious\UnitTests\Base;
use Yumilicious\Domain;
use Yumilicious\Entity;
use Yumilicious\Validator;

class LocationScheduleTest extends Base
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDaoLocationSchedule()
    {
        return $this->getMockBuilder('\Yumilicious\Dao\LocationSchedule')
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEntityLocationSchedule()
    {
        return $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->setMethods(array('validate'))
            ->getMock();
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::getSchedule
     */
    public function getScheduleReturnsHydratedEntity()
    {
        /** @var $domainLocationSchedule Domain\LocationSchedule */
        $domainLocationSchedule = $this->app['domainLocationSchedule'];

        $daoLocationSchedule = $this->getDaoLocationSchedule();

        $getValue = false;
        $daoLocationSchedule->expects($this->once())
            ->method('get')
            ->will($this->returnValue($getValue));

        $locationId = 123;

        $createReturn = array('locationId' => $locationId);
        $daoLocationSchedule->expects($this->once())
            ->method('create')
            ->will($this->returnValue($createReturn));

        $this->app['daoLocationSchedule'] = $daoLocationSchedule;

        $this->setAttribute($domainLocationSchedule, 'app', $this->app);

        $result = $domainLocationSchedule->getSchedule($locationId);

        $this->assertEquals(
            $result->getLocationId(),
            $locationId,
            'LocationId did not match expected'
        );
    }
}