<?php

namespace Yumilicious\UnitTests;

use Yumilicious\Entity;
use \DateTime;

class EntityTest extends Base
{
    /**
     * @test
     * @covers \Yumilicious\Entity::hydrate
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
            'id' => 1,
            'email'    => 'test@email.com',
            'password' => 'test',
            'displayName' => 'Barney Rubble',
        );

        $entityPersonAccount->hydrate($dbResults);

        $this->assertEquals(
            $dbResults['id'],
            $entityPersonAccount->getId(),
            'Person id field does not match expected'
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
     * @covers \Yumilicious\Entity::__call
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
     * @covers \Yumilicious\Entity::handleDATETIME
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
     * @covers \Yumilicious\Entity::handleDATETIME
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
     * @covers \Yumilicious\Entity::handleDATETIME
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

    /**
     * @test
     * @covers \Yumilicious\Entity::sluggify
     * @dataProvider providerSluggifyReturnsSluggifiedString
     */
    public function sluggifyReturnsSluggifiedString(
        $rawString,
        $expectedResult
    )
    {
        $entity = $this->getMockBuilder('\Yumilicious\Entity')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $result = $this->invokeMethod(
            $entity,
            'sluggify',
            array($rawString)
        );

        $this->assertEquals(
            $expectedResult,
            $result,
            'Expected slug did not match actual result'
        );
    }

    /**
     * Provider for sluggifyReturnsSluggifiedString
     */
    public function providersluggifyReturnsSluggifiedString()
    {
        return array(
            array(
                "Mess'd up --text-- just (to) stress /test/ ?our! `little` \\clean\\ url fun.ction!?-->",
                'messd-up-text-just-to-stress-test-our-little-clean-url-function',
            ),
            array(
                "Mess'd up --text-- just (to) stress /test/ ?our! `little` \\clean\\ url fun.ction!?-->Mess'd up --text-- just (to) stress /tes",
                'messd-up-text-just-to-stress-test-our-little-clean-url-function-messd-up-text-just-to'
            ),
            array(
                "Perché l'erba è verde?"."'",
                'perche-lerba-e-verde',
            ),
            array(
                "Peux-tu m'aider s'il te plaît?".",",
                'peux-tu-maider-sil-te-plait',
            ),
            array(
                "Tänk efter nu – förr'n vi föser dig bort",
                'tank-efter-nu-forrn-vi-foser-dig-bort',
            ),
            array(
                "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöùúûüýÿ",
                'aaaaaaaeceeeeiiiinooooouuuuyssaaaaaaaeceeeeiiiinooooouuuuyy',
            ),
            array(
                "My+Last_Crazy|/example",
                'my-last-crazy-example',
            ),
        );
    }

    /**
     * @test
     * @dataProvider providerHandleZeroValueDatesReturnsExpected
     */
    public function handleZeroValueDatesReturnsExpected(
        $dateString,
        $expectedResponse
    ){
        $entity = $this->getMockBuilder('\Yumilicious\Entity')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $result = $this->invokeMethod(
            $entity,
            'handleZeroValueDates',
            array($dateString)
        );

        $this->assertEquals(
            $expectedResponse,
            $result,
            'Result does not match expected'
        );
    }

    /**
     * Provider for handleZeroValueDatesReturnsExpected()
     *
     * @return array
     */
    public function providerHandleZeroValueDatesReturnsExpected()
    {
        return array(
            array(
                '0000-00-00 00:00:00',
                null,
            ),
            array(
                '1995-00-00 00:00:00',
                '1995-00-00 00:00:00',
            ),
        );
    }
}