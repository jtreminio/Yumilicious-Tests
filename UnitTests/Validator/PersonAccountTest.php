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
        /** @var $validatorPersonAccount \Yumilicious\Validator\PersonAccount */
        $validatorPersonAccount = $this->app['validatorPersonAccount'];

        $entityPersonAccount = new \Yumilicious\Entity\PersonAccount();
        $entityPersonAccount->setEmail('test@test.com')
            ->setPassword('$2y$5ihgrGEjgwewg')
            ->setDisplayName('Test Name')
            ->setCreatedBy('123');

        $errors = $validatorPersonAccount->validate($entityPersonAccount);
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
        /** @var $validatorPersonAccount \Yumilicious\Validator\PersonAccount */
        $validatorPersonAccount = $this->app['validatorPersonAccount'];

        $entityPersonAccount = new \Yumilicious\Entity\PersonAccount();

        $errors = $validatorPersonAccount->validate($entityPersonAccount);
        $expectedErrorCount = 4;

        $this->assertCount(
            $expectedErrorCount,
            $errors,
            "Expected {$expectedErrorCount} errors returned"
        );
    }

}