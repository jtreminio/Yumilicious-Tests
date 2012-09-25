<?php

namespace Yumilicious\UnitTests;

use Yumilicious\Entity;
use \DateTime;

class EntityTest extends Base
{

    /**
     * @test
     */
    public function hydrateReturnsExpectedValues()
    {
        /** @var $dao \Yumilicious\Dao */
        $dao = $this->getMockBuilder('\Yumilicious\Dao')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $entityPersonAccount = new \Yumilicious\Entity\PersonAccount;

        $dao->setEntity($entityPersonAccount);

        $dbResults = array(
            'personId' => 1,
            'email'    => 'test@email.com',
            'password' => 'test',
            'displayName' => 'Barney Rubble',
        );

        $entityPersonAccount->hydrate($dbResults);

        $this->assertEquals(
            $dbResults['personId'],
            $entityPersonAccount->getPersonId(),
            'PersonId field does not match expected'
        );

        $this->assertEquals(
            $dbResults['email'],
            $entityPersonAccount->getEmail(),
            'Email field does not match expected'
        );

        $this->assertEquals(
            $dbResults['password'],
            $entityPersonAccount->getPassword(),
            'Password field does not match expected'
        );

        $this->assertEquals(
            $dbResults['displayName'],
            $entityPersonAccount->getDisplayName(),
            'DisplayName field does not match expected'
        );
    }

    /**
     * @test
     */
    public function entityClassThrowsExceptionWhenCallingUndefinedMethod()
    {
        $entity = $this->getMockBuilder('\Yumilicious\Entity')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $className = get_class($entity);
        $method = 'fooBar';

        $this->setExpectedException(
            'Yumilicious\Exception\Entity',
            "Attempting to set or get non-existant value: {$className}::{$method}"
        );

        $entity->$method();
    }

    /**
     * @test
     */
    public function handleDATETIMEReturnsNullWhenDateStringAllZeros()
    {
        $entity = $this->getMockBuilder('\Yumilicious\Entity')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $badDateString = '0000-00-00 00:00:00';

        $result = $this->invokeMethod(
            $entity,
            'handleDATETIME',
            array($badDateString)
        );

        $this->assertNull(
            $result,
            'Expected NULL'
        );
    }

    /**
     * @test
     */
    public function handleDATETIMEReturnsCorrectlyFormattedStringOnValidDateTimeString()
    {
        $entity = $this->getMockBuilder('\Yumilicious\Entity')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $salmaHayekBirthday = '1966-09-02 00:00:00';

        $expectedResult = new DateTime($salmaHayekBirthday);
        $expectedResult = $expectedResult->format('Y-m-d H:i:s');

        $result = $this->invokeMethod(
            $entity,
            'handleDATETIME',
            array($salmaHayekBirthday)
        );

        $this->assertEquals(
            $result,
            $expectedResult,
            'Resulting DateTime string not matching expected'
        );
    }

    /**
     * @test
     */
    public function handleDATETIMEReturnsCorrectlyFormattedStringOnValidDateTimeObject()
    {
        $entity = $this->getMockBuilder('\Yumilicious\Entity')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $salmaHayekBirthday = new DateTime('1966-09-02 00:00:00');

        $expectedResult = $salmaHayekBirthday->format('Y-m-d H:i:s');

        $result = $this->invokeMethod(
            $entity,
            'handleDATETIME',
            array($salmaHayekBirthday)
        );

        $this->assertEquals(
            $result,
            $expectedResult,
            'Resulting DateTime string not matching expected'
        );
    }

}