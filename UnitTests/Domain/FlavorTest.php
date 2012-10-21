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
     * @test
     * @covers \Yumilicious\Domain\Flavor::getFlavorByName
     * @dataProvider providerGetFlavorByNameReturnsExpectedValues
     */
    public function getFlavorByNameReturnsExpectedValues(
        $searchString,
        $expectedResult
    )
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $yogurtFlavors = array(
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
            array(
                'location' => 'sorbet/coffee',
                'name'     => 'No Sugar Added Coffee',
                'details'  => array(
                    'carbs'        => 5,
                    'protein'      => 1,
                    'servingSize'  => '1 oz',
                    'contains'     => 'Milk, sweetened with sucralose and Ace-K',
                    'calories'     => 20,
                    'glutenFree'   => true,
                    'liveCultures' => true,
                    'nonFat'       => true,
                    'rbstFree'     => true,
                    'transFat'     => true,
                ),
            ),
        );

        $extraFlavorKeys = array(
            array(
                'location' => 'dry/general',
                'name'     => 'Dry Toppings',
                'details'  => array(
                    'detailText' => 'Seasonal'
                ),
            ),
        );

        $beverages = array(
            array(
                'location' => 'beverages/shakealicious',
                'name'     => 'Shake-A-Licious',
                'details'  => array(
                    'link'   => 'http://www.yumilicious.co/shakeit',
                    'target' => '_blank',
                ),
            ),
            array(
                'location' => 'beverages/hotchocolate',
                'name'     => 'Hot Chocolate*',
                'details'  => array(
                    'detailText' => 'Seasonal. Available at most locations.'
                ),
            ),
        );

        $daoFlavor->expects($this->once())
            ->method('getYogurtFlavors')
            ->will($this->returnValue($yogurtFlavors));

        $daoFlavor->expects($this->once())
            ->method('getExtraFlavorKeys')
            ->will($this->returnValue($extraFlavorKeys));

        $daoFlavor->expects($this->once())
            ->method('getBeverages')
            ->will($this->returnValue($beverages));

        $this->setService('daoFlavor', $daoFlavor);

        $result = $domainFlavor->getFlavorByName($searchString);

        $this->assertEquals(
            $expectedResult,
            $result,
            'Array does not contain expected'
        );
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
     * @covers \Yumilicious\Domain\Flavor::getFlavorByName
     */
    public function getFlavorByNameThrowsExceptionWhenFlavorNotFound()
    {
        $invalidFlavorName = 'sorbet/coffee';

        $this->setExpectedException(
            'Yumilicious\Exception\Domain\Flavor',
            "Flavor name {$invalidFlavorName} not found"
        );

        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $yogurtFlavors = array(
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
        );

        $extraFlavorKeys = array(
            array(
                'location' => 'dry/general',
                'name'     => 'Dry Toppings',
                'details'  => array(
                    'detailText' => 'Seasonal'
                ),
            ),
        );

        $beverages = array(
            array(
                'location' => 'beverages/shakealicious',
                'name'     => 'Shake-A-Licious',
                'details'  => array(
                    'link'   => 'http://www.yumilicious.co/shakeit',
                    'target' => '_blank',
                ),
            ),
        );

        $daoFlavor->expects($this->once())
            ->method('getYogurtFlavors')
            ->will($this->returnValue($yogurtFlavors));

        $daoFlavor->expects($this->once())
            ->method('getExtraFlavorKeys')
            ->will($this->returnValue($extraFlavorKeys));

        $daoFlavor->expects($this->once())
            ->method('getBeverages')
            ->will($this->returnValue($beverages));

        $this->setService('daoFlavor', $daoFlavor);

        $domainFlavor->getFlavorByName($invalidFlavorName);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getSortedYogurtFlavors
     */
    public function getSortedYogurtFlavorsReturnsAlphabetizedList()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $yogurtFlavors = array(
            array(
                'location' => 'sorbet/coffee',
                'name'     => 'No Sugar Added Coffee',
                'details'  => array(
                    'carbs'        => 5,
                    'protein'      => 1,
                    'servingSize'  => '1 oz',
                    'contains'     => 'Milk, sweetened with sucralose and Ace-K',
                    'calories'     => 20,
                    'glutenFree'   => true,
                    'liveCultures' => true,
                    'nonFat'       => true,
                    'rbstFree'     => true,
                    'transFat'     => true,
                ),
            ),
            array(
                'location' => 'sorbet/pomegranateRaspberry',
                'name'     => 'Pomegranate Raspberry Sorbet',
                'details'  => array(
                    'carbs'        => 6,
                    'servingSize'  => '1 oz',
                    'calories'     => 25,
                    'glutenFree'   => true,
                    'nonFat'       => true,
                    'dairyFree'    => true,
                    'transFat'     => true,
                ),
            ),
            array(
                'location' => 'sorbet/kiwiStrawberry',
                'name'     => 'Kiwi Strawberry Sorbet',
                'details'  => array(
                    'carbs'        => 6,
                    'protein'      => 1,
                    'servingSize'  => '1 oz',
                    'calories'     => 23,
                    'glutenFree'   => true,
                    'nonFat'       => true,
                    'dairyFree'    => true,
                    'transFat'     => true,
                ),
            ),
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
        );

        $expectedResult = array(
            array(
                'location' => 'sorbet/kiwiStrawberry',
                'name'     => 'Kiwi Strawberry Sorbet',
                'details'  => array(
                    'carbs'        => 6,
                    'protein'      => 1,
                    'servingSize'  => '1 oz',
                    'calories'     => 23,
                    'glutenFree'   => true,
                    'nonFat'       => true,
                    'dairyFree'    => true,
                    'transFat'     => true,
                ),
            ),
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
            array(
                'location' => 'sorbet/coffee',
                'name'     => 'No Sugar Added Coffee',
                'details'  => array(
                    'carbs'        => 5,
                    'protein'      => 1,
                    'servingSize'  => '1 oz',
                    'contains'     => 'Milk, sweetened with sucralose and Ace-K',
                    'calories'     => 20,
                    'glutenFree'   => true,
                    'liveCultures' => true,
                    'nonFat'       => true,
                    'rbstFree'     => true,
                    'transFat'     => true,
                ),
            ),
            array(
                'location' => 'sorbet/pomegranateRaspberry',
                'name'     => 'Pomegranate Raspberry Sorbet',
                'details'  => array(
                    'carbs'        => 6,
                    'servingSize'  => '1 oz',
                    'calories'     => 25,
                    'glutenFree'   => true,
                    'nonFat'       => true,
                    'dairyFree'    => true,
                    'transFat'     => true,
                ),
            ),
        );

        $daoFlavor->expects($this->once())
            ->method('getYogurtFlavors')
            ->will($this->returnValue($yogurtFlavors));

        $this->setService('daoFlavor', $daoFlavor);

        $result = $domainFlavor->getSortedYogurtFlavors();

        $this->assertEquals(
            $expectedResult,
            $result,
            'Flavor array was not properly alphabetized'
        );
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

        $domainFlavor     = $this->getDomainFlavor();
        $domainFlavorType = $this->getDomainFlavorType();
        $entityFlavorType = new Entity\FlavorType();

        $flavorTypeId = 1;
        $entityFlavorType->setId($flavorTypeId);

        $domainFlavorType->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($entityFlavorType));

        $this->setService('domainFlavorType', $domainFlavorType);

        $dataset = array(
            'name'       => 'test name',
            'flavorType' => 1,
        );

        $detailArray = array();

        $domainFlavor->create($dataset, $detailArray);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::create
     */
    public function createThrowsExceptionOnFlavorTypeNoExist()
    {
        $this->setExpectedException(
            '\Yumilicious\Exception\Domain\Flavor',
            'Flavor type was not found'
        );

        $domainFlavor       = $this->getDomainFlavor();
        $domainFlavorType   = $this->getDomainFlavorType();
        $domainFlavorDetail = $this->getDomainFlavorDetail();

        $getOneByIdReturn = false;
        $dataset = array('flavorType' => 'test');
        $domainFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($dataset['flavorType'])
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
            'name'       => 'test name',
            'flavorType' => 1,
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
            'name'       => 'test name',
            'flavorType' => 1,
        );

        $detailArray = array();

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
    public function updateFromArrayThrowsExceptionOnValidateFailure()
    {
        $this->setExpectedException(
            '\Yumilicious\Exception\Domain',
            'updatedBy - This value should not be blank.<br />'
        );

        $dataset = array(
            'name' => 'test name',
        );

        $domainFlavor = $this->getDomainFlavor();

        $domainFlavor->updateFromArray($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::updateFromArray
     */
    public function updateFromArrayReturnsEntityOnSuccess()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();
        $entityFlavor = $this->getEntityFlavor();

        $validateReturn = array();
        $entityFlavor->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validateReturn));

        $updateReturn = true;
        $daoFlavor->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $this->setService('daoFlavor', $daoFlavor)
             ->setService('entityFlavor', $entityFlavor);

        $dataset = array('name' => 'test name');

        /** @var $result Entity\Flavor */
        $result = $domainFlavor->updateFromArray($dataset);

        $this->assertEquals(
            $dataset['name'],
            $result->getName(),
            'Entity getName() does not match expected'
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
     */
    public function getOneByIdEntityOnSuccess()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getOneByIdReturn = array('name' => 'test name');
        $daoFlavor->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($getOneByIdReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $id = 123;
        /** @var $result Entity\Flavor */
        $result = $domainFlavor->getOneById($id);

        $this->assertEquals(
            $getOneByIdReturn['name'],
            $result->getName(),
            '::getName() does not match expected'
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
        $daoFlavor->expects($this->once())
            ->method('getAllActive')
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
        $daoFlavor->expects($this->once())
            ->method('getAllInactive')
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
     * @covers \Yumilicious\Domain\Flavor::getall
     */
    public function getAllReturnsEntityOnSuccess()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getAllActiveReturn = array(
            array('name' => 'test name 1'),
            array('name' => 'test name 2'),
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
            $getAllActiveReturn[1]['name'],
            $result[1]->getName(),
            'Expected second entity to match name'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getYogurtFlavors
     */
    public function getYogurtFlavorsReturnsExpected()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getYogurtFlavorsReturn = array('flavor test name');
        $daoFlavor->expects($this->once())
            ->method('getYogurtFlavors')
            ->will($this->returnValue($getYogurtFlavorsReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $this->assertEquals(
            $getYogurtFlavorsReturn,
            $domainFlavor->getYogurtFlavors(),
            'Returned yogurt flavors does not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getSortedYogurtFlavors
     * @covers \Yumilicious\Domain\Flavor::sort_flavors
     */
    public function getSortedYogurtFlavorsReturnsExpected()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $yogurtFlavors = array(
            array('name' => 'zflavor name',),
            array('name' => 'dflavor name',),
            array('name' => 'aflavor name',),
        );

        $expectedResult = array(
            array('name' => 'aflavor name',),
            array('name' => 'dflavor name',),
            array('name' => 'zflavor name',),
        );

        $daoFlavor->expects($this->once())
            ->method('getYogurtFlavors')
            ->will($this->returnValue($yogurtFlavors));

        $this->setService('daoFlavor', $daoFlavor);

        $this->assertEquals(
            $expectedResult,
            $domainFlavor->getSortedYogurtFlavors(),
            'Returned yogurt flavors was not sorted'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getBeverages
     */
    public function getBeveragesReturnsExpected()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getBeveragesFlavorsReturn = array('beverage test name');
        $daoFlavor->expects($this->once())
            ->method('getBeverages')
            ->will($this->returnValue($getBeveragesFlavorsReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $this->assertEquals(
            $getBeveragesFlavorsReturn,
            $domainFlavor->getBeverages(),
            'Returned beverages do not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getFreshFruitToppings
     */
    public function getFreshFruitToppingsReturnsExpected()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getFreshFruitToppingsReturn = array('fruit test name');
        $daoFlavor->expects($this->once())
            ->method('getFreshFruitToppings')
            ->will($this->returnValue($getFreshFruitToppingsReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $this->assertEquals(
            $getFreshFruitToppingsReturn,
            $domainFlavor->getFreshFruitToppings(),
            'Returned beverages do not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getDryToppings
     */
    public function getDryToppingsReturnsExpected()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getDryToppingsReturn = array('dry fruit test name');
        $daoFlavor->expects($this->once())
            ->method('getDryToppings')
            ->will($this->returnValue($getDryToppingsReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $this->assertEquals(
            $getDryToppingsReturn,
            $domainFlavor->getDryToppings(),
            'Returned beverages do not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getLightSyrupToppings
     */
    public function getLightSyrupToppingsReturnsExpected()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getLightSyrupToppingsReturn = array('dry fruit test name');
        $daoFlavor->expects($this->once())
            ->method('getLightSyrupToppings')
            ->will($this->returnValue($getLightSyrupToppingsReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $this->assertEquals(
            $getLightSyrupToppingsReturn,
            $domainFlavor->getLightSyrupToppings(),
            'Returned beverages do not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getExtraFlavorKeys
     */
    public function getExtraFlavorKeysReturnsExpected()
    {
        $domainFlavor = $this->getDomainFlavor();
        $daoFlavor    = $this->getDaoFlavor();

        $getExtraFlavorKeysReturn = array('extra flavor test name');
        $daoFlavor->expects($this->once())
            ->method('getExtraFlavorKeys')
            ->will($this->returnValue($getExtraFlavorKeysReturn));

        $this->setService('daoFlavor', $daoFlavor);

        $this->assertEquals(
            $getExtraFlavorKeysReturn,
            $domainFlavor->getExtraFlavorKeys(),
            'Returned beverages do not match expected'
        );
    }
}