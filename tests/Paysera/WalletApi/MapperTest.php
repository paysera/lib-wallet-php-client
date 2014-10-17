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
}
