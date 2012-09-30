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
    public function createThrowsExceptionOnValidationErrors()
    {
        $expectedException =
            'email - This value should not be blank.<br />' .
            'password - This value should not be blank.<br />' .
            'displayName - This value should not be blank.<br />' .
            'createdBy - This value should not be blank.<br />';

        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            $expectedException
        );

        $dataSet = array();

        /** @var $domainPersonAccount Domain\PersonAccount */
        $domainPersonAccount = $this->app['domainPersonAccount'];
        $domainPersonAccount->create($dataSet);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::getByPersonId
     */
    public function getByPersonIdReturnsEntityOnFound()
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
            'personId' => $personId,
            'email'    => $email,
        );

        $daoPersonAccount->expects($this->once())
            ->method('getOneByPersonId')
            ->with($personId)
            ->will($this->returnValue($account));

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        $this->app['daoPersonAccount'] = $daoPersonAccount;

        $result = $domainPersonAccount->getByPersonId($personId);

        $this->assertEquals(
            $account['personId'],
            $result->getPersonId(),
            'personId value does not match expected'
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

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        $this->app['domainPersonAccount'] = $domainPersonAccount;
        $this->app['daoPersonAccount'] = $daoPersonAccount;
        $this->app['entityPersonAccount'] = $entityPersonAccount;

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

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        $this->app['domainPersonAccount'] = $domainPersonAccount;
        $this->app['daoPersonAccount'] = $daoPersonAccount;
        $this->app['entityPersonAccount'] = $entityPersonAccount;

        $this->assertFalse(
            $domainPersonAccount->getPersonByEmailAndPassword($email, $password),
            'Expected Domain\PersonAccount::password_verify to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\PersonAccount::getPersonByEmailAndPassword
     *
     * @todo Rename this to returning entity and verify
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

        $entityPersonAccount = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
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

        $entityPersonAccount->expects($this->once())
            ->method('hydrate')
            ->with($expectedPersonAccount)
            ->will($this->returnValue(true));

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        $this->app['domainPersonAccount'] = $domainPersonAccount;
        $this->app['daoPersonAccount'] = $daoPersonAccount;
        $this->app['entityPersonAccount'] = $entityPersonAccount;

        $this->assertTrue(
            $domainPersonAccount->getPersonByEmailAndPassword($email, $password),
            'Expecting return true'
        );
    }
}