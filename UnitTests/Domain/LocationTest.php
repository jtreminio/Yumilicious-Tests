<?php

namespace Yumilicious\UnitTests\Domain;

use Yumilicious\UnitTests\Base;
use Yumilicious\Domain\Location;

class LocationTest extends Base
{
    /**
     * @test
     * @covers \Yumilicious\Domain\Location::addLocation
     */
    public function addLocationSetsCreatedAtIfEmpty()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'getDateTime',
                    'validate',
                )
            )
            ->getMock();

        $daoLocation = $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->disableOriginalConstructor()
            ->getMock();

        $dateTime = new \DateTime();

        $domainLocation->expects($this->once())
            ->method('getDateTime')
            ->will($this->returnValue($dateTime));

        $validatePasses = true;
        $domainLocation->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validatePasses));

        $lastInsertId = 15;
        $daoLocation->expects($this->once())
            ->method('create')
            ->will($this->returnValue($lastInsertId));

        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $dataSet = array(
            'name' => 'test name',
        );

        $result = $domainLocation->addLocation($dataSet);

        $this->assertEquals(
            $dateTime->format('Y-m-d H:i:s'),
            $result->getCreatedAt(),
            'getCreatedAt() was not called correctly'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::addLocation
     */
    public function addLocationThrowsExceptionOnEntityValidationFailure()
    {
        $errorPropertyPath = 'My property path';
        $errorMessage = 'My error message';

        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            "{$errorPropertyPath} - {$errorMessage}<br />"
        );

        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $entityLocation = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->getMock();

        $errorClass = $this->getMockBuilder('\stdClass')
            ->setMethods(
                array(
                    'getPropertyPath',
                    'getMessage',
                )
            )
            ->getMock();

        $errorClass->expects($this->once())
            ->method('getPropertyPath')
            ->will($this->returnValue($errorPropertyPath));

        $errorClass->expects($this->once())
            ->method('getMessage')
            ->will($this->returnValue($errorMessage));

        $entityLocation->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(array($errorClass)));

        $this->app['entityLocation'] = $entityLocation;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $dataSet = array();

        $domainLocation->addLocation($dataSet);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::addLocation
     */
    public function addLocationReturnsEntityOnSuccessfulCreation()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'getDateTime',
                    'validate',
                )
            )
            ->getMock();

        $daoLocation = $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->disableOriginalConstructor()
            ->getMock();

        $dateTime = new \DateTime();

        $domainLocation->expects($this->once())
            ->method('getDateTime')
            ->will($this->returnValue($dateTime));

        $validatePasses = true;
        $domainLocation->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validatePasses));

        $lastInsertId = 15;
        $daoLocation->expects($this->once())
            ->method('create')
            ->will($this->returnValue($lastInsertId));

        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $dataSet = array(
            'name' => 'test name',
        );

        $result = $domainLocation->addLocation($dataSet);

        $this->assertEquals(
            $dataSet['name'],
            $result->getName(),
            'Expecting result to be hydrated Entity\Location'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::addLocation
     */
    public function addLocationReturnsFalseOnFailedCreateCall()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('validate',))
            ->getMock();

        $daoLocation = $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->disableOriginalConstructor()
            ->getMock();

        $validatePasses = true;
        $domainLocation->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validatePasses));

        $lastInsertId = false;
        $daoLocation->expects($this->once())
            ->method('create')
            ->will($this->returnValue($lastInsertId));

        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $dataSet = array(
            'name' => 'test name',
        );

        $this->assertFalse(
            $domainLocation->addLocation($dataSet),
            'Expecting result to be false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::getOneById
     */
    public function getOneByIdReturnsResults()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $domainLocationSchedule = $this->getMockBuilder('\Yumilicious\Domain\LocationSchedule')
            ->disableOriginalConstructor()
            ->getMock();

        $entityLocationSchedule = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->disableOriginalConstructor()
            ->getMock();

        $daoLocation = $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->disableOriginalConstructor()
            ->getMock();

        $id = 1;
        $getOneByIdResult = array(
            'id'       => 123,
            'name'     => 'test name',
            'subTitle' => 'test subtitle',
        );

        $daoLocation->expects($this->once())
            ->method('getOneById')
            ->with($id)
            ->will($this->returnValue($getOneByIdResult));

        $domainLocationSchedule->expects($this->once())
            ->method('getSchedule')
            ->with($id)
            ->will($this->returnValue($entityLocationSchedule));

        $this->app['domainLocationSchedule'] = $domainLocationSchedule;
        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $result = $domainLocation->getOneById($id);

        $this->assertEquals(
            $getOneByIdResult['id'],
            $result->getId(),
            'Expected an entity result'
        );

        $this->assertTrue(
            is_a($result->getSchedule(), '\Yumilicious\Entity\LocationSchedule'),
            'Expecting location key to contain Entity\LocationSchedule'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::getOneById
     */
    public function getOneByIdReturnsFalseOnNoResult()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $daoLocation = $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->disableOriginalConstructor()
            ->getMock();

        $entityLocation = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->getMock();

        $id = 1;
        $getOneByIdResult = false;

        $daoLocation->expects($this->once())
            ->method('getOneById')
            ->with($id)
            ->will($this->returnValue($getOneByIdResult));

        $entityLocation->expects($this->never())
            ->method('hydrate');

        $this->app['entityLocation'] = $entityLocation;
        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $this->assertFalse(
            $domainLocation->getOneById($id),
            'Expected return of false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::getAll
     */
    public function getAllReturnsResults()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'hydrateMultiple',
                    'getMultipleSchedules',
                    'getLocationIdsFromEntities',
                    'mapMultipleSchedulesToLocations',
                )
            )
            ->getMock();

        $domainLocationSchedule = $this->getMockBuilder('\Yumilicious\Domain\LocationSchedule')
            ->disableOriginalConstructor()
            ->getMock();

        $daoLocation = $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->disableOriginalConstructor()
            ->getMock();

        $getAllResult = array('non-empty array');
        $daoLocation->expects($this->once())
            ->method('getAllActive')
            ->will($this->returnValue($getAllResult));

        $entityName = 'entityLocation';
        $locations = array('non-empty array');
        $domainLocation->expects($this->once())
            ->method('hydrateMultiple')
            ->with($entityName, $getAllResult)
            ->will($this->returnValue($locations));

        $locationIds = array(1, 2, 3);
        $domainLocation->expects($this->once())
            ->method('getLocationIdsFromEntities')
            ->with($locations)
            ->will($this->returnValue($locationIds));

        $schedules = array(
            'schedule1',
            'schedule2',
            'schedule3',
        );
        $domainLocationSchedule->expects($this->once())
            ->method('getMultipleSchedules')
            ->with($locationIds)
            ->will($this->returnValue($schedules));

        $expectedLocations = array(100, 200, 300);
        $domainLocation->expects($this->once())
            ->method('mapMultipleSchedulesToLocations')
            ->with($locations, $schedules)
            ->will($this->returnValue($expectedLocations));

        $this->app['domainLocationSchedule'] = $domainLocationSchedule;
        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $this->assertEquals(
            $expectedLocations,
            $domainLocation->getAll(),
            'Expected return of an array'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::separateIntoStates
     */
    public function separateIntoStatesReturnsCorrectlyShapedArray()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $daoState = $this->getMockBuilder('\Yumilicious\Dao\State')
            ->disableOriginalConstructor()
            ->getMock();

        $locationOne = $this->getMockBuilder('\stdClass')
            ->disableOriginalConstructor()
            ->setMethods(array('getState'))
            ->getMock();

        $locationTwo = $this->getMockBuilder('\stdClass')
            ->disableOriginalConstructor()
            ->setMethods(array('getState'))
            ->getMock();

        $locationThree = $this->getMockBuilder('\stdClass')
            ->disableOriginalConstructor()
            ->setMethods(array('getState'))
            ->getMock();

        $states = array(
            'AK' => 'Alaska',
            'AL' => 'Alabama',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
        );

        $daoState->expects($this->once())
            ->method('getStates')
            ->will($this->returnValue($states));

        $locationOneState = 'AL';
        $locationOne->expects($this->once())
            ->method('getState')
            ->will($this->returnValue($locationOneState));

        $locationTwoState = 'CT';
        $locationTwo->expects($this->once())
            ->method('getState')
            ->will($this->returnValue($locationTwoState));

        $locationThreeState = 'AL';
        $locationThree->expects($this->once())
            ->method('getState')
            ->will($this->returnValue($locationThreeState));

        $this->app['daoState'] = $daoState;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $arrayOfLocations = array($locationOne, $locationTwo, $locationThree);

        $results = $domainLocation->separateIntoStates($arrayOfLocations);

        $expectedAlabamaCount = 2;
        $this->assertCount(
            $expectedAlabamaCount,
            $results['Alabama']
        );

        $expectedConnecticutCount = 1;
        $this->assertCount(
            $expectedConnecticutCount,
            $results['Connecticut']
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::mapMultipleSchedulesToLocations
     */
    public function mapMultipleSchedulesToLocationsReturnsAllLocationsWithSchedules()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $locationOne = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $locationTwo = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $locationThree = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $scheduleOne = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $scheduleTwo = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $scheduleThree = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $domainLocationSchedule = $this->getMockBuilder('\Yumilicious\Domain\LocationSchedule')
            ->disableOriginalConstructor()
            ->getMock();

        $locationIdOne = 123;
        $locationOne->setId($locationIdOne);
        $scheduleOne->setLocationId($locationIdOne);

        $locationIdTwo = 456;
        $locationTwo->setId(456);
        $scheduleTwo->setLocationId($locationIdTwo);

        $locationIdThree = 789;
        $locationThree->setId(789);
        $scheduleThree->setLocationId($locationIdThree);

        $domainLocationSchedule->expects($this->never())
            ->method('createEmptySchedules');

        $locationsArray = array(
            $locationOne,
            $locationTwo,
            $locationThree,
        );

        $schedulesArray = array(
            $scheduleOne,
            $scheduleTwo,
            $scheduleThree,
        );

        $this->app['domainLocationSchedule'] = $domainLocationSchedule;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $result = $this->invokeMethod(
            $domainLocation,
            'mapMultipleSchedulesToLocations',
            array($locationsArray, $schedulesArray)
        );

        $this->assertEquals(
            $locationIdOne,
            $result[0]->getId(),
            'Expecting getId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdOne,
            $result[0]->getSchedule()->getLocationId(),
            'Expecting getLocationId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdTwo,
            $result[1]->getId(),
            'Expecting getId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdTwo,
            $result[1]->getSchedule()->getLocationId(),
            'Expecting getLocationId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdThree,
            $result[2]->getId(),
            'Expecting getId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdThree,
            $result[2]->getSchedule()->getLocationId(),
            'Expecting getLocationId() to equal location ID one'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::mapMultipleSchedulesToLocations
     */
    public function mapMultipleSchedulesToLocationsReturnsAllLocationsWithSchedulesWhenSchedulesDoNotExist()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $locationOne = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $locationTwo = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $locationThree = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $locationFour = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $locationFive = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $scheduleOne = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $scheduleTwo = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $scheduleThree = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $missingScheduleOne = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $missingScheduleTwo = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $domainLocationSchedule = $this->getMockBuilder('\Yumilicious\Domain\LocationSchedule')
            ->disableOriginalConstructor()
            ->getMock();

        $locationIdOne = 123;
        $locationOne->setId($locationIdOne);
        $scheduleOne->setLocationId($locationIdOne);

        $locationIdTwo = 456;
        $locationTwo->setId($locationIdTwo);
        $scheduleTwo->setLocationId($locationIdTwo);

        $locationIdThree = 789;
        $locationThree->setId($locationIdThree);
        $scheduleThree->setLocationId($locationIdThree);

        $locationIdFour = 987;
        $locationFour->setId($locationIdFour);
        $missingScheduleOne->setLocationId($locationIdFour);

        $locationIdFive = 654;
        $locationFive->setId($locationIdFive);
        $missingScheduleTwo->setLocationId($locationIdFive);

        $missingSchedules = array($locationIdFour, $locationIdFive);

        $domainLocationSchedule->expects($this->once())
            ->method('createEmptySchedules')
            ->with($missingSchedules);

        $getMultipleSchedulesResult = array(
            $missingScheduleOne,
            $missingScheduleTwo,
        );
        $domainLocationSchedule->expects($this->once())
            ->method('getMultipleSchedules')
            ->with($missingSchedules)
            ->will($this->returnValue($getMultipleSchedulesResult));

        $locationsArray = array(
            $locationOne,
            $locationTwo,
            $locationThree,
            $locationFour,
            $locationFive,
        );

        $schedulesArray = array(
            $scheduleOne,
            $scheduleTwo,
            $scheduleThree,
        );

        $this->app['domainLocationSchedule'] = $domainLocationSchedule;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $result = $this->invokeMethod(
            $domainLocation,
            'mapMultipleSchedulesToLocations',
            array($locationsArray, $schedulesArray)
        );

        $this->assertEquals(
            $locationIdOne,
            $result[0]->getId(),
            'Expecting getId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdOne,
            $result[0]->getSchedule()->getLocationId(),
            'Expecting getLocationId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdTwo,
            $result[1]->getId(),
            'Expecting getId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdTwo,
            $result[1]->getSchedule()->getLocationId(),
            'Expecting getLocationId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdThree,
            $result[2]->getId(),
            'Expecting getId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdThree,
            $result[2]->getSchedule()->getLocationId(),
            'Expecting getLocationId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdFour,
            $result[3]->getId(),
            'Expecting getId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdFour,
            $result[3]->getSchedule()->getLocationId(),
            'Expecting getLocationId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdFive,
            $result[4]->getId(),
            'Expecting getId() to equal location ID one'
        );

        $this->assertEquals(
            $locationIdFive,
            $result[4]->getSchedule()->getLocationId(),
            'Expecting getLocationId() to equal location ID one'
        );
    }
}