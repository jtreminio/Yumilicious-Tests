<?php

namespace Yumilicious\UnitTests\Domain;

use Yumilicious\UnitTests\Base;
use Yumilicious\Domain;
use Yumilicious\Entity;
use Yumilicious\Validator;

class FlavorTest extends Base
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDaoFlavor()
    {
        return $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();
    }
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDaoFlavorDetail()
    {
        return $this->getMockBuilder('\Yumilicious\Dao\FlavorDetail')
            ->getMock();
    }

    /**
     * @return Domain\Flavor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDomainFlavor()
    {
        return $this->getMockBuilder('\Yumilicious\Domain\Flavor')
            ->setConstructorArgs(array($this->app))
            ->setMethods(null)
            ->getMock();
    }

    /**
     * @return Domain\FlavorDetail|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDomainFlavorDetail()
    {
        return $this->getMockBuilder('\Yumilicious\Domain\FlavorDetail')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return Domain\FlavorType|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDomainFlavorType()
    {
        return $this->getMockBuilder('\Yumilicious\Domain\FlavorType')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEntityFlavor()
    {
        return $this->getMockBuilder('\Yumilicious\Entity\Flavor')
            ->setMethods(array('validate'))
            ->getMock();
    }

    /**
     * Data provider for getFlavorByNameReturnsExpectedValues
     *
     * @return array
     */
    public function providerGetFlavorByNameReturnsExpectedValues()
    {
        return array(
            array(
                'sorbet/chocolate',
                array(
                    'location' => 'sorbet/chocolate',
                    'name'     => 'No Sugar Added Chocolate',
                    'details'  => array(
                        'carbs'        => 5,
                        'protein'      => 1,
                        'servingSize'  => '1 oz',
                        'contains'     => 'Milk, sweetened with sucralose and Ace-K',
                        'calories'     => 23,
                        'glutenFree'   => true,
                        'liveCultures' => true,
                        'nonFat'       => true,
                        'rbstFree'     => true,
                        'transFat'     => true,
                    ),
                ),
            ),

            array(
                'beverages/shakealicious',
                array(
                    'location' => 'beverages/shakealicious',
                    'name'     => 'Shake-A-Licious',
                    'details'  => array(
                        'link'   => 'http://www.yumilicious.co/shakeit',
                        'target' => '_blank',
                    ),
                ),
            ),

            array(
                'dry/general',
                array(
                    'location' => 'dry/general',
                    'name'     => 'Dry Toppings',
                    'details'  => array(
                        'detailText' => 'Seasonal'
                    ),
                ),
            ),
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::create
     */
    public function createThrowsExceptionOnFlavorNameAlreadyExisting()
    {
        $this->setExpectedException(
            '\Yumilicious\Exception\Domain',
            'Flavor name already exists'
        );

        $domainFlavor     = $this->getDomainFlavor();
        $daoFlavor        = $this->getDaoFlavor();
        $domainFlavorDetail = $this->getDomainFlavorDetail();
        $domainFlavorType = $this->getDomainFlavorType();
        $entityFlavorType = new Entity\FlavorType();

        $flavorTypeId = 123;
        $entityFlavorType->setId($flavorTypeId);

        $domainFlavorType->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($entityFlavorType));

        $dataset = array(
            'name' => 'test name',
            'type' => 123,
        );

        $getOneByNameReturn = array(
            'name' => 'not empty',
            'type'   => $flavorTypeId,
        );
        $daoFlavor->expects($this->once())
            ->method('getOneByName')
            ->with($dataset['name'])
            ->will($this->returnValue($getOneByNameReturn));

        $this->setService('daoFlavor', $daoFlavor)
             ->setService('domainFlavorDetail', $domainFlavorDetail)
             ->setService('domainFlavorType', $domainFlavorType);

        $detailArray = array();

        $domainFlavor->create($dataset, $detailArray);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::create
     */
    public function createThrowsExceptionOnValidateReturnFalse()
    {
        $this->setExpectedException(
            '\Yumilicious\Exception\Domain',
            'updatedBy - This value should not be blank.<br />'
        );

        $domainFlavor       = $this->getDomainFlavor();
        $daoFlavor          = $this->getDaoFlavor();
        $domainFlavorDetail = $this->getDomainFlavorDetail();
        $domainFlavorType   = $this->getDomainFlavorType();
        $entityFlavorType   = new Entity\FlavorType();

        $flavorTypeId = 1;
        $entityFlavorType->setId($flavorTypeId);

        $domainFlavorType->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($entityFlavorType));

        $this->setService('daoFlavor', $daoFlavor)
             ->setService('domainFlavorDetail', $domainFlavorDetail)
             ->setService('domainFlavorType', $domainFlavorType);

        $dataset = array(
            'name' => 'test name',
            'type' => 1,
        );

        $detailArray = array();

        $domainFlavor->create($dataset, $detailArray);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::create
     */
    public function createThrowsExceptionOnTypeNotExist()
    {
        $this->setExpectedException(
            '\Yumilicious\Exception\Domain\Flavor',
            'Flavor type was not found'
        );

        $domainFlavor       = $this->getDomainFlavor();
        $domainFlavorType   = $this->getDomainFlavorType();
        $domainFlavorDetail = $this->getDomainFlavorDetail();

        $getOneByIdReturn = false;
        $dataset = array('type' => 'test');
        $domainFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($dataset['type'])
            ->will($this->returnValue($getOneByIdReturn));

        $this->setService('domainFlavorType', $domainFlavorType)
             ->setService('domainFlavorDetail', $domainFlavorDetail);

        $detailsArray = array();
        $domainFlavor->create($dataset, $detailsArray);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::create
     */
    public function createReturnsFalseOnDaoCreateFailed()
    {
        $domainFlavor     = $this->getDomainFlavor();
        $daoFlavor        = $this->getDaoFlavor();
        $domainFlavorType = $this->getDomainFlavorType();
        $entityFlavor     = $this->getEntityFlavor();
        $entityFlavorType = new Entity\FlavorType();

        $flavorTypeId = 1;
        $entityFlavorType->setId($flavorTypeId);

        $domainFlavorType->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($entityFlavorType));

        $validateReturn = array();
        $entityFlavor->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validateReturn));

        $createReturn = false;
        $daoFlavor->expects($this->once())
            ->method('create')
            ->will($this->returnValue($createReturn));

        $dataset = array(
            'name' => 'test name',
            'type' => 1,
        );

        $detailArray = array();

        $this->setService('daoFlavor', $daoFlavor)
             ->setService('domainFlavorType', $domainFlavorType)
             ->setService('entityFlavor', $entityFlavor);

        $this->assertFalse(
            $domainFlavor->create($dataset, $detailArray),
            'Expected return to be false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::create
     */
    public function createReturnsEntityOnSuccess()
    {
        $domainFlavor       = $this->getDomainFlavor();
        $daoFlavor          = $this->getDaoFlavor();
        $domainFlavorType   = $this->getDomainFlavorType();
        $domainFlavorDetail = $this->getDomainFlavorDetail();
        $entityFlavor       = $this->getEntityFlavor();
        $entityFlavorType   = new Entity\FlavorType();

        $flavorTypeId = 1;
        $entityFlavorType->setId($flavorTypeId);

        $domainFlavorType->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($entityFlavorType));

        $validateReturn = array();
        $entityFlavor->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validateReturn));

        $createReturn = true;
        $daoFlavor->expects($this->once())
            ->method('create')
            ->will($this->returnValue($createReturn));

        $createMultipleFromArrayReturn = true;
        $domainFlavorDetail->expects($this->once())
            ->method('createMultipleFromArray')
            ->will($this->returnValue($createMultipleFromArrayReturn));

        $dataset = array(
            'name' => 'test name',
            'type' => 1,
        );

        $detailArray = array('link' => 'http://test.com');

        $this->setService('daoFlavor', $daoFlavor)
             ->setService('domainFlavorDetail', $domainFlavorDetail)
             ->setService('domainFlavorType', $domainFlavorType)
             ->setService('entityFlavor', $entityFlavor);

        /** @var $result Entity\Flavor */
        $result = $domainFlavor->create($dataset, $detailArray);

        $this->assertEquals(
            $dataset['name'],
            $result->getName(),
            'Entity getName() did not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::update
     */
    public function updateReturnsFalseOnFailure()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();
        $entityFlavor = new Entity\Flavor();

        $updateReturn = false;
        $daoFlavor->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $this->assertFalse(
            $domainFlavor->update($entityFlavor),
            'Expected ::update() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::update
     */
    public function updateReturnsEntityOnSuccess()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();
        $entityFlavor = new Entity\Flavor();

        $entityName = 'test name';
        $entityFlavor->setName($entityName);

        $updateReturn = true;
        $daoFlavor->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $this->setService('daoFlavor', $daoFlavor);

        /** @var $result Entity\Flavor */
        $result = $domainFlavor->update($entityFlavor);

        $this->assertEquals(
            $entityName,
            $result->getName(),
            'Entity getName() does not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::updateFromArray
     */
    public function updateFromArrayThrowsExceptionOnFlavorNotFound()
    {
        $this->setExpectedException(
            '\Yumilicious\Exception\Domain',
            'Flavor was not found'
        );

        $domainFlavor     = $this->getDomainFlavor();
        $daoFlavor        = $this->getDaoFlavor();
        $domainFlavorType = $this->getDomainFlavorType();

        $dataset = array(
            'name' => 'test name',
            'type' => 123,
        );

        $entityFlavorType = new Entity\Flavortype();
        $entityFlavorType->setId(123);
        $domainFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($dataset['type'])
            ->will($this->returnValue($entityFlavorType));

        $this->setService('domainFlavorType', $domainFlavorType)
             ->setService('daoFlavor', $daoFlavor);

        $domainFlavor->updateFromArray($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::updateFromArray
     */
    public function updateFromArrayThrowsExceptionOnTypeNotFound()
    {
        $this->setExpectedException(
            '\Yumilicious\Exception\Domain',
            'Flavor type was not found'
        );

        $domainFlavor     = $this->getDomainFlavor();
        $domainFlavorType = $this->getDomainFlavorType();

        $dataset = array(
            'name' => 'test name',
            'type' => 123,
        );

        $getOneByIdReturn = false;
        $domainFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($dataset['type'])
            ->will($this->returnValue($getOneByIdReturn));

        $this->setService('domainFlavorType', $domainFlavorType);

        $domainFlavor->updateFromArray($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::updateFromArray
     */
    public function updateFromArrayThrowsExceptionOnValidateFailure()
    {
        $this->setExpectedException(
            '\Yumilicious\Exception\Domain',
            'updatedBy - This value should not be blank.<br />'
        );

        $domainFlavor     = $this->getDomainFlavor();
        $daoFlavor        = $this->getDaoFlavor();
        $domainFlavorType = $this->getDomainFlavorType();
        $entityFlavorType = new Entity\FlavorType();

        $dataset = array(
            'id'   => 123,
            'name' => 'test name',
            'type' => 321,
        );

        $domainFlavorType->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($entityFlavorType));

        $getOneByIdReturn = array(
            array(
                'id'           => $dataset['id'],
                'name'         => $dataset['name'],
                'type-id'      => $dataset['type'],
                'type-name'    => 'type name',
                'detail-id'    => 987,
                'detail-name'  => 'detailName1',
                'detail-value' => '60',
            ),
        );
        $daoFlavor->expects($this->once())
            ->method('getOneById')
            ->with($dataset['id'])
            ->will($this->returnValue($getOneByIdReturn));

        $this->setService('domainFlavorType', $domainFlavorType)
             ->setService('daoFlavor', $daoFlavor);

        $domainFlavor->updateFromArray($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::updateFromArray
     */
    public function updateFromArrayThrowsErrorOnFlavorNameAlreadyExistsAndNotThisFlavor()
    {
        $this->setExpectedException(
            '\Yumilicious\Exception\Domain',
            'Flavor name already exists'
        );

        $domainFlavor       = $this->getDomainFlavor();
        $daoFlavor          = $this->getDaoFlavor();
        $domainFlavorDetail = $this->getDomainFlavorDetail();
        $domainFlavorType   = $this->getDomainFlavorType();
        $entityFlavor       = $this->getEntityFlavor();
        $entityFlavorType   = new Entity\FlavorType();

        $domainFlavorType->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($entityFlavorType));

        $validateReturn = array();
        $entityFlavor->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validateReturn));

        $dataset = array(
            'id'   => 123,
            'name' => 'duplicate name',
            'type' => 123,
        );

        $getOneByIdReturn = array(
            array(
                'id'           => $dataset['id'],
                'name'         => $dataset['name'],
                'type-id'      => $dataset['type'],
                'type-name'    => 'type name',
                'detail-id'    => 987,
                'detail-name'  => 'detailName1',
                'detail-value' => '60',
            ),
        );
        $daoFlavor->expects($this->once())
            ->method('getOneById')
            ->with($dataset['id'])
            ->will($this->returnValue($getOneByIdReturn));

        $getOneByNameReturn = array(
            'id'   => 456,
            'type' => $dataset['type'],
        );
        $daoFlavor->expects($this->once())
            ->method('getOneByName')
            ->with($dataset['name'])
            ->will($this->returnValue($getOneByNameReturn));

        $this->setService('daoFlavor', $daoFlavor)
             ->setService('domainFlavorDetail', $domainFlavorDetail)
             ->setService('domainFlavorType', $domainFlavorType)
             ->setService('entityFlavor', $entityFlavor);

        $domainFlavor->updateFromArray($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::updateFromArray
     * @covers \Yumilicious\Domain\Flavor::createNestedFlavorDetailTypeArray
     */
    public function updateFromArrayReturnsEntityOnSuccess()
    {
        $domainFlavor       = $this->getDomainFlavor();
        $domainFlavorDetail = $this->getDomainFlavorDetail();
        $domainFlavorType   = $this->getDomainFlavorType();
        $daoFlavor          = $this->getDaoFlavor();
        $entityFlavor       = $this->getEntityFlavor();
        $entityFlavorType   = new Entity\FlavorType();

        $dataset = array(
            'id'         => 123,
            'name'       => 'test name',
            'type'       => 321,
            'image'      => 'testImage.png',
            'titleImage' => 'titleImage.png',
        );

        $domainFlavorType->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($entityFlavorType));

        $validateReturn = array();
        $entityFlavor->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validateReturn));

        $getOneByIdReturn = array(
            array(
                'id'           => $dataset['id'],
                'name'         => $dataset['name'],
                'type-id'      => $dataset['type'],
                'type-name'    => 'type name',
                'detail-id'    => 987,
                'detail-name'  => 'detailName1',
                'detail-value' => '60',
            ),
            array(
                'id'           => $dataset['id'],
                'name'         => $dataset['name'],
                'type-id'      => $dataset['type'],
                'type-name'    => 'type name',
                'detail-id'    => 789,
                'detail-name'  => 'detailName2',
                'detail-value' => '120',
            ),
        );
        $daoFlavor->expects($this->once())
            ->method('getOneById')
            ->with($dataset['id'])
            ->will($this->returnValue($getOneByIdReturn));

        $updateReturn = true;
        $daoFlavor->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $this->setService('daoFlavor', $daoFlavor)
             ->setService('domainFlavorDetail', $domainFlavorDetail)
             ->setService('domainFlavorType', $domainFlavorType)
             ->setService('entityFlavor', $entityFlavor);

        /** @var $result Entity\Flavor */
        $result = $domainFlavor->updateFromArray($dataset);

        $this->assertEquals(
            $dataset['name'],
            $result->getName(),
            'Entity getName() does not match expected'
        );

        $this->assertEquals(
            $dataset['type'],
            $result->getType()->getId(),
            'Type id does not match expected'
        );

        $this->assertEquals(
            $getOneByIdReturn[0]['detail-id'],
            $result->getDetails()['detailName1']->getId(),
            'Detail id 1 does not match expected'
        );

        $this->assertEquals(
            $getOneByIdReturn[1]['detail-name'],
            $result->getDetails()['detailName2']->getName(),
            'Detail name 2 does not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::updateFromArray
     * @covers \Yumilicious\Domain\Flavor::createNestedFlavorDetailTypeArray
     */
    public function updateFromArrayReturnsEntityOnSuccessAndDeletesExistingImages()
    {
        $domainFlavor = $this->getMockBuilder('\Yumilicious\Domain\Flavor')
            ->setConstructorArgs(array($this->app))
            ->setMethods(array('deleteImage'))
            ->getMock();

        $domainFlavorDetail = $this->getDomainFlavorDetail();
        $domainFlavorType   = $this->getDomainFlavorType();
        $daoFlavor          = $this->getDaoFlavor();
        $entityFlavor       = $this->getEntityFlavor();
        $entityFlavorType   = new Entity\FlavorType();

        $dataset = array(
            'id'   => 123,
            'name' => 'test name',
            'type' => 321,
        );

        $domainFlavorType->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($entityFlavorType));

        $validateReturn = array();
        $entityFlavor->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validateReturn));

        $getOneByIdReturn = array(
            array(
                'id'           => $dataset['id'],
                'name'         => $dataset['name'],
                'type-id'      => $dataset['type'],
                'type-name'    => 'type name',
                'detail-id'    => 987,
                'detail-name'  => 'detailName1',
                'detail-value' => '60',
            ),
            array(
                'id'           => $dataset['id'],
                'name'         => $dataset['name'],
                'type-id'      => $dataset['type'],
                'type-name'    => 'type name',
                'detail-id'    => 789,
                'detail-name'  => 'detailName2',
                'detail-value' => '120',
            ),
        );
        $daoFlavor->expects($this->once())
            ->method('getOneById')
            ->with($dataset['id'])
            ->will($this->returnValue($getOneByIdReturn));

        $updateReturn = true;
        $daoFlavor->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $domainFlavor->expects($this->exactly(2))
            ->method('deleteImage');

        $this->setService('daoFlavor', $daoFlavor)
             ->setService('domainFlavorDetail', $domainFlavorDetail)
             ->setService('domainFlavorType', $domainFlavorType)
             ->setService('entityFlavor', $entityFlavor);

        /** @var $result Entity\Flavor */
        $result = $domainFlavor->updateFromArray($dataset);

        $this->assertEquals(
            $dataset['name'],
            $result->getName(),
            'Entity getName() does not match expected'
        );

        $this->assertEquals(
            $dataset['type'],
            $result->getType()->getId(),
            'Type id does not match expected'
        );

        $this->assertEquals(
            $getOneByIdReturn[0]['detail-id'],
            $result->getDetails()['detailName1']->getId(),
            'Detail id 1 does not match expected'
        );

        $this->assertEquals(
            $getOneByIdReturn[1]['detail-name'],
            $result->getDetails()['detailName2']->getName(),
            'Detail name 2 does not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getOneById
     */
    public function getOneByIdReturnsFalseOnNotRecordFound()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getOneByIdReturn = array();
        $daoFlavor->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($getOneByIdReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $id = 123;
        $this->assertFalse(
            $domainFlavor->getOneById($id),
            'Expected ::getOneById() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getOneById
     * @covers \Yumilicious\Domain\Flavor::createNestedFlavorDetailTypeArray
     * @covers \Yumilicious\Domain::matchArrayKeys
     * @covers \Yumilicious\Domain::cropArrayKeys
     * @covers \Yumilicious\Domain::removeMatchingArrayKeys
     * @covers \Yumilicious\Domain::matchArrayKeyToArrayOfStrings
     */
    public function getOneByIdEntityOnSuccess()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getOneByIdReturn = array(
            array(
                'id' => 123,
                'name' => 'Test Name',
                'type-id' => 321,
                'type-name' => 'Test Name Type',
                'detail-id' => 213,
                'detail-name' => 'rbstFree'
            ),
            array(
                'id' => 123,
                'name' => 'Test Name',
                'type-id' => 321,
                'type-name' => 'Test Name Type',
                'detail-id' => 546,
                'detail-name' => 'vegetarian'
            ),
            array(
                'id' => 123,
                'name' => 'Test Name',
                'type-id' => 321,
                'type-name' => 'Test Name Type',
                'detail-id' => 879,
                'detail-name' => 'calories'
            ),
        );

        $daoFlavor->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($getOneByIdReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $id = 123;
        /** @var $result Entity\Flavor */
        $result = $domainFlavor->getOneById($id);

        $this->assertEquals(
            $getOneByIdReturn[0]['name'],
            $result->getName(),
            '::getName() does not match expected'
        );

        $this->assertEquals(
            $getOneByIdReturn[0]['type-id'],
            $result->getType()->getId(),
            'Flavor Type Id did not match expected'
        );

        $this->assertEquals(
            $getOneByIdReturn[0]['detail-name'],
            $result->getDetails()['rbstFree']->getName(),
            'Result did not contain expected rbstFree detail'
        );

        $this->assertEquals(
            $getOneByIdReturn[1]['detail-name'],
            $result->getDetails()['vegetarian']->getName(),
            'Result did not contain expected vegetarian detail'
        );

        $this->assertEquals(
            $getOneByIdReturn[2]['detail-name'],
            $result->getDetails()['calories']->getName(),
            'Result did not contain expected calories detail'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getOneByIdNoType
     */
    public function getOneByIdNoTypeReturnsFalseOnNotRecordFound()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getOneByIdReturn = array();
        $daoFlavor->expects($this->once())
            ->method('getOneByIdNoType')
            ->will($this->returnValue($getOneByIdReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $id = 123;
        $this->assertFalse(
            $domainFlavor->getOneByIdNoType($id),
            'Expected ::getOneByIdNoType() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getOneByIdNoType
     * @covers \Yumilicious\Domain\Flavor::createNestedFlavorDetailTypeArray
     * @covers \Yumilicious\Domain::matchArrayKeys
     * @covers \Yumilicious\Domain::cropArrayKeys
     * @covers \Yumilicious\Domain::removeMatchingArrayKeys
     * @covers \Yumilicious\Domain::matchArrayKeyToArrayOfStrings
     */
    public function getOneByIdNoTypeEntityOnSuccess()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getOneByIdReturn = array(
            array(
                'id' => 123,
                'name' => 'Test Name',
                'detail-id' => 213,
                'detail-name' => 'rbstFree'
            ),
            array(
                'id' => 123,
                'name' => 'Test Name',
                'detail-id' => 546,
                'detail-name' => 'vegetarian'
            ),
            array(
                'id' => 123,
                'name' => 'Test Name',
                'detail-id' => 879,
                'detail-name' => 'calories'
            ),
        );

        $daoFlavor->expects($this->once())
            ->method('getOneByIdNoType')
            ->will($this->returnValue($getOneByIdReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $id = 123;
        /** @var $result Entity\Flavor */
        $result = $domainFlavor->getOneByIdNoType($id);

        $this->assertEquals(
            $getOneByIdReturn[0]['name'],
            $result->getName(),
            '::getName() does not match expected'
        );

        $this->assertEquals(
            $getOneByIdReturn[0]['detail-name'],
            $result->getDetails()['rbstFree']->getName(),
            'Result did not contain expected rbstFree detail'
        );

        $this->assertEquals(
            $getOneByIdReturn[1]['detail-name'],
            $result->getDetails()['vegetarian']->getName(),
            'Result did not contain expected vegetarian detail'
        );

        $this->assertEquals(
            $getOneByIdReturn[2]['detail-name'],
            $result->getDetails()['calories']->getName(),
            'Result did not contain expected calories detail'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getall
     */
    public function getAllReturnsFalseOnNoActiveResults()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getAllActiveReturn = array();
        $activeStatus = 1;
        $daoFlavor->expects($this->once())
            ->method('getAllByActive')
            ->with($activeStatus)
            ->will($this->returnValue($getAllActiveReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $status = 'active';

        $this->assertFalse(
            $domainFlavor->getAll($status),
            'Expected ::getAll() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getall
     */
    public function getAllReturnsFalseOnNoInactiveResults()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getAllActiveReturn = array();
        $activeStatus = 0;
        $daoFlavor->expects($this->once())
            ->method('getAllByActive')
            ->with($activeStatus)
            ->will($this->returnValue($getAllActiveReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $status = 'inactive';

        $this->assertFalse(
            $domainFlavor->getAll($status),
            'Expected ::getAll() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getAll
     * @covers \Yumilicious\Domain\Flavor::createNestedFlavorDetailTypeArray
     * @covers \Yumilicious\Domain::nestMultipleResultsByKey
     */
    public function getAllReturnsEntityOnSuccess()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getAllActiveReturn = array(
            array(
                'id'          => 123,
                'name'        => 'test name 1',
                'type-id'     => 1234,
                'type-name'   => 'test type name 1',
                'detail-id'   => 4321,
                'detail-name' => 'test detail name 1',
            ),
            array(
                'id'          => 123,
                'name'        => 'test name 1',
                'type-id'     => 1234,
                'type-name'   => 'test type name 1',
                'detail-id'   => 43210,
                'detail-name' => 'test detail name 1-1',
            ),
            array(
                'id'          => 456,
                'name'        => 'test name 2',
                'type-id'     => 4567,
                'type-name'   => 'test type name 2',
                'detail-id'   => 7654,
                'detail-name' => 'test detail name 2',
            ),
            array(
                'id'          => 456,
                'name'        => 'test name 2',
                'type-id'     => 4567,
                'type-name'   => 'test type name 2',
                'detail-id'   => 76543,
                'detail-name' => 'test detail name 2-1',
            ),
        );
        $daoFlavor->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue($getAllActiveReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $status = 'fauxStatus';
        $result = $domainFlavor->getAll($status);

        $this->assertEquals(
            $getAllActiveReturn[0]['name'],
            $result[0]->getName(),
            'Expected first entity to match name'
        );

        $this->assertEquals(
            $getAllActiveReturn[2]['name'],
            $result[1]->getName(),
            'Expected second entity to match name'
        );
    }

    /**
     * @test
     * @dataProvider providerGetAllNamesReturnsFalseOnNoResults
     * @covers \Yumilicious\Domain\Flavor::getAllNames
     */
    public function getAllNamesReturnsFalseOnNoResults($status, $activeStatus)
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getAllActiveReturn = array();
        $daoFlavor->expects($this->once())
            ->method('getAllNamesByActive')
            ->with($activeStatus)
            ->will($this->returnValue($getAllActiveReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $this->assertFalse(
            $domainFlavor->getAllNames($status),
            'Expected ::getAll() to return false'
        );
    }

    /**
     * Provider for getAllNamesReturnsFalseOnNoResults
     *
     * @return array
     */
    public function providerGetAllNamesReturnsFalseOnNoResults()
    {
        return array(
            array('active', 1),
            array('inactive', 0),
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getAllNames
     * @covers \Yumilicious\Domain\Flavor::createNestedFlavorDetailTypeArray
     * @covers \Yumilicious\Domain::nestMultipleResultsByKey
     */
    public function getAllNamesReturnsEntityOnSuccess()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getAllActiveReturn = array(
            array(
                'id'              => 123,
                'name'            => 'test name 1',
                'type-name'       => 'test type name 1',
                'typeParent-name' => 'test typeParent name 1',
            ),
            array(
                'id'              => 321,
                'name'            => 'test name 2',
                'type-name'       => 'test type name 2',
                'typeParent-name' => 'test typeParent name 1',
            ),
            array(
                'id'              => 456,
                'name'            => 'test name 3',
                'type-name'       => 'test type name 3',
                'typeParent-name' => 'test typeParent name 2',
            ),
            array(
                'id'              => 789,
                'name'            => 'test name 4',
                'type-name'       => 'test type name 4',
                'typeParent-name' => 'test typeParent name 3',
            ),
            array(
                'id'              => 854,
                'name'            => 'test name 5',
                'type-name'       => 'test type name 5',
                'typeParent-name' => null,
            ),
            array(
                'id'              => 5153,
                'name'            => 'test name 6',
                'type-name'       => null,
                'typeParent-name' => null,
            ),
        );
        $statusParameter = 1;
        $daoFlavor->expects($this->once())
            ->method('getAllNamesByActive')
            ->with($statusParameter)
            ->will($this->returnValue($getAllActiveReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $status = 'active';
        $result = $domainFlavor->getAllNames($status);

        $this->assertEquals(
            $getAllActiveReturn[0]['name'],
            $result['test typeParent name 1']['test type name 1'][0]->getName(),
            'Expected first entity to match name'
        );

        $this->assertEquals(
            $getAllActiveReturn[1]['name'],
            $result['test typeParent name 1']['test type name 2'][0]->getName(),
            'Expected first entity to match name'
        );

        $this->assertEquals(
            $getAllActiveReturn[2]['name'],
            $result['test typeParent name 2']['test type name 3'][0]->getName(),
            'Expected first entity to match name'
        );

        $this->assertEquals(
            $getAllActiveReturn[3]['name'],
            $result['test typeParent name 3']['test type name 4'][0]->getName(),
            'Expected first entity to match name'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::delete
     */
    public function deleteThrowsExceptionOnFlavorNotFound()
    {
        $flavorId = 123;

        $this->setExpectedException(
            '\Yumilicious\Exception\Domain',
            "Flavor Id {$flavorId} was not found"
        );

        $domainFlavor    = $this->getDomainFlavor();
        $daoFlavor       = $this->getDaoFlavor();
        $daoFlavorDetail = $this->getDaoFlavorDetail();

        $getOneByIdReturn = false;
        $daoFlavor->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($getOneByIdReturn));

        $this->setService('daoFlavor', $daoFlavor)
             ->setService('daoFlavorDetail', $daoFlavorDetail);

        $domainFlavor->delete($flavorId);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::delete
     */
    public function deleteThrowsExceptionOnErrorDeleting()
    {
        $flavorId = 123;

        $this->setExpectedException(
            '\Yumilicious\Exception\Domain',
            'There was a problem deleting some flavor information from the database!'
        );

        $domainFlavor    = $this->getDomainFlavor();
        $daoFlavor       = $this->getDaoFlavor();
        $daoFlavorDetail = $this->getDaoFlavorDetail();

        $getOneByIdReturn = array(
            'id'   => $flavorId,
            'name' => 'Test Name',
        );
        $daoFlavor->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($getOneByIdReturn));

        $daoFlavor->expects($this->once())
            ->method('delete')
            ->will($this->throwException(new \Exception()));

        $this->setService('daoFlavor', $daoFlavor)
             ->setService('daoFlavorDetail', $daoFlavorDetail);

        $domainFlavor->delete($flavorId);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::delete
     */
    public function deleteReturnsTrue()
    {
        $flavorId = 123;

        /** @var $domainFlavor Domain\Flavor|\PHPUnit_Framework_MockObject_MockObject */
        $domainFlavor    = $this->getMockBuilder('\Yumilicious\Domain\Flavor')
            ->setConstructorArgs(array($this->app))
            ->setMethods(array('deleteImage'))
            ->getMock();
        $daoFlavor       = $this->getDaoFlavor();
        $daoFlavorDetail = $this->getDaoFlavorDetail();

        $getOneByIdReturn = array(
            'id'   => $flavorId,
            'name' => 'Test Name',
        );
        $daoFlavor->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($getOneByIdReturn));

        $deleteReturn = true;
        $daoFlavor->expects($this->once())
            ->method('delete')
            ->will($this->returnValue($deleteReturn));

        $daoFlavorDetail->expects($this->once())
            ->method('deleteByFlavorId')
            ->with($flavorId)
            ->will($this->returnValue($deleteReturn));

        $deleteImageCallCount = 2;
        $domainFlavor->expects($this->exactly($deleteImageCallCount))
            ->method('deleteImage');

        $this->setService('daoFlavor', $daoFlavor)
             ->setService('daoFlavorDetail', $daoFlavorDetail);

        $this->assertTrue(
            $domainFlavor->delete($flavorId),
            'Expected ::delete() to return true'
        );
    }
}