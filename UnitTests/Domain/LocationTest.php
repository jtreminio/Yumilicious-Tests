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
    protected function getDaoState()
    {
        return $this->getMockBuilder('\Yumilicious\Dao\State')
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDomainLocationSchedule()
    {
        return $this->getMockBuilder('\Yumilicious\Domain\LocationSchedule')
            ->disableOriginalConstructor()
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

    protected function getEntityLocationSchedule()
    {
        return $this->getMockBuilder('\Yumilicious\Entity\LocationSchedule')
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

        $daoLocation    = $this->getDaoLocation();
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

        $daoLocation    = $this->getDaoLocation();
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
        $domainLocation = $this->app['domainLocation'];

        $domainLocationSchedule = $this->getDomainLocationSchedule();
        $entityLocationSchedule = $this->getEntityLocationSchedule();

        $daoLocation            = $this->getDaoLocation();

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
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::getOneById
     */
    public function getOneByIdReturnsFalseOnNoResult()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoLocation = $this->getDaoLocation();

        $id = 1;
        $getOneByIdResult = false;
        $daoLocation->expects($this->once())
            ->method('getOneById')
            ->with($id)
            ->will($this->returnValue($getOneByIdResult));

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
    public function getAllReturnsFalseOnNoResult()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoLocation = $this->getDaoLocation();

        $getAllInactiveReturn = array();
        $daoLocation->expects($this->once())
            ->method('getAllInactive')
            ->will($this->returnValue($getAllInactiveReturn));

        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $status = 'inactive';
        $this->assertFalse(
            $domainLocation->getAll($status),
            'Expected ::getAll() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::getAll
     */
    public function getAllReturnsFalseOnNoResultWithNoStatus()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoLocation = $this->getDaoLocation();

        $getAllReturn = array();
        $daoLocation->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue($getAllReturn));

        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $status = 'fubar';
        $this->assertFalse(
            $domainLocation->getAll($status),
            'Expected ::getAll() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::getAll
     */
    public function getAllReturnsResults()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoLocation            = $this->getDaoLocation();
        $domainLocationSchedule = $this->getDomainLocationSchedule();

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

        $entityLocationScheduleOne   = new Entity\LocationSchedule();
        $entityLocationScheduleTwo   = new Entity\LocationSchedule();
        $entityLocationScheduleThree = new Entity\LocationSchedule();

        $entityLocationScheduleOne->setLocationId($getAllResult[0]['id']);
        $entityLocationScheduleTwo->setLocationId($getAllResult[1]['id']);
        $entityLocationScheduleThree->setLocationId($getAllResult[2]['id']);

        $multipleScheduleEntityArray = array(
            $entityLocationScheduleOne,
            $entityLocationScheduleTwo,
            $entityLocationScheduleThree,
        );

        $domainLocationSchedule->expects($this->once())
            ->method('getMultipleSchedules')
            ->will($this->returnValue($multipleScheduleEntityArray));

        $this->app['domainLocationSchedule'] = $domainLocationSchedule;
        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $result = $domainLocation->getAll('active');

        $this->assertEquals(
            $getAllResult[0]['id'],
            $result[0]->getId(),
            'Expected first location ID not correct'
        );

        $this->assertEquals(
            $getAllResult[1]['id'],
            $result[1]->getId(),
            'Expected second location ID not correct'
        );

        $this->assertEquals(
            $getAllResult[2]['id'],
            $result[2]->getId(),
            'Expected third location ID not correct'
        );

        $this->assertEquals(
            $entityLocationScheduleOne->getLocationId(),
            $result[0]->getSchedule()->getLocationId(),
            "Expected first location schedule's locationId not correct"
        );

        $this->assertEquals(
            $entityLocationScheduleTwo->getLocationId(),
            $result[1]->getSchedule()->getLocationId(),
            "Expected second location schedule's locationId not correct"
        );

        $this->assertEquals(
            $entityLocationScheduleThree->getLocationId(),
            $result[2]->getSchedule()->getLocationId(),
            "Expected third location schedule's locationId not correct"
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::update
     */
    public function updateReturnsFalseOnDaoUpdateFail()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoLocation = $this->getDaoLocation();

        $updateReturn = false;
        $daoLocation->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $entity = new Entity\Location();

        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $this->assertFalse(
            $domainLocation->update($entity),
            'Expected ::update() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::update
     */
    public function updateReturnsEntityOnSuccess()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoLocation = $this->getDaoLocation();
        $domainLocationSchedule = $this->getDomainLocationSchedule();

        $updateReturn = true;
        $daoLocation->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $domainLocationSchedule->expects($this->once())
            ->method('update');

        $entity = new Entity\Location();
        $entity->setSchedule(new Entity\LocationSchedule());
        $entity->setName('foo test');

        $this->app['daoLocation']            = $daoLocation;
        $this->app['domainLocationSchedule'] = $domainLocationSchedule;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $result = $domainLocation->update($entity);

        $this->assertEquals(
            $entity->getName(),
            $result->getName(),
            'Expected entity getName() to match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::updateFromArray
     */
    public function updateFromArrayReturnsEntityOnSuccess()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoLocation = $this->getDaoLocation();
        $domainLocationSchedule = $this->getDomainLocationSchedule();

        $updateValue = true;
        $daoLocation->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateValue));

        $locationDataset = array(
            'name' => 'location name',
            'address' => 'location address',
            'city' => 'location city',
            'state' => 'tx',
            'email' => 'local@email.com',
            'updatedBy' => 123,
        );

        $scheduleDataset = array(
            'locationId' => 123,
        );

        $this->app['daoLocation']            = $daoLocation;
        $this->app['domainLocationSchedule'] = $domainLocationSchedule;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $result = $domainLocation->updateFromArray($locationDataset, $scheduleDataset);

        $this->assertEquals(
            $locationDataset['name'],
            $result->getName(),
            'Location entity name did not match expected'
        );

        $this->assertEquals(
            $scheduleDataset['locationId'],
            $result->getSchedule()->getLocationId(),
            'Location schedule locationId did not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::updateFromArray
     */
    public function updateFromArrayThrowsExceptionOnNonValidation()
    {
        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            'updatedBy - This value should not be blank.<br />'
        );

        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $locationDataset = array(
            'name' => 'location name',
            'address' => 'location address',
            'city' => 'location city',
            'state' => 'tx',
            'email' => 'local@email.com',
        );

        $scheduleDataset = array();

        $this->setAttribute($domainLocation, 'app', $this->app);

        $result = $domainLocation->updateFromArray($locationDataset, $scheduleDataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::separateIntoStates
     */
    public function separateIntoStatesReturnsCorrectlyShapedArray()
    {
        $domainLocation = $this->app['domainLocation'];

        $daoState = $this->getDaoState();

        $locationOne = new Entity\Location();
        $locationTwo = new Entity\Location();
        $locationThree = new Entity\Location();

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
        $locationOne->setState($locationOneState);

        $locationTwoState = 'CT';
        $locationTwo->setState($locationTwoState);

        $locationThreeState = 'AL';
        $locationThree->setState($locationThreeState);

        $arrayOfLocations = array($locationOne, $locationTwo, $locationThree);

        $this->app['daoState'] = $daoState;

        $this->setAttribute($domainLocation, 'app', $this->app);

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
     * @covers \Yumilicious\Domain\Location::getStates
     */
    public function getStatesReturnsStates()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoState = $this->getDaoState();

        $getStatesValue = array('TX' => 'Texas');
        $daoState->expects($this->once())
            ->method('getStates')
            ->will($this->returnValue($getStatesValue));

        $this->app['daoState'] = $daoState;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $result = $domainLocation->getStates();

        $this->assertEquals(
            $getStatesValue['TX'],
            $result['TX']
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::getStates
     */
    public function getStatesReturnsFalseOnNoStates()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoState = $this->getDaoState();

        $getStatesValue = false;
        $daoState->expects($this->once())
            ->method('getStates')
            ->will($this->returnValue($getStatesValue));

        $this->app['daoState'] = $daoState;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $this->assertFalse(
            $domainLocation->getStates(),
            'Expected false from ::getStates()'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::getOrderingForNewLocation
     */
    public function getOrderingForNewLocationReturns10OnNoExistingNumber()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoLocation = $this->getDaoLocation();

        $getHighestOrderForStateValue = null;
        $daoLocation->expects($this->once())
            ->method('getHighestOrderForState')
            ->will($this->returnValue($getHighestOrderForStateValue));

        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $expectedReturnValue = 10;
        $state = 'tx';

        $this->assertEquals(
            $expectedReturnValue,
            $domainLocation->getOrderingForNewLocation($state),
            'Expected return value of 10'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::getOrderingForNewLocation
     */
    public function getOrderingForNewLocationReturnsExpectedValue()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoLocation = $this->getDaoLocation();

        $getHighestOrderForStateValue = array('ordering' => 10);
        $daoLocation->expects($this->once())
            ->method('getHighestOrderForState')
            ->will($this->returnValue($getHighestOrderForStateValue));

        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $expectedReturnValue = 20;
        $state = 'tx';

        $this->assertEquals(
            $expectedReturnValue,
            $domainLocation->getOrderingForNewLocation($state),
            'Expected return value of 20'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::delete
     */
    public function deleteReturnsFalseOnLocationNotFound()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoLocation = $this->getDaoLocation();

        $deleteValue = false;
        $daoLocation->expects($this->once())
            ->method('delete')
            ->will($this->returnValue($deleteValue));

        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $locationId = 123;
        $this->assertFalse(
            $domainLocation->delete($locationId),
            '::delete() expected to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::delete
     */
    public function deleteReturnsTrueOnDeletion()
    {
        /** @var $domainLocation Domain\Location */
        $domainLocation = $this->app['domainLocation'];

        $daoLocation            = $this->getDaoLocation();
        $domainLocationSchedule = $this->getDomainLocationSchedule();

        $deleteValue = true;
        $daoLocation->expects($this->once())
            ->method('delete')
            ->will($this->returnValue($deleteValue));

        $domainLocationSchedule->expects($this->once())
            ->method('delete')
            ->will($this->returnValue($deleteValue));

        $this->app['daoLocation']            = $daoLocation;
        $this->app['domainLocationSchedule'] = $domainLocationSchedule;

        $this->setAttribute($domainLocation, 'app', $this->app);

        $locationId = 123;
        $this->assertTrue(
            $domainLocation->delete($locationId),
            '::delete() expected to return true'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Location::mapMultipleSchedulesToLocations
     */
    public function mapMultipleSchedulesToLocationsReturnsAllLocationsWithSchedules()
    {
        $domainLocation = $this->app['domainLocation'];

        $locationOne   = new Entity\Location();
        $locationTwo   = new Entity\Location();
        $locationThree = new Entity\Location();

        $scheduleOne   = new Entity\LocationSchedule();
        $scheduleTwo   = new Entity\LocationSchedule();
        $scheduleThree = new Entity\LocationSchedule();

        $locationIdOne = 123;
        $locationOne->setId($locationIdOne);
        $scheduleOne->setLocationId($locationIdOne);

        $locationIdTwo = 456;
        $locationTwo->setId(456);
        $scheduleTwo->setLocationId($locationIdTwo);

        $locationIdThree = 789;
        $locationThree->setId(789);
        $scheduleThree->setLocationId($locationIdThree);

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
        $domainLocation = $this->app['domainLocation'];

        $locationOne   = $this->getEntityLocation();
        $locationTwo   = $this->getEntityLocation();
        $locationThree = $this->getEntityLocation();
        $locationFour  = $this->getEntityLocation();
        $locationFive  = $this->getEntityLocation();

        $scheduleOne   = $this->getEntityLocationSchedule();
        $scheduleTwo   = $this->getEntityLocationSchedule();
        $scheduleThree = $this->getEntityLocationSchedule();

        $missingScheduleOne = $this->getEntityLocationSchedule();
        $missingScheduleTwo = $this->getEntityLocationSchedule();

        $domainLocationSchedule = $this->getDomainLocationSchedule();

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