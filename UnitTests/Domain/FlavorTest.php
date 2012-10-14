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
}