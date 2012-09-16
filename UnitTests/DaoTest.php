<?php

namespace Yumilicious\UnitTests;

class DaoTest extends Base
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

        $entityPersonAccount = $this->getMockBuilder('\Yumilicious\Entity\PersonAccount')
            ->disableOriginalConstructor()
            ->setMethods(array('__construct'))
            ->getMock();

        $dao->setEntity($entityPersonAccount);

        $dbResults = array(
            'personId' => 1,
            'email'    => 'test@email.com',
            'password' => 'test',
            'displayName' => 'Barney Rubble',
        );

        /** @var $results \Yumilicious\Entity\PersonAccount */
        $results = $this->invokeMethod(
            $dao,
            'hydrate',
            array(
                 $dbResults,
                 $entityPersonAccount
            )
        );

        $this->assertEquals(
            $dbResults['personId'],
            $results->getPersonId(),
            'PersonId field does not match expected'
        );

        $this->assertEquals(
            $dbResults['email'],
            $results->getEmail(),
            'Email field does not match expected'
        );

        $this->assertEquals(
            $dbResults['password'],
            $results->getPassword(),
            'Password field does not match expected'
        );

        $this->assertEquals(
            $dbResults['displayName'],
            $results->getDisplayName(),
            'DisplayName field does not match expected'
        );
    }

}