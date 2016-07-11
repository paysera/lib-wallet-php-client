<?php

namespace Paysera\WalletApi;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    public function testMapperJoinsLocationSearchFilterStatusesArray()
    {
        $filter = new \Paysera_WalletApi_Entity_Location_SearchFilter();
        $filter->setStatuses(array('a','b'));

        $mapper = new \Paysera_WalletApi_Mapper();
        $encoded = $mapper->encodeLocationFilter($filter);

        $statuses = explode(',', $encoded['status']);
        $this->assertCount(2, $statuses);
        $this->assertContains('a', $statuses);
        $this->assertContains('b', $statuses);
    }

    public function testIdentityMapperEncoding()
    {
        $identity = (new \Paysera_WalletApi_Entity_User_Identity())
            ->setName('Name')
            ->setSurname("Surname")
            ->setCode(9999999)
            ->setNationality("LT")
        ;

        $result = (new \Paysera_WalletApi_Mapper_IdentityMapper())->mapToArray($identity);

        $this->assertSame($identity->getName(), $result['name']);
        $this->assertSame($identity->getSurname(), $result['surname']);
        $this->assertSame($identity->getCode(), $result['code']);
        $this->assertSame($identity->getNationality(), $result['nationality']);
    }

    public function testIdentityMapperDecoding()
    {
        $identity = array (
            'name' => 'Name',
            'surname' => 'Surname',
            'code' => 9999999,
            'nationality' => 'LT'
        );

        $result = (new \Paysera_WalletApi_Mapper_IdentityMapper())->mapToEntity($identity);

        $this->assertSame($identity['name'], $result->getName());
        $this->assertSame($identity['surname'], $result->getSurname());
        $this->assertSame($identity['code'], $result->getCode());
        $this->assertSame($identity['nationality'], $result->getNationality());
    }
}
