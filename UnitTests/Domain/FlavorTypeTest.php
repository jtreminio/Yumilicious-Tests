<?php

namespace Yumilicious\UnitTests\Domain;

use Yumilicious\UnitTests\Base;
use Yumilicious\Domain;
use Yumilicious\Entity;
use Yumilicious\Validator;

class FlavorTypeTest extends Base
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDaoFlavorType()
    {
        return $this->getMockBuilder('\Yumilicious\Dao\FlavorType')
            ->getMock();
    }

    /**
     * @return Domain\FlavorType|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDomainFlavorType()
    {
        return $this->getMockBuilder('\Yumilicious\Domain\FlavorType')
            ->setConstructorArgs(array($this->app))
            ->setMethods(null)
            ->getMock();
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::create
     * @covers \Yumilicious\Domain\FlavorType::typeExists
     */
    public function createThrowsExceptionOnParentNotExist()
    {
        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            'Selected flavor type parent does not exist'
        );

        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $dataset = array('parentId' => 123);

        $getOneByIdReturn = false;
        $daoFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($dataset['parentId'])
            ->will($this->returnValue($getOneByIdReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $domainFlavorType->create($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::create
     * @covers \Yumilicious\Domain\FlavorType::typeExists
     * @covers \Yumilicious\Domain\FlavorType::nameExists
     */
    public function createThrowsExceptionOnNameExists()
    {
        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            'Selected flavor type name is already in use'
        );

        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $dataset = array(
            'parentId' => 123,
            'name'     => 'test name'
        );

        $getOneByIdReturn = array('name' => 'Parent Name');
        $daoFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($dataset['parentId'])
            ->will($this->returnValue($getOneByIdReturn));

        $getOneByNameReturn = true;
        $daoFlavorType->expects($this->once())
            ->method('getOneByName')
            ->with($dataset['name'])
            ->will($this->returnValue($getOneByNameReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $domainFlavorType->create($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::create
     * @covers \Yumilicious\Domain\FlavorType::typeExists
     * @covers \Yumilicious\Domain\FlavorType::nameExists
     */
    public function createThrowsExceptionOnValidateFailure()
    {
        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            'updatedBy - This value should not be blank.<br />'
        );

        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $dataset = array(
            'parentId' => 123,
            'name'     => 'test name'
        );

        $getOneByIdReturn = array('name' => 'Parent name');
        $daoFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($dataset['parentId'])
            ->will($this->returnValue($getOneByIdReturn));

        $getOneByNameReturn = false;
        $daoFlavorType->expects($this->once())
            ->method('getOneByName')
            ->with($dataset['name'])
            ->will($this->returnValue($getOneByNameReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $domainFlavorType->create($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::create
     */
    public function createReturnsFalseOnCreateFailure()
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $dataset = array(
            'name'      => 'test name',
            'updatedBy' => 321,
        );

        $createReturn = false;
        $daoFlavorType->expects($this->once())
            ->method('create')
            ->will($this->returnValue($createReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $this->assertFalse(
            $domainFlavorType->create($dataset),
            'Expected ::create() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::create
     * @covers \Yumilicious\Domain\FlavorType::typeExists
     */
    public function createReturnsEntity()
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $dataset = array(
            'parentId' => 123,
            'name'     => 'test name',
            'updatedBy' => 321,
        );

        $getOneByIdReturn = array('name' => 'Parent name');
        $daoFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($dataset['parentId'])
            ->will($this->returnValue($getOneByIdReturn));

        $getOneByNameReturn = false;
        $daoFlavorType->expects($this->once())
            ->method('getOneByName')
            ->with($dataset['name'])
            ->will($this->returnValue($getOneByNameReturn));

        $createReturn = 456;
        $daoFlavorType->expects($this->once())
            ->method('create')
            ->will($this->returnValue($createReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        /** @var $result Entity\FlavorType */
        $result = $domainFlavorType->create($dataset);

        $this->assertEquals(
            $createReturn,
            $result->getId(),
            'Expected entity getId to match'
        );

        $this->assertEquals(
            $dataset['name'],
            $result->getName(),
            'Expected entity getId to match'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::update
     */
    public function updateReturnsFalseOnFailedUpdate()
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $updateReturn = false;
        $daoFlavorType->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $entityId = 123;
        $parentId = 321;

        $entityFlavorType       = new Entity\FlavorType();
        $entityFlavorTypeParent = new Entity\FlavorType();

        $entityFlavorTypeParent->setId($parentId);

        $entityFlavorType->setId($entityId);
        $entityFlavorType->setParent($entityFlavorTypeParent);

        $this->assertFalse(
            $domainFlavorType->update($entityFlavorType),
            'Expected ::update() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::update
     */
    public function updateThrowsExceptionOnIdAndParentIdSame()
    {
        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            'Flavor type parent cannot be itself'
        );

        $domainFlavorType = $this->getDomainFlavorType();

        $entityId = 123;
        $parentId = 123;

        $entityFlavorType       = new Entity\FlavorType();
        $entityFlavorTypeParent = new Entity\FlavorType();

        $entityFlavorTypeParent->setId($parentId);

        $entityFlavorType->setId($entityId);
        $entityFlavorType->setParent($entityFlavorTypeParent);

        $domainFlavorType->update($entityFlavorType);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::update
     */
    public function updateReturnsEntity()
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $updateReturn = true;
        $daoFlavorType->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $entityId = 123;
        $entityFlavorType = new Entity\FlavorType();
        $entityFlavorType->setId($entityId);

        /** @var $result Entity\FlavorType */
        $result = $domainFlavorType->update($entityFlavorType);

        $this->assertEquals(
            $result->getId(),
            $entityId,
            'Entity id did not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::updateFromArray
     */
    public function updateFromArrayThrowsErrorOnValidateFailure()
    {
        $this->setExpectedException(
            'Yumilicious\Exception\Domain',
            'updatedBy - This value should not be blank.<br />'
        );

        $domainFlavorType = $this->getDomainFlavorType();

        $dataset = array(
            'id'   => 123,
            'name' => 'test name',
        );

        $domainFlavorType->updateFromArray($dataset);
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::updateFromArray
     */
    public function updateFromArrayReturnsEntity()
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $updateReturn = true;
        $daoFlavorType->expects($this->once())
            ->method('update')
            ->will($this->returnValue($updateReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $dataset = array(
            'id'        => 123,
            'name'      => 'test name',
            'updatedBy' => 321,
        );

        /** @var $result Entity\FlavorType */
        $result = $domainFlavorType->updateFromArray($dataset);

        $this->assertEquals(
            $result->getId(),
            $dataset['id'],
            'Entity id did not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::getOneById
     */
    public function getOneByIdReturnsFalseOnNoResult()
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $getOneByIdReturn = array();
        $daoFlavorType->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($getOneByIdReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $id = 123;
        $this->assertFalse(
            $domainFlavorType->getOneById($id),
            'Expected ::getOneById() to return false'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::getOneById
     */
    public function getOneByIdReturnsEntity()
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $getOneByIdReturn = array(
            'id'   => 123,
            'name' => 'test name 1',
        );
        $daoFlavorType->expects($this->once())
            ->method('getOneById')
            ->will($this->returnValue($getOneByIdReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $id = 123;

        /** @var $result Entity\FlavorType */
        $result = $domainFlavorType->getOneById($id);

        $this->assertEquals(
            $getOneByIdReturn['id'],
            $result->getId(),
            'getId() did not match expected'
        );

        $this->assertEquals(
            $getOneByIdReturn['name'],
            $result->getName(),
            'getName() did not match expected'
        );
    }

    /**
     * @test
     * @dataProvider providerGetAllReturnsFalseOnNoResults
     * @covers \Yumilicious\Domain\FlavorType::getAll
     */
    public function getAllReturnsFalseOnNoResults($status, $getAllParameter)
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $getAll = array();
        $daoFlavorType->expects($this->once())
            ->method('getAllByActive')
            ->with($getAllParameter)
            ->will($this->returnValue($getAll));

        $this->setService('daoFlavorType', $daoFlavorType);

        $this->assertFalse(
            $domainFlavorType->getAll($status),
            'Expecting ::getAll() to return false'
        );
    }

    /**
     * Provider for getAllReturnsFalseOnNoResults
     *
     * @return array
     */
    public function providerGetAllReturnsFalseOnNoResults()
    {
        return array(
            array('active', 1),
            array('inactive', 0),
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::delete
     * @covers \Yumilicious\Domain\FlavorType::setChildrenParentIdOfDeletedType
     * @covers \Yumilicious\Domain\FlavorType::createNestedFlavorTypeArray
     * @covers \Yumilicious\Domain::removeMatchingArrayKeys
     */
    public function deleteChangesParentIdOfChildrenOfDeletedTypeWhenTypeHasNoParent()
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $deleteReturn = true;
        $daoFlavorType->expects($this->once())
            ->method('delete')
            ->will($this->returnValue($deleteReturn));

        $typeId = 123;
        $typeParentId = null;
        $typeArray = array(
            'id'   => $typeId,
            'name' => 'Test name',
        );

        $daoFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($typeId)
            ->will($this->returnValue($typeArray));

        $daoFlavorType->expects($this->once())
            ->method('updateParentIdOfMultipleChildren')
            ->with($typeId, $typeParentId);

        $this->setService('daoFlavorType', $daoFlavorType);

        $this->assertTrue(
            $domainFlavorType->delete($typeId),
            'Expected ::delete() to return true'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::delete
     * @covers \Yumilicious\Domain\FlavorType::setChildrenParentIdOfDeletedType
     * @covers \Yumilicious\Domain\FlavorType::createNestedFlavorTypeArray
     */
    public function deleteChangesParentIdOfChildrenOfDeletedTypeWhenTypeHasParent()
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $deleteReturn = true;
        $daoFlavorType->expects($this->once())
            ->method('delete')
            ->will($this->returnValue($deleteReturn));

        $typeId = 123;
        $typeParentId = 321;

        $typeArray = array(
            'id'          => $typeId,
            'name'        => 'Test name',
            'parent-id'   => $typeParentId,
            'parent-name' => 'Parent name',
        );

        $daoFlavorType->expects($this->once())
            ->method('getOneById')
            ->with($typeId)
            ->will($this->returnValue($typeArray));

        $daoFlavorType->expects($this->once())
            ->method('updateParentIdOfMultipleChildren')
            ->with($typeId, $typeParentId);

        $this->setService('daoFlavorType', $daoFlavorType);

        $this->assertTrue(
            $domainFlavorType->delete($typeId),
            'Expected ::delete() to return true'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::getAll
     */
    public function getAllReturnsExpected()
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $getAllReturn = array(
            array('name' => 'result 1'),
            array('name' => 'result 2'),
            array('name' => 'result 3'),
        );
        $daoFlavorType->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue($getAllReturn));

        $this->setService('daoFlavorType', $daoFlavorType);

        $status = 'fooStatus';

        $result = $domainFlavorType->getAll($status);

        $this->assertEquals(
            $getAllReturn[0]['name'],
            $result[0]->getName(),
            'getName() does not match expected'
        );

        $this->assertEquals(
            $getAllReturn[1]['name'],
            $result[1]->getName(),
            'getName() does not match expected'
        );

        $this->assertEquals(
            $getAllReturn[2]['name'],
            $result[2]->getName(),
            'getName() does not match expected'
        );
    }

    /**
     * @test
     * @covers \Yumilicious\Domain\FlavorType::getAllFlat
     * @covers \Yumilicious\Domain\FlavorType::flatten
     *
     * Expected result should look similar to
            $elements = array(
                array(
                    'id'   => 1,
                    'name' => 'element 1',
                ),
                    array(
                        'id'       => 3,
                        'name'     => 'element 3',
                        'parentId' => 1,
                    ),
                        array(
                            'id'       => 5,
                            'name'     => 'element 5',
                            'parentId' => 3,
                        ),
                array(
                    'id'   => 2,
                    'name' => 'element 2',
                ),
                    array(
                        'id'       => 4,
                        'name'     => 'element 4',
                        'parentId' => 2,
                    ),
                    array(
                        'id'       => 6,
                        'name'     => 'element 6',
                        'parentId' => 2,
                    ),
            );
     */
    public function getAllFlatReturnsOrderedArray()
    {
        $domainFlavorType = $this->getDomainFlavorType();
        $daoFlavorType    = $this->getDaoFlavorType();

        $elements = array(
            array(
                'id'   => 1,
                'name' => 'element 1',
            ),
            array(
                'id'   => 2,
                'name' => 'element 2',
            ),
            array(
                'id'       => 3,
                'name'     => 'element 3',
                'parentId' => 1,
            ),
            array(
                'id'       => 4,
                'name'     => 'element 4',
                'parentId' => 2,
            ),
            array(
                'id'       => 5,
                'name'     => 'element 5',
                'parentId' => 3,
            ),
            array(
                'id'       => 6,
                'name'     => 'element 6',
                'parentId' => 2,
            ),
        );

        $daoFlavorType->expects($this->once())
            ->method('getAllByActive')
            ->will($this->returnValue($elements));

        $this->setService('daoFlavorType', $daoFlavorType);

        $status = 'active';

        $result = $domainFlavorType->getAllFlat($status);

        $this->assertEquals(
            $elements[0]['name'],
            $result[0]->getName(),
            'First element did not match expected'
        );

        $this->assertContains(
            $elements[2]['name'],
            $result[0]->getChildren()[3]->getName(),
            'First element did not contain expected child'
        );

        $this->assertContains(
            $elements[4]['name'],
            $result[0]->getChildren()[3]->getChildren()[5]->getName(),
            "First element's child did not contain expected child"
        );

        $this->assertEquals(
            $elements[1]['name'],
            $result[3]->getName(),
            'Second element did not match expected'
        );

        $this->assertContains(
            $elements[3]['name'],
            $result[3]->getChildren()[4]->getName(),
            'Second element did not contain expected child'
        );

        $this->assertContains(
            $elements[5]['name'],
            $result[3]->getChildren()[6]->getName(),
            'Second element did not contain expected child'
        );
    }
}