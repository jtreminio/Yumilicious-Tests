<?php

namespace Yumilicious\IntegrationTests\Dao;

use Yumilicious\IntegrationTests\Base;

class LocationScheduleTest extends Base
{
    /**
     * @test
     * @covers \Yumilicious\Dao\LocationSchedule::update
     */
    public function updateCreatesNewRecord()
    {
        /** @var $daoLocationSchedule \Yumilicious\Dao\LocationSchedule */
        $daoLocationSchedule = $this->app['daoLocationSchedule'];

        /** @var $entity \Yumilicious\Entity\LocationSchedule */
        $entity = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->setMethods(null)
            ->getMock();

        $sampleData = $this->_createSampleData();

        $entity->hydrate($sampleData);

        $daoLocationSchedule->update($entity);

        $fetchedRecord = $daoLocationSchedule->get($entity->getLocationId());

        $this->assertEquals(
            $entity->getLocationId(),
            $fetchedRecord['locationId'],
            'Inserted record locatin Id does not match expected'
        );

        $this->assertEquals(
            $entity->getMonOpen(),
            $fetchedRecord['monOpen'],
            'Inserted record monday open time does not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Dao\LocationSchedule::update
     */
    public function updateUpdatesExistingRecord()
    {
        /** @var $daoLocationSchedule \Yumilicious\Dao\LocationSchedule */
        $daoLocationSchedule = $this->app['daoLocationSchedule'];

        /** @var $entity \Yumilicious\Entity\LocationSchedule */
        $entity = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->setMethods(null)
            ->getMock();

        $sampleData = $this->_createSampleData();

        $entity->hydrate($sampleData);

        $daoLocationSchedule->update($entity);

        $monClose = '19:00 ZM';
        $tuesClose = '33:18 IM';
        $entity->setMonClose($monClose);
        $entity->setTuesClose($tuesClose);

        $daoLocationSchedule->update($entity);

        $fetchedRecord = $daoLocationSchedule->get($entity->getLocationId());

        $this->assertEquals(
            $entity->getLocationId(),
            $fetchedRecord['locationId'],
            'Inserted record locatin Id does not match expected'
        );

        $this->assertEquals(
            $entity->getMonClose(),
            $fetchedRecord['monClose'],
            'Updated record Monday close does not match expected'
        );

        $this->assertEquals(
            $entity->getTuesClose(),
            $fetchedRecord['tuesClose'],
            'Updated record Tuesday close time does not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Dao\LocationSchedule::getMultiple
     */
    public function getMultipleReturnsCorrectResults()
    {
        /** @var $daoLocationSchedule \Yumilicious\Dao\LocationSchedule */
        $daoLocationSchedule = $this->app['daoLocationSchedule'];

        /** @var $entityOne \Yumilicious\Entity\LocationSchedule */
        $entityOne = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->setMethods(null)
            ->getMock();

        /** @var $entityTwo \Yumilicious\Entity\LocationSchedule */
        $entityTwo = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->setMethods(null)
            ->getMock();

        /** @var $entityThree \Yumilicious\Entity\LocationSchedule */
        $entityThree = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->setMethods(null)
            ->getMock();

        $sampleDataOne = $this->_createSampleData();
        $sampleDataTwo = $this->_createSampleData();
        $sampleDataThree = $this->_createSampleData();

        $entityOne->hydrate($sampleDataOne);
        $entityTwo->hydrate($sampleDataTwo);
        $entityThree->hydrate($sampleDataThree);

        $daoLocationSchedule->update($entityOne);
        $daoLocationSchedule->update($entityTwo);
        $daoLocationSchedule->update($entityThree);

        $locationIds = array(
            $sampleDataOne['locationId'],
            $sampleDataTwo['locationId'],
            $sampleDataThree['locationId'],
        );

        $keyedResults = array();

        foreach ($results = $daoLocationSchedule->getMultiple($locationIds) as $result) {
            $keyedResults[$result['locationId']]= $result;
        }

        $this->assertArrayHasKey(
            $sampleDataOne['locationId'],
            $keyedResults
        );

        $this->assertEquals(
            $entityOne->getLocationId(),
            $keyedResults[$sampleDataOne['locationId']]['locationId'],
            'Expected location Id not present'
        );

        $this->assertEquals(
            $entityOne->getMonOpen(),
            $keyedResults[$sampleDataOne['locationId']]['monOpen'],
            'Expected Monday open time not present'
        );

        $this->assertArrayHasKey(
            $sampleDataTwo['locationId'],
            $keyedResults
        );

        $this->assertEquals(
            $entityTwo->getLocationId(),
            $keyedResults[$sampleDataTwo['locationId']]['locationId'],
            'Expected location Id not present'
        );

        $this->assertEquals(
            $entityTwo->getWedOpen(),
            $keyedResults[$sampleDataTwo['locationId']]['wedOpen'],
            'Expected Wednesday open time not present'
        );

        $this->assertArrayHasKey(
            $sampleDataThree['locationId'],
            $keyedResults
        );

        $this->assertEquals(
            $entityThree->getLocationId(),
            $keyedResults[$sampleDataThree['locationId']]['locationId'],
            'Expected location Id not present'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Dao\LocationSchedule::createMultipleEmptySchedules
     */
    public function createMultipleEmptySchedulesCreatesAll()
    {
        /** @var $daoLocationSchedule \Yumilicious\Dao\LocationSchedule */
        $daoLocationSchedule = $this->app['daoLocationSchedule'];

        $locationIds = array(
            mt_rand(123, 999),
            mt_rand(123, 999),
            mt_rand(123, 999)
        );

        $this->assertTrue(
            $daoLocationSchedule->createMultipleEmptySchedules($locationIds),
            'Expecting true for success'
        );

        $expectedResultCount = count($locationIds);
        $results = $daoLocationSchedule->getMultiple($locationIds);

        $this->assertCount(
            $expectedResultCount,
            $results,
            'Expected result count for multiple inserted schedules did not match result'
        );
    }

    /**
     * Create sample schedule data
     *
     * @return array
     */
    protected function _createSampleData()
    {
        $amPm = array(
            'am', 'AM', 'Am', 'aM',
            'pm', 'PM', 'Pm', 'pM',
        );

        return array(
            'locationId' => mt_rand(123, 999),
            'monOpen'    => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
            'monClose'   => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
            'tuesOpen'   => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
            'tuesClose'  => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
            'wedOpen'    => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
            'wedClose'   => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
            'thursOpen'  => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
            'thursClose' => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
            'friOpen'    => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
            'friClose'   => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
            'satOpen'    => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
            'satClose'   => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
            'sunOpen'    => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
            'sunClose'   => mt_rand(01, 99).':'.mt_rand(01, 99).' '.$amPm[array_rand($amPm)],
        );
    }
}