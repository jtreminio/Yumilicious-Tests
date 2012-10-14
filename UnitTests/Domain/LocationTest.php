<?php

namespace Yumilicious\UnitTests\Domain;

use Yumilicious\UnitTests\Base;
use Yumilicious\Domain;
use Yumilicious\Entity;
use Yumilicious\Validator;

class LocationTest extends Base
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDaoLocation()
    {
        return $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEntityLocation()
    {
        return $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->setMethods(array('validate'))
            ->getMock();
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::create
     */
    public function createReturnsEntityOnSuccess()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoLocation = $this->getDaoLocation();
        $entityLocation = $this->getEntityLocation();

        $validateReturn = array();
        $entityLocation->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validateReturn));

        $lastInsertId = 15;
        $daoLocation->expects($this->once())
            ->method('create')
            ->will($this->returnValue($lastInsertId));

        $this->app['daoLocation'] = $daoLocation;
        $this->app['entityLocation'] = $entityLocation;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $dataSet = array(
            'name' => 'test name',
        );

        $result = $domainLocation->create($dataSet);

        $this->assertEquals(
            $dataSet['name'],
            $result->getName(),
            'getCreatedAt() was not called correctly'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::create
     */
    public function createThrowsExceptionOnEntityValidationFailure()
    {
        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            "updatedBy - This value should not be blank.<br />"
        );

        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoLocation = $this->getDaoLocation();

        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $dataSet = array(
            'name'    => 'Test Name',
            'address' => 'Test Address',
            'city'    => 'Test City',
            'state'   => 'TX'
        );

        $domainLocation->create($dataSet);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::create
     */
    public function createReturnsFalseOnFailedCreateCall()
    {
        $domainLocation = $this->app['domainLocation'];

        $daoLocation = $this->getDaoLocation();
        $entityLocation = $this->getEntityLocation();

        $validateValue = array();
        $entityLocation->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validateValue));

        $lastInsertId = false;
        $daoLocation->expects($this->once())
            ->method('create')
            ->will($this->returnValue($lastInsertId));

        $this->app['daoLocation'] = $daoLocation;
        $this->app['entityLocation'] = $entityLocation;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $dataSet = array(
            'name' => 'test name',
        );

        $this->assertFalse(
            $domainLocation->create($dataSet),
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

        $daoLocation = $this->getDaoLocation();

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

        $this->setAttribute($domainLocation, 'app', $this->app);

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

        $daoLocation = $this->getDaoLocation();

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

        $this->setAttribute($domainLocation, 'app', $this->app);

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
            ->setMethods(null)
            ->getMock();

        $daoLocation = $this->getDaoLocation();

        $domainLocationSchedule = $this->getMockBuilder('\Yumilicious\Domain\LocationSchedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getMultipleSchedules',))
            ->getMock();

        $entityLocationScheduleOne = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $entityLocationScheduleTwo = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $entityLocationScheduleThree = $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $getAllResult = array(
            array(
                'id'   => 123,
                'name' => 'result 1',
            ),
            array(
                'id'   => 456,
                'name' => 'result 2',
            ),
            array(
                'id'   => 789,
                'name' => 'result 3',
            ),
        );

        $daoLocation->expects($this->once())
            ->method('getAllActive')
            ->will($this->returnValue($getAllResult));

        $entityLocationScheduleOne->setLocationId($getAllResult[0]['id']);
        $entityLocationScheduleTwo->setLocationId($getAllResult[1]['id']);
        $entityLocationScheduleThree->setLocationId($getAllResult[2]['id']);

        $scheduleIds = array(
            $getAllResult[0]['id'],
            $getAllResult[1]['id'],
            $getAllResult[2]['id'],
        );

        $multipleScheduleEntityArray = array(
            $entityLocationScheduleOne,
            $entityLocationScheduleTwo,
            $entityLocationScheduleThree,
        );

        $domainLocationSchedule->expects($this->once())
            ->method('getMultipleSchedules')
            ->with($scheduleIds)
            ->will($this->returnValue($multipleScheduleEntityArray));


        $this->app['domainLocationSchedule'] = $domainLocationSchedule;
        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $expectedResult = $getAllResult;
        $expectedResult[0]['schedule']= $entityLocationScheduleOne;
        $expectedResult[1]['schedule']= $entityLocationScheduleTwo;
        $expectedResult[2]['schedule']= $entityLocationScheduleThree;

        $result = $domainLocation->getAll('active');

        $this->assertEquals(
            $expectedResult[0]['id'],
            $result[0]->getId(),
            'Expected first location ID not correct'
        );

        $this->assertEquals(
            $expectedResult[1]['id'],
            $result[1]->getId(),
            'Expected second location ID not correct'
        );

        $this->assertEquals(
            $expectedResult[2]['id'],
            $result[2]->getId(),
            'Expected third location ID not correct'
        );

        $this->assertEquals(
            $expectedResult[0]['schedule']->getLocationId(),
            $result[0]->getSchedule()->getLocationId(),
            "Expected first location schedule's locationId not correct"
        );

        $this->assertEquals(
            $expectedResult[1]['schedule']->getLocationId(),
            $result[1]->getSchedule()->getLocationId(),
            "Expected second location schedule's locationId not correct"
        );

        $this->assertEquals(
            $expectedResult[2]['schedule']->getLocationId(),
            $result[2]->getSchedule()->getLocationId(),
            "Expected third location schedule's locationId not correct"
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

        $this->setAttribute($domainLocation, 'app', $this->app);

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

        $this->setAttribute($domainLocation, 'app', $this->app);

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

        $this->setAttribute($domainLocation, 'app', $this->app);

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