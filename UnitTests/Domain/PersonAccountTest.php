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
        $domainPersonAccount->create(
            $dataSet,
            $this->app['entityPersonAccount']
        );
    }

}