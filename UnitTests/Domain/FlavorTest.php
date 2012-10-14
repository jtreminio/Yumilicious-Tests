<?php

namespace Yumilicious\UnitTests\Domain;

use Yumilicious\UnitTests\Base;
use Yumilicious\Domain\Flavor;

class FlavorTest extends Base
{
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
        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->disableOriginalConstructor()
            ->getMock();

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

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor Flavor */
        $domainFlavor = $this->app['domainFlavor'];

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

        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->disableOriginalConstructor()
            ->getMock();

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

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor Flavor */
        $domainFlavor = $this->app['domainFlavor'];

        $domainFlavor->getFlavorByName($invalidFlavorName);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getSortedYogurtFlavors
     */
    public function getSortedYogurtFlavorsReturnsAlphabetizedList()
    {
        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->disableOriginalConstructor()
            ->getMock();

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

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor Flavor */
        $domainFlavor = $this->app['domainFlavor'];

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

        $dataset = array(
            'name' => 'test name',
        );

        /** @var $domainFlavor Flavor */
        $domainFlavor = $this->app['domainFlavor'];

        $domainFlavor->create($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::create
     */
    public function createReturnsFalseOnDaoCreateFailed()
    {
        $entityFlavor = $this->getMockBuilder('\Yumilicious\Entity\Flavor')
            ->getMock();

        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

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
        );

        $this->app['entityFlavor'] = $entityFlavor;
        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor Flavor */
        $domainFlavor = $this->app['domainFlavor'];

        $this->assertFalse(
            $domainFlavor->create($dataset),
            'Expected return to be false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::create
     */
    public function createReturnsEntityOnSuccess()
    {
        $entityFlavor = $this->getMockBuilder('\Yumilicious\Entity\Flavor')
            ->setMethods(array('validate'))
            ->getMock();

        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $validateReturn = array();
        $entityFlavor->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validateReturn));

        $createReturn = true;
        $daoFlavor->expects($this->once())
            ->method('create')
            ->will($this->returnValue($createReturn));

        $dataset = array(
            'name' => 'test name',
        );

        $this->app['entityFlavor'] = $entityFlavor;
        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor Flavor */
        $domainFlavor = $this->app['domainFlavor'];

        $result = $domainFlavor->create($dataset);

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
        /** @var $entityFlavor \Yumilicious\Entity\Flavor */
        $entityFlavor = new \Yumilicious\Entity\Flavor();

        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $updateReturn = false;
        $daoFlavor->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

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
        /** @var $entityFlavor \Yumilicious\Entity\Flavor */
        $entityFlavor = new \Yumilicious\Entity\Flavor();

        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $entityName = 'test name';
        $entityFlavor->setName($entityName);

        $updateReturn = true;
        $daoFlavor->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

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

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

        $domainFlavor->updateFromArray($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::updateFromArray
     */
    public function updateFromArrayReturnsEntityOnSuccess()
    {
        $entityFlavor = $this->getMockBuilder('\Yumilicious\Entity\Flavor')
            ->setMethods(array('validate'))
            ->getMock();

        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $validateReturn = array();
        $entityFlavor->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validateReturn));

        $updateReturn = true;
        $daoFlavor->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $this->app['entityFlavor'] = $entityFlavor;
        $this->app['daoFlavor'] = $daoFlavor;

        $dataset = array('name' => 'test name');

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

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
        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $getOneByIdReturn = array();
        $daoFlavor->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($getOneByIdReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

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
        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $getOneByIdReturn = array('name' => 'test name');
        $daoFlavor->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($getOneByIdReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

        $id = 123;
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
        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $getAllActiveReturn = array();
        $daoFlavor->expects($this->once())
            ->method('getAllActive')
            ->will($this->returnValue($getAllActiveReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        $status = 'active';

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

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
        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $getAllActiveReturn = array();
        $daoFlavor->expects($this->once())
            ->method('getAllInactive')
            ->will($this->returnValue($getAllActiveReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        $status = 'inactive';

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

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
        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $getAllActiveReturn = array(
            array('name' => 'test name 1'),
            array('name' => 'test name 2'),
        );
        $daoFlavor->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue($getAllActiveReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        $status = 'fauxStatus';

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

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
     * @dataProvider providerToggleActivationReturnsExpected
     * @covers \Yumilicious\Domain\Flavor::toggleActivation
     */
    public function toggleActivationReturnsExpected(
        $isActiveStatus,
        $expectedIsActiveResult
    ){
        $entityFlavor = new \Yumilicious\Entity\Flavor();
        $entityFlavor->setIsActive($isActiveStatus);

        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $updateReturn = true;
        $daoFlavor->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

        $result = $domainFlavor->toggleActivation($entityFlavor);

        $this->assertEquals(
            $expectedIsActiveResult,
            $result->getIsActive()
        );
    }

    /**
     * Provider for toggleActivationReturnsExpected()
     *
     * @return array
     */
    public function providerToggleActivationReturnsExpected()
    {
        return array(
            array(1, 0),
            array(0, 1),
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getYogurtFlavors
     */
    public function getYogurtFlavorsReturnsExpected()
    {
        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $getYogurtFlavorsReturn = array('flavor test name');
        $daoFlavor->expects($this->once())
            ->method('getYogurtFlavors')
            ->will($this->returnValue($getYogurtFlavorsReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

        $this->assertEquals(
            $getYogurtFlavorsReturn,
            $domainFlavor->getYogurtFlavors(),
            'Returned yogurt flavors does not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getSortedYogurtFlavors
     */
    public function getSortedYogurtFlavorsReturnsExpected()
    {
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

        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $daoFlavor->expects($this->once())
            ->method('getYogurtFlavors')
            ->will($this->returnValue($yogurtFlavors));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

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
        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $getBeveragesFlavorsReturn = array('beverage test name');
        $daoFlavor->expects($this->once())
            ->method('getBeverages')
            ->will($this->returnValue($getBeveragesFlavorsReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

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
        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $getFreshFruitToppingsReturn = array('fruit test name');
        $daoFlavor->expects($this->once())
            ->method('getFreshFruitToppings')
            ->will($this->returnValue($getFreshFruitToppingsReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

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
        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $getDryToppingsReturn = array('dry fruit test name');
        $daoFlavor->expects($this->once())
            ->method('getDryToppings')
            ->will($this->returnValue($getDryToppingsReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

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
        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $getLightSyrupToppingsReturn = array('dry fruit test name');
        $daoFlavor->expects($this->once())
            ->method('getLightSyrupToppings')
            ->will($this->returnValue($getLightSyrupToppingsReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

        $this->assertEquals(
            $getLightSyrupToppingsReturn,
            $domainFlavor->getLightSyrupToppings(),
            'Returned beverages do not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\Flavor::getExtraFlavorKeys
     * @group me
     */
    public function getExtraFlavorKeysReturnsExpected()
    {
        $daoFlavor = $this->getMockBuilder('\Yumilicious\Dao\Flavor')
            ->getMock();

        $getExtraFlavorKeysReturn = array('extra flavor test name');
        $daoFlavor->expects($this->once())
            ->method('getExtraFlavorKeys')
            ->will($this->returnValue($getExtraFlavorKeysReturn));

        $this->app['daoFlavor'] = $daoFlavor;

        /** @var $domainFlavor \Yumilicious\Domain\Flavor */
        $domainFlavor = $this->app['domainFlavor'];

        $this->assertEquals(
            $getExtraFlavorKeysReturn,
            $domainFlavor->getExtraFlavorKeys(),
            'Returned beverages do not match expected'
        );
    }
}