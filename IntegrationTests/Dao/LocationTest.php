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