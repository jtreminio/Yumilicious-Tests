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
     * @covers \Yumilicious\Domain\LocationSchedule::getSchedule
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

    /**
     * @test
     * @covers \Yumilicious\Domain\LocationSchedule::getMultipleSchedules
     */
    public function getMultipleSchedulesReturnsFalseOnNoArray()
    {
        /** @var $domainLocationSchedule Domain\LocationSchedule */
        $domainLocationSchedule = $this->app['domainLocationSchedule'];

        $this->setAttribute($domainLocationSchedule, 'app', $this->app);

        $locationIds = false;

        $this->assertFalse(
            $domainLocationSchedule->getMultipleSchedules($locationIds),
            '::getMultipleSchedules() expected to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\LocationSchedule::getMultipleSchedules
     */
    public function getMultipleSchedulesReturnsMultipleEntities()
    {
        /** @var $domainLocationSchedule Domain\LocationSchedule */
        $domainLocationSchedule = $this->app['domainLocationSchedule'];

        $daoLocationSchedule = $this->getDaoLocationSchedule();

        $locationIds = array(123, 456, 789);

        $multipleSchedules = array(
            array('locationId' => 123),
            array('locationId' => 456),
            array('locationId' => 789),
        );

        $daoLocationSchedule->expects($this->once())
            ->method('getMultiple')
            ->with($locationIds)
            ->will($this->returnValue($multipleSchedules));

        $this->app['daoLocationSchedule'] = $daoLocationSchedule;
        $this->setAttribute($domainLocationSchedule, 'app', $this->app);

        $result = $domainLocationSchedule->getMultipleSchedules($locationIds);

        $this->assertEquals(
            $multipleSchedules[0]['locationId'],
            $result[0]->getLocationId(),
            'LocationId did not match expected'
        );

        $this->assertEquals(
            $multipleSchedules[1]['locationId'],
            $result[1]->getLocationId(),
            'LocationId did not match expected'
        );

        $this->assertEquals(
            $multipleSchedules[2]['locationId'],
            $result[2]->getLocationId(),
            'LocationId did not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\LocationSchedule::update
     */
    public function updateReturnsFalseOnFailure()
    {
        /** @var $domainLocationSchedule Domain\LocationSchedule */
        $domainLocationSchedule = $this->app['domainLocationSchedule'];

        $daoLocationSchedule = $this->getDaoLocationSchedule();

        $entityLocationSchedule = new Entity\LocationSchedule();

        $updateResult = false;
        $daoLocationSchedule->expects($this->once())
            ->method('update')
            ->with($entityLocationSchedule)
            ->will($this->returnValue($updateResult));

        $this->app['daoLocationSchedule'] = $daoLocationSchedule;
        $this->setAttribute($domainLocationSchedule, 'app', $this->app);

        $this->assertFalse(
            $domainLocationSchedule->update($entityLocationSchedule),
            'Expected ::update() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\LocationSchedule::update
     */
    public function updateReturnsEntity()
    {
        /** @var $domainLocationSchedule Domain\LocationSchedule */
        $domainLocationSchedule = $this->app['domainLocationSchedule'];

        $daoLocationSchedule = $this->getDaoLocationSchedule();

        $entityLocationSchedule = new Entity\LocationSchedule();
        $entityLocationSchedule->setLocationId(123);

        $updateResult = true;
        $daoLocationSchedule->expects($this->once())
            ->method('update')
            ->with($entityLocationSchedule)
            ->will($this->returnValue($updateResult));

        $this->app['daoLocationSchedule'] = $daoLocationSchedule;
        $this->setAttribute($domainLocationSchedule, 'app', $this->app);

        $result = $domainLocationSchedule->update($entityLocationSchedule);

        $this->assertEquals(
            $entityLocationSchedule->getLocationId(),
            $result->getLocationId(),
            'Entity locationIds do not match'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\LocationSchedule::updateFromArray
     */
    public function updateFromArrayThrowsExceptionOnFailure()
    {
        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            'locationId - This value should be 1 or more.<br />'
        );

        /** @var $domainLocationSchedule Domain\LocationSchedule */
        $domainLocationSchedule = $this->app['domainLocationSchedule'];

        $this->setAttribute($domainLocationSchedule, 'app', $this->app);

        $dataset = array('locationId' => 0);

        $domainLocationSchedule->updateFromArray($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\LocationSchedule::updateFromArray
     */
    public function updateFromArrayReturnsEntity()
    {
        /** @var $domainLocationSchedule Domain\LocationSchedule */
        $domainLocationSchedule = $this->app['domainLocationSchedule'];
        $daoLocationSchedule = $this->getDaoLocationSchedule();

        $updateValue = true;
        $daoLocationSchedule->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateValue));

        $this->app['daoLocationSchedule'] = $daoLocationSchedule;
        $this->setAttribute($domainLocationSchedule, 'app', $this->app);

        $dataset = array('locationId' => 123);

        $result = $domainLocationSchedule->updateFromArray($dataset);

        $this->assertEquals(
            $dataset['locationId'],
            $result->getLocationId(),
            'LocationIds do not match'
        );
    }
}