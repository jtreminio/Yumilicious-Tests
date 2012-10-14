<?php

namespace Yumilicious\UnitTests\Domain;

use Yumilicious\UnitTests\Base;
use Yumilicious\Domain;
use Yumilicious\Entity;
use Yumilicious\Validator;

class PersonAccountTest extends Base
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDomainPersonAccount()
    {
        return $this->getMockBuilder('\Yumilicious\Domain\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('password_hash'))
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDaoPersonAccount()
    {
        return $this->getMockBuilder('\Yumilicious\Dao\PersonAccount')
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEntityPersonAccount()
    {
        return $entityPersonAccount = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->setMethods(array('validate'))
            ->getMock();
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::create
     */
    public function createReturnsFalseOnAccountNotCreated()
    {
        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        $domainPersonAccount = $this->getDomainPersonAccount();

        $daoPersonAccount = $this->getDaoPersonAccount();
        $entityPersonAccount = $this->getEntityPersonAccount();

        $validateReturn = array();
        $entityPersonAccount->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($validateReturn));

        $createReturn = false;
        $daoPersonAccount->expects($this->once())
            ->method('create')
            ->will($this->returnValue($createReturn));

        $this->app['entityPersonAccount'] = $entityPersonAccount;
        $this->app['daoPersonAccount'] = $daoPersonAccount;

        $dataSet = array(
            'password'       => 'blah123',
            'passwordVerify' => 'blah123',
        );

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        $this->assertFalse(
            $domainPersonAccount->create($dataSet),
            'Expected ::create() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::create
     */
    public function createThrowsExceptionWhenPasswordsDoNotMatch()
    {
        $expectedException = 'Password must be entered exactly the same twice';

        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            $expectedException
        );

        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        $domainPersonAccount = $this->getDomainPersonAccount();

        $dataSet = array(
            'password'       => 'blah',
            'passwordVerify' => 'blah123',
        );

        $domainPersonAccount->create($dataSet);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::create
     */
    public function createThrowsExceptionOnValidationErrors()
    {
        $expectedException =
            'updatedBy - This value should not be blank.<br />' .
            'email - This value should not be blank.<br />' .
            'displayName - This value should not be blank.<br />';

        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            $expectedException
        );

        $domainPersonAccount = $this->getDomainPersonAccount();

        $dataSet = array(
            'password'       => '$2y$blah',
            'passwordVerify' => '$2y$blah',
        );

        $domainPersonAccount->expects($this->once())
            ->method('password_hash')
            ->will($this->returnValue($dataSet['password']));

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        $domainPersonAccount->create($dataSet);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::create
     */
    public function createReturnsEntityOnSuccess()
    {
        $domainPersonAccount = $this->getDomainPersonAccount();

        $daoPersonAccount = $this->getDaoPersonAccount();

        $createdId = 321;
        $daoPersonAccount->expects($this->once())
            ->method('create')
            ->will($this->returnValue($createdId));

        $updatedBy = 123;
        $dataSet = array(
            'email'          => 'blah@blah.com',
            'password'       => 'blah',
            'passwordVerify' => 'blah',
            'displayName'    => 'Test Name',
            'isActive'       => 1,
            'updatedBy'      => $updatedBy,
        );

        $hashedPassword = '$2y$'.$dataSet['password'];
        $domainPersonAccount->expects($this->once())
            ->method('password_hash')
            ->will($this->returnValue($hashedPassword));

        $this->app['daoPersonAccount'] = $daoPersonAccount;

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        /** @var $entity Entity\PersonAccount */
        $entity = $domainPersonAccount->create($dataSet);

        $this->assertEquals(
            $createdId,
            $entity->getId(),
            'Created Id does not match expected'
        );

        $this->assertEquals(
            $dataSet['email'],
            $entity->getEmail(),
            'Entity email does not match expected'
        );

        $this->assertEquals(
            $dataSet['displayName'],
            $entity->getDisplayName(),
            'Entity display name does not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::update
     */
    public function updateReturnsFalseOnNoUpdate()
    {
        $domainPersonAccount = $this->getDomainPersonAccount();
        $daoPersonAccount = $this->getDaoPersonAccount();

        $entityPersonAccount = new \Yumilicious\Entity\PersonAccount();
        $entityPersonAccount->setPassword('abc');

        $updateValue = false;
        $daoPersonAccount->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateValue));

        $this->app['daoPersonAccount'] = $daoPersonAccount;

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        $this->assertFalse(
            $domainPersonAccount->update($entityPersonAccount, true),
            'Expecting ::update() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::update
     */
    public function updateReturnsEntityOnSuccess()
    {
        $domainPersonAccount = $this->getDomainPersonAccount();
        $daoPersonAccount = $this->getDaoPersonAccount();

        $displayName = 'test name';
        $entityPersonAccount = new \Yumilicious\Entity\PersonAccount();
        $entityPersonAccount->setDisplayName($displayName);
        $entityPersonAccount->setPassword('abc');

        $updateValue = true;
        $daoPersonAccount->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateValue));

        $this->app['daoPersonAccount'] = $daoPersonAccount;

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        $result = $domainPersonAccount->update($entityPersonAccount, true);

        $this->assertEquals(
            $displayName,
            $result->getDisplayName(),
            'Entity getDisplayName does not equal expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::updateFromArray
     */
    public function updateFromArrayThrowsExceptionOnPasswordVerifyNotSetWhenPasswordIsSet()
    {
        $this->setExpectedException(
            '\Yumilicious\Exception\Domain',
            'Password must be entered exactly the same twice'
        );

        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        $domainPersonAccount = $this->getDomainPersonAccount();

        $dataset = array('password' => 'fubar');

        $domainPersonAccount->updateFromArray($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::updateFromArray
     */
    public function updateFromArrayThrowsExceptionOnPasswordsNotMatch()
    {
        $this->setExpectedException(
            '\Yumilicious\Exception\Domain',
            'Password must be entered exactly the same twice'
        );

        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        $domainPersonAccount = $this->getDomainPersonAccount();

        $dataset = array(
            'password'       => 'fubar',
            'passwordVerify' => 'rabuf',
        );

        $domainPersonAccount->updateFromArray($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::updateFromArray
     */
    public function updateFromArrayThrowsExceptionOnValidateFailed()
    {
        $this->setExpectedException(
            '\Yumilicious\Exception\Domain',
            'updatedBy - This value should not be blank.<br />'
        );

        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        $domainPersonAccount = $this->getDomainPersonAccount();

        $dataset = array(
            'displayName'    => 'test name',
            'password'       => '$2y$blah',
            'passwordVerify' => '$2y$blah',
            'email'          => 'test@email.com',
        );

        $domainPersonAccount->expects($this->once())
            ->method('password_hash')
            ->will($this->returnValue($dataset['password']));

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        $domainPersonAccount->updateFromArray($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::updateFromArray
     */
    public function updateFromArrayReturnsEntityOnSuccess()
    {
        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        $domainPersonAccount = $this->getDomainPersonAccount();

        $daoPersonAccount = $this->getDaoPersonAccount();

        $updateValue = true;
        $daoPersonAccount->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateValue));

        $dataset = array(
            'displayName'    => 'test name',
            'password'       => '$2y$blah',
            'passwordVerify' => '$2y$blah',
            'email'          => 'test@email.com',
            'updatedBy'      => '7',
        );

        $domainPersonAccount->expects($this->once())
            ->method('password_hash')
            ->will($this->returnValue($dataset['password']));

        $this->app['daoPersonAccount'] = $daoPersonAccount;

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        $result = $domainPersonAccount->updateFromArray($dataset);

        $this->assertEquals(
            $dataset['displayName'],
            $result->getDisplayName(),
            'Display names do not match'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::getOneById
     */
    public function getOneByIdReturnsEntityOnFound()
    {
        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        $domainPersonAccount = $this->getDomainPersonAccount();

        $daoPersonAccount = $this->getDaoPersonAccount();

        $personId = 123;
        $email = 'foo@bar.com';

        $account = array(
            'id'    => $personId,
            'email' => $email,
        );

        $daoPersonAccount->expects($this->once())
            ->method('getOneById')
            ->with($personId)
            ->will($this->returnValue($account));

        $this->app['daoPersonAccount'] = $daoPersonAccount;

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        $result = $domainPersonAccount->getOneById($personId);

        $this->assertEquals(
            $account['id'],
            $result->getId(),
            'person id value does not match expected'
        );

        $this->assertEquals(
            $account['email'],
            $result->getEmail(),
            'email value does not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::getPersonByEmailAndPassword
     */
    public function getPersonByEmailAndPasswordReturnsFalseOnEmailNotFound()
    {
        $domainPersonAccount = $this->getDomainPersonAccount();

        $daoPersonAccount = $this->getDaoPersonAccount();

        $entityPersonAccount = $this->getEntityPersonAccount();

        $email = 'test@test.com';
        $accountFound = false;
        $daoPersonAccount->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->will($this->returnValue($accountFound));

        $domainPersonAccount->expects($this->never())
            ->method('password_verify');

        $entityPersonAccount->expects($this->never())
            ->method('hydrate');

        $this->app['daoPersonAccount'] = $daoPersonAccount;
        $this->app['entityPersonAccount'] = $entityPersonAccount;

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        $password = 'foobar';

        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        $this->assertFalse(
            $domainPersonAccount->getPersonByEmailAndPassword($email, $password),
            'Expecting return false '
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::getPersonByEmailAndPassword
     */
    public function getPersonByEmailAndPasswordReturnsFalseOnMismatchedPasswords()
    {
        $email = 'test@test.com';
        $password = 'foobar';

        $domainPersonAccount = $this->getMockBuilder('\Yumilicious\Domain\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('password_verify',))
            ->getMock();

        $daoPersonAccount = $this->getDaoPersonAccount();

        $entityPersonAccount = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->getMock();

        $expectedPersonAccount = array('password' => 'BADPASSWORD');

        $daoPersonAccount->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->will($this->returnValue($expectedPersonAccount));

        $passwordMatches = false;
        $domainPersonAccount->expects($this->once())
            ->method('password_verify')
            ->with($password, $expectedPersonAccount['password'])
            ->will($this->returnValue($passwordMatches));

        $entityPersonAccount->expects($this->never())
            ->method('hydrate');

        $this->app['daoPersonAccount'] = $daoPersonAccount;
        $this->app['entityPersonAccount'] = $entityPersonAccount;

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        $this->assertFalse(
            $domainPersonAccount->getPersonByEmailAndPassword($email, $password),
            'Expected Domain\PersonAccount::password_verify to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::getPersonByEmailAndPassword
     */
    public function getPersonByEmailAndPasswordReturnsEntityOnSuccess()
    {
        $email = 'test@test.com';
        $password = 'foobar';

        $domainPersonAccount = $this->getMockBuilder('\Yumilicious\Domain\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('password_verify',))
            ->getMock();

        $daoPersonAccount = $this->getDaoPersonAccount();

        $expectedPersonAccount = array(
            'email'    => $email,
            'password' => $password,
        );

        $daoPersonAccount->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->will($this->returnValue($expectedPersonAccount));

        $domainPersonAccount->expects($this->once())
            ->method('password_verify')
            ->with($password, $expectedPersonAccount['password'])
            ->will($this->returnValue(true));

        $this->app['daoPersonAccount'] = $daoPersonAccount;

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        /** @var $domainPersonAccount \Yumilicious\Domain\PersonAccount */
        $result = $domainPersonAccount->getPersonByEmailAndPassword($email, $password);

        $this->assertEquals(
            $email,
            $result->getEmail(),
            'email value does not match expected'
        );

        $this->assertEquals(
            $password,
            $result->getPassword(),
            'password value does not match expected'
        );
    }
}