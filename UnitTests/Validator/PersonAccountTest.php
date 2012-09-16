<?php

namespace Yumilicious\UnitTests\Validator;

use Yumilicious\UnitTests\Base;
use Yumilicious\Validator\PersonAccount;

class PersonAccountTest extends Base
{

    /**
     * @test
     */
    public function setConstraintsPassesWithMinimumAttributes()
    {
        $entityPersonAccount = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('getCalledClass'))
            ->getMock();

        /** @var $validatorPersonAccount \Yumilicious\Validator\PersonAccount */
        $validatorPersonAccount = $this->app['validatorPersonAccount'];

        $calledClass = '\Yumilicious\Entity\PersonAccount';

        $entityPersonAccount->expects($this->once())
            ->method('getCalledClass')
            ->will($this->returnValue($calledClass));

        $entityPersonAccount->setValidator($validatorPersonAccount);

        $dataSet = array(
            'email'    => 'foo@foo.com',
            'password' => '$2y$fiosifhiajff',
            'displayName' => 'Foo Name',
            'createdBy'   => '123'
        );

        $entityPersonAccount->hydrate($dataSet);

        $errors = $entityPersonAccount->validate($entityPersonAccount);
        $expectedErrorCount = 0;

        $this->assertCount(
            $expectedErrorCount,
            $errors,
            'Expected no errors returned'
        );
    }

    /**
     * @test
     */
    public function setConstraintsFailsWithBadMinimumAttributes()
    {
        $entityPersonAccount = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('getCalledClass'))
            ->getMock();

        /** @var $validatorPersonAccount \Yumilicious\Validator\PersonAccount */
        $validatorPersonAccount = $this->app['validatorPersonAccount'];

        $calledClass = '\Yumilicious\Entity\PersonAccount';

        $entityPersonAccount->expects($this->once())
            ->method('getCalledClass')
            ->will($this->returnValue($calledClass));

        $entityPersonAccount->setValidator($validatorPersonAccount);

        $dataSet = array();

        $entityPersonAccount->hydrate($dataSet);

        $errors = $entityPersonAccount->validate($entityPersonAccount);
        $expectedErrorCount = 4;

        $this->assertCount(
            $expectedErrorCount,
            $errors,
            'Expected no errors returned'
        );
    }

}