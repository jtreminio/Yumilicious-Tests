<?php

namespace Yumilicious\UnitTests;

use Yumilicious\Entity;
use \DateTime;

class EntityTest extends Base
{

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
    public function handleDATETIMEReturnsDateTimeObjectOnValidDateTimeString()
    {
        $entity = $this->getMockBuilder('\Yumilicious\Entity')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $salmaHayekBirthday = '1966-09-02 00:00:00';

        $expectedResult = new DateTime($salmaHayekBirthday);
        $expectedResult = $expectedResult->format(DateTime::ISO8601);

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
    public function handleDATETIMEReturnsDateTimeObjectOnValidDateTimeObject()
    {
        $entity = $this->getMockBuilder('\Yumilicious\Entity')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $salmaHayekBirthday = new DateTime('1966-09-02 00:00:00');

        $expectedResult = $salmaHayekBirthday->format(DateTime::ISO8601);

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