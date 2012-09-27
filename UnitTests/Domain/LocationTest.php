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
            $lastInsertId,
            $result,
            'Returned value does not equal expected lastInsertId value'
        );
    }
}