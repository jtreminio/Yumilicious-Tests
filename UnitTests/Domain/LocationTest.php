<?php

namespace Yumilicious\UnitTests\Domain;

use Yumilicious\UnitTests\Base;
use Yumilicious\Domain\Location;

class LocationTest extends Base
{
    /**
     * @test
     */
    public function addLocationSetsCreatedAtIfEmpty()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('getDateTime'))
            ->getMock();

        $entityLocation = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'hydrate',
                    'validate',
                )
            )
            ->getMock();

        $daoLocation = $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dateTime = new \DateTime();
        $dataSet = array();

        $domainLocation->expects($this->once())
            ->method('getDateTime')
            ->will($this->returnValue($dateTime));

        $entityLocation->expects($this->once())
            ->method('hydrate')
            ->with($dataSet);

        $this->app['entityLocation'] = $entityLocation;
        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $domainLocation->addLocation($dataSet);

        $this->assertEquals(
            $dateTime->format('Y-m-d H:i:s'),
            $entityLocation->getCreatedAt(),
            'getCreatedAt() was not called correctly'
        );
    }

    /**
     * @test
     */
    public function addLocationThrowsExceptionOnEntityValidationFailure()
    {
        $errorPropertyPath = 'My property path';
        $errorMessage = 'My error message';

        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            '"'.$errorPropertyPath.'" - '.$errorMessage
        );

        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('getDateTime'))
            ->getMock();

        $entityLocation = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'hydrate',
                    'validate',
                )
            )
            ->getMock();

        $daoLocation = $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->disableOriginalConstructor()
            ->setMethods(array())
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
        $this->app['daoLocation'] = $daoLocation;

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
     */
    public function addLocationReturnsLastInsertIdOnSuccessfulCreation()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('getDateTime'))
            ->getMock();

        $entityLocation = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $daoLocation = $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();

        $lastInsertId = 123;

        $daoLocation->expects($this->once())
            ->method('create')
            ->with($entityLocation)
            ->will($this->returnValue($lastInsertId));

        $this->app['entityLocation'] = $entityLocation;
        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $dataSet = array();
        $result = $domainLocation->addLocation($dataSet);

        $this->assertEquals(
            $entityLocation,
            $result,
            'Returned value does not equal expected lastInsertId value'
        );
    }

    /**
     * @test
     */
    public function addLocationReturnsFalseOnFailedCreateCall()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('getDateTime'))
            ->getMock();

        $entityLocation = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $daoLocation = $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();

        $createValue = false;

        $daoLocation->expects($this->once())
            ->method('create')
            ->with($entityLocation)
            ->will($this->returnValue($createValue));

        $this->app['entityLocation'] = $entityLocation;
        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $dataSet = array();
        $result = $domainLocation->addLocation($dataSet);

        $this->assertFalse(
            $result,
            'Returned value should be false'
        );
    }

    /**
     * @test
     */
    public function getOneByIdReturnsResults()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $daoLocation = $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('getOneById'))
            ->getMock();

        $entityLocation = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('hydrate'))
            ->getMock();

        $id = 1;
        $getOneByIdResult = array(1, 2, 3);

        $daoLocation->expects($this->once())
            ->method('getOneById')
            ->with($id)
            ->will($this->returnValue($getOneByIdResult));

        $entityLocation->expects($this->once())
            ->method('hydrate')
            ->will($this->returnValue($getOneByIdResult));

        $this->app['entityLocation'] = $entityLocation;
        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $this->assertEquals(
            $getOneByIdResult,
            $domainLocation->getOneById($id),
            'Expected an entity result'
        );
    }

    /**
     * @test
     */
    public function getOneByIdReturnsFalseOnNoResult()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $daoLocation = $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('getOneById'))
            ->getMock();

        $entityLocation = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('hydrate'))
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
     */
    public function getAllReturnsResults()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('hydrateMultiple'))
            ->getMock();

        $daoLocation = $this->getMockBuilder('\Yumilicious\Dao\Location')
            ->disableOriginalConstructor()
            ->setMethods(array('getAll'))
            ->getMock();

        $getAllResult = array(1, 2, 3);
        $entityName = 'entityLocation';

        $daoLocation->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue($getAllResult));

        $domainLocation->expects($this->once())
            ->method('hydrateMultiple')
            ->with($entityName, $getAllResult)
            ->will($this->returnValue($getAllResult));

        $this->app['daoLocation'] = $daoLocation;

        $this->setAttribute(
            $domainLocation,
            'app',
            $this->app
        );

        $this->assertEquals(
            $getAllResult,
            $domainLocation->getAll(),
            'Expected return of an array'
        );
    }

    /**
     * @test
     */
    public function separateIntoStatesReturnsCorrectlyShapedArray()
    {
        $domainLocation = $this->getMockBuilder('\Yumilicious\Domain\Location')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $daoState = $this->getMockBuilder('\Yumilicious\Dao\State')
            ->disableOriginalConstructor()
            ->setMethods(array('getStates'))
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
}