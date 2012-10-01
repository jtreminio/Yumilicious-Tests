<?php

namespace Yumilicious\IntegrationTests\Dao;

use Yumilicious\IntegrationTests\Base;

class LocationTest extends Base
{
    /**
     * @test
     * @covers \Yumilicious\Dao\Location::create
     */
    public function createReturnsInsertedId()
    {
        /** @var $daoLocation \Yumilicious\Dao\Location */
        $daoLocation = $this->app['daoLocation'];

        /** @var $entity \Yumilicious\Entity\Location */
        $entity = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->setMethods(null)
            ->getMock();

        $sampleData = $this->_createSampleData();

        $entity->hydrate($sampleData);

        $insertedId = $daoLocation->create($entity);

        $fetchedRecord = $daoLocation->getOneById($insertedId);

        $this->assertEquals(
            $entity->getName(),
            $fetchedRecord['name'],
            'Fetched name does not match expected'
        );

        $this->assertEquals(
            $entity->getEmail(),
            $fetchedRecord['email'],
            'Fetched email does not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Dao\Location::create
     */
    public function updateUpdatesRecord()
    {
        /** @var $daoLocation \Yumilicious\Dao\Location */
        $daoLocation = $this->app['daoLocation'];

        /** @var $entity \Yumilicious\Entity\Location */
        $entity = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->setMethods(null)
            ->getMock();

        $sampleData = $this->_createSampleData();

        $entity->hydrate($sampleData);

        $entity->setId($daoLocation->create($entity));

        $entity->setName('Updated Name');

        $daoLocation->update($entity);

        $fetchedRecord = $daoLocation->getOneById($entity->getId());

        $this->assertEquals(
            $entity->getName(),
            $fetchedRecord['name'],
            'Fetched name does not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Dao\Location::reorder
     */
    public function reorderSetsCorrectOrderingNumbers()
    {
        /** @var $daoLocation \Yumilicious\Dao\Location */
        $daoLocation = $this->app['daoLocation'];

        /** @var $entityOne \Yumilicious\Entity\Location */
        $entityOne = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->setMethods(null)
            ->getMock();

        /** @var $entityTwo \Yumilicious\Entity\Location */
        $entityTwo = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->setMethods(null)
            ->getMock();

        /** @var $entityThree \Yumilicious\Entity\Location */
        $entityThree = $this->getMockBuilder('\Yumilicious\Entity\Location')
            ->setMethods(null)
            ->getMock();

        $sampleDataOne = $this->_createSampleData();
        $sampleDataTwo = $this->_createSampleData();
        $sampleDataThree = $this->_createSampleData();

        $sampleDataOne['state'] = 'ZA';
        $sampleDataTwo['state'] = 'ZA';
        $sampleDataThree['state'] = 'ZA';

        $sampleDataOne['ordering'] = '10';
        $sampleDataTwo['ordering'] = '20';
        $sampleDataThree['ordering'] = '30';

        $entityOne->hydrate($sampleDataOne);
        $entityTwo->hydrate($sampleDataTwo);
        $entityThree->hydrate($sampleDataThree);

        $entityOne->setId($daoLocation->create($entityOne));
        $entityTwo->setId($daoLocation->create($entityTwo));
        $entityThree->setId($daoLocation->create($entityThree));

        $entityThree->setOrdering(5);

        $daoLocation->reorder($entityThree);

        $resultOne = $daoLocation->getOneById($entityOne->getId());
        $resultTwo = $daoLocation->getOneById($entityTwo->getId());
        $resultThree = $daoLocation->getOneById($entityThree->getId());

        $expectedResultOneOrdering = 20;
        $expectedResultTwoOrdering = 30;
        $expectedResultThreeOrdering = 10;

        $this->assertEquals(
            $expectedResultOneOrdering,
            $resultOne['ordering'],
            'Ordering value of result one not as expected'
        );

        $this->assertEquals(
            $expectedResultTwoOrdering,
            $resultTwo['ordering'],
            'Ordering value of result two not as expected'
        );

        $this->assertEquals(
            $expectedResultThreeOrdering,
            $resultThree['ordering'],
            'Ordering value of result three not as expected'
        );
    }

    /**
     * Create sample location data
     *
     * @return array
     */
    protected function _createSampleData()
    {
        $date = new \DateTime();

        $createdAt = $date->format('Y-m-d H:i:s');
        $createdBy = mt_rand(1234567890, 9999999999);

        return array(
            'id'        => mt_rand(123, 999),
            'ordering'  => mt_rand(12345, 99999),
            'name'      => 'Test Name'.uniqid('', true),
            'subTitle'  => 'Test SubTitle'.uniqid('', true),
            'address'   => 'Test Address'.uniqid('', true),
            'city'      => 'Test City'.uniqid('', true),
            'state'     => substr(uniqid('', true), 0, 2),
            'zipCode'   => mt_rand(12345, 99999),
            'extraInfo' => 'Extra Info'.uniqid('', true),
            'phone'     => mt_rand(12345, 99999),
            'email'     => 'Email'.uniqid('', true).'@email.com',
            'isActive'  => 1,
            'createdAt' => $createdAt,
            'createdBy' => $createdBy,
            'updatedAt' => $createdAt,
            'updatedBy' => $createdBy,
        );
    }
}