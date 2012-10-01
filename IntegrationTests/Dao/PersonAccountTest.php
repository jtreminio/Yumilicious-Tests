<?php

namespace Yumilicious\IntegrationTests\Dao;

use Yumilicious\IntegrationTests\Base;

class PersonAccountTest extends Base
{
    /**
     * @test
     * @covers \Yumilicious\Dao\PersonAccount::create
     */
    public function createInsertsNewRecord()
    {
        $daoPersonAccount = $this->app['daoPersonAccount'];

        /** @var $entity \Yumilicious\Entity\PersonAccount */
        $entity = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->setMethods(null)
            ->getMock();

        $sampleData = $this->_createSampleData();

        $entity->hydrate($sampleData);

        $insertedId = $daoPersonAccount->create($entity);

        $fetchedRecord = $daoPersonAccount->getOneById($insertedId);

        $this->assertEquals(
            $entity->getEmail(),
            $fetchedRecord['email'],
            'Returned email does not match expected'
        );

        $this->assertEquals(
            $entity->getPassword(),
            $fetchedRecord['password'],
            'Returned password does not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Dao\PersonAccount::create
     */
    public function createReturnsFalseOnFailure()
    {
        $db = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->setMethods(array('executeQuery'))
            ->getMock();

        /** @var $entity \Yumilicious\Entity\PersonAccount */
        $entity = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->setMethods(null)
            ->getMock();

        $executeQuery = false;
        $db->expects($this->once())
            ->method('executeQuery')
            ->will($this->returnValue($executeQuery));

        $sampleData = $this->_createSampleData();

        $entity->hydrate($sampleData);

        $daoPersonAccount = new \Yumilicious\Dao\PersonAccount();
        $daoPersonAccount->setDb($db);

        $this->assertFalse(
            $daoPersonAccount->create($entity),
            'Expected false returned'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Dao\PersonAccount::getByEmail
     */
    public function getByEmailReturnsExpected()
    {
        $daoPersonAccount = $this->app['daoPersonAccount'];

        /** @var $entity \Yumilicious\Entity\PersonAccount */
        $entity = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->setMethods(null)
            ->getMock();

        $sampleData = $this->_createSampleData();

        $entity->hydrate($sampleData);

        $daoPersonAccount->create($entity);

        $result = $daoPersonAccount->getByEmail($entity->getEmail());

        $this->assertEquals(
            $entity->getEmail(),
            $result['email'],
            'Emails do not match'
        );

        $this->assertEquals(
            $entity->getDisplayName(),
            $result['displayName'],
            'Display names do not match'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Dao\PersonAccount::getById
     */
    public function getByIdReturnsExpected()
    {
        $daoPersonAccount = $this->app['daoPersonAccount'];

        /** @var $entity \Yumilicious\Entity\PersonAccount */
        $entity = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->setMethods(null)
            ->getMock();

        $sampleData = $this->_createSampleData();

        $entity->hydrate($sampleData);

        $insertedId = $daoPersonAccount->create($entity);

        $fetchedRecord = $daoPersonAccount->getOneById($insertedId);

        $this->assertEquals(
            $entity->getEmail(),
            $fetchedRecord['email'],
            'Returned email does not match expected'
        );

        $this->assertEquals(
            $entity->getPassword(),
            $fetchedRecord['password'],
            'Returned password does not match expected'
        );
    }

    /**
     * Create sample user data
     *
     * @return array
     */
    protected function _createSampleData()
    {
        $date = new \DateTime();

        $createdAt = $date->format('Y-m-d H:i:s');
        $createdBy = mt_rand(1234567890, 9999999999);

        return array(
            'id' => mt_rand(1234567890, 9999999999),
            'email' => uniqid('', true).'@email.com',
            'password' => uniqid('password', true),
            'displayName' => 'Test User'.uniqid('', true),
            'lastLoginAt' => '',
            'passwordUpdatedAt' => '',
            'failedLoginAttempts' => '',
            'lockoutStartTime' => '',
            'unreadNotificationCount' => 2,
            'isActive' => 1,
            'createdAt' => $createdAt,
            'createdBy' => $createdBy,
            'updatedAt' => $createdAt,
            'updatedBy' => $createdBy,
        );
    }
}