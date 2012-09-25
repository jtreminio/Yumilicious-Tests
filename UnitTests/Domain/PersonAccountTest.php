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
            '"email" - This value should not be blank.'."\n" .
            '"password" - This value should not be blank.'."\n" .
            '"displayName" - This value should not be blank.'."\n" .
            '"createdBy" - This value should not be blank.'."\n";

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
     * @covers \Yumilicious\Domain\PersonAccount::getPersonByEmailAndPassword
     */
    public function getPersonByEmailAndPasswordReturnsFalseOnEmailNotFound()
    {
        $email = 'test@test.com';
        $password = 'foobar';

        $domainPersonAccount = $this->getMockBuilder('\Yumilicious\Domain\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                     '__construct',
                     'password_verify',
                )
            )
            ->getMock();

        $daoPersonAccount = $this->getMockBuilder('\Yumilicious\Dao\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('getByEmail'))
            ->getMock();

        $entityPersonAccount = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('hydrate'))
            ->getMock();

        $daoPersonAccount->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->will($this->returnValue(false));

        $domainPersonAccount->expects($this->never())
            ->method('password_verify');

        $entityPersonAccount->expects($this->never())
            ->method('hydrate');

        $this->setAttribute($domainPersonAccount, 'app', $this->app);

        $this->app['domainPersonAccount'] = $domainPersonAccount;
        $this->app['daoPersonAccount'] = $daoPersonAccount;
        $this->app['entityPersonAccount'] = $entityPersonAccount;

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
            ->setMethods(
                array(
                     '__construct',
                     'password_verify',
                )
            )
            ->getMock();

        $daoPersonAccount = $this->getMockBuilder('\Yumilicious\Dao\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('getByEmail'))
            ->getMock();

        $entityPersonAccount = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('hydrate'))
            ->getMock();

        $expectedPersonAccount = array('password' => 'BADPASSWORD');

        $daoPersonAccount->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->will($this->returnValue($expectedPersonAccount));

        $domainPersonAccount->expects($this->once())
            ->method('password_verify')
            ->with($password, $expectedPersonAccount['password'])
            ->will($this->returnValue(false));

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
     */
    public function getPersonByEmailAndPasswordReturnsEntityOnSuccess()
    {
        $email = 'test@test.com';
        $password = 'foobar';

        $domainPersonAccount = $this->getMockBuilder('\Yumilicious\Domain\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                     '__construct',
                     'password_verify',
                )
            )
            ->getMock();

        $daoPersonAccount = $this->getMockBuilder('\Yumilicious\Dao\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('getByEmail'))
            ->getMock();

        $entityPersonAccount = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('hydrate'))
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