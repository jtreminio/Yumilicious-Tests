<?php

namespace Yumilicious\UnitTests\Domain;

use Yumilicious\UnitTests\Base;
use Yumilicious\Domain;
use Yumilicious\Entity;
use Yumilicious\Validator;

class PersonAccountTest extends Base
{
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

        $domainPersonAccount = $this->getMockBuilder('\Yumilicious\Domain\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

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
            'email - This value should not be blank.<br />' .
            'displayName - This value should not be blank.<br />' .
            'createdBy - This value should not be blank.<br />';

        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            $expectedException
        );

        $domainPersonAccount = $this->getMockBuilder('\Yumilicious\Domain\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('password_hash',))
            ->getMock();

        $password_hashResult = '$2y$blah';
        $domainPersonAccount->expects($this->once())
            ->method('password_hash')
            ->will($this->returnValue($password_hashResult));

        $dataSet = array(
            'password'       => 'blah',
            'passwordVerify' => 'blah',
        );

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        $domainPersonAccount->create($dataSet);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::create
     */
    public function createReturnsEntityOnSuccess()
    {
        $domainPersonAccount = $this->getMockBuilder('\Yumilicious\Domain\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('password_hash'))
            ->getMock();

        $daoPersonAccount = $this->getMockBuilder('\Yumilicious\Dao\PersonAccount')
            ->disableOriginalConstructor()
            ->getMock();

        $createdId = 321;
        $daoPersonAccount->expects($this->once())
            ->method('create')
            ->will($this->returnValue($createdId));

        $createdBy = 123;
        $dataSet = array(
            'email'          => 'blah@blah.com',
            'password'       => 'blah',
            'passwordVerify' => 'blah',
            'displayName'    => 'Test Name',
            'isActive'       => 1,
            'createdBy'      => $createdBy,
        );

        $hashedPassword = '$2y$'.$dataSet['password'];
        $domainPersonAccount->expects($this->once())
            ->method('password_hash')
            ->will($this->returnValue($hashedPassword));

        $this->app['daoPersonAccount'] = $daoPersonAccount;

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

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
     * @covers \Yumilicious\Domain\PersonAccount::getById
     */
    public function getOneByIdReturnsEntityOnFound()
    {
        $domainPersonAccount = $this->getMockBuilder('\Yumilicious\Domain\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('password_verify',))
            ->getMock();

        $daoPersonAccount = $this->getMockBuilder('\Yumilicious\Dao\PersonAccount')
            ->disableOriginalConstructor()
            ->getMock();

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
        $domainPersonAccount = $this->getMockBuilder('\Yumilicious\Domain\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('password_verify',))
            ->getMock();

        $daoPersonAccount = $this->getMockBuilder('\Yumilicious\Dao\PersonAccount')
            ->disableOriginalConstructor()
            ->getMock();

        $entityPersonAccount = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->disableOriginalConstructor()
            ->getMock();

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

        $daoPersonAccount = $this->getMockBuilder('\Yumilicious\Dao\PersonAccount')
            ->disableOriginalConstructor()
            ->getMock();

        $entityPersonAccount = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->disableOriginalConstructor()
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

        $daoPersonAccount = $this->getMockBuilder('\Yumilicious\Dao\PersonAccount')
            ->disableOriginalConstructor()
            ->getMock();

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