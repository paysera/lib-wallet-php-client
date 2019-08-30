<?php
/**
 * Created by: Gediminas Samulis
 * Date: 2014-02-25
 */

class Paysera_WalletApi_Service_LocationManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Paysera_WalletApi_Service_LocationManager
     */
    protected $service;

    public function setUp()
    {
        $this->service = new Paysera_WalletApi_Service_LocationManager();
    }

    /**
     * @dataProvider dataProviderForTestIsLocationOpen1
     */
    public function testIsLocationOpen_working_hours_exist($expected, $date)
    {
        $location = new Paysera_WalletApi_Entity_Location();

        $workingHours1 = new Paysera_WalletApi_Entity_Location_DayWorkingHours();
        $workingHours1->setDay(Paysera_WalletApi_Entity_Location_DayWorkingHours::DAY_MONDAY);
        $workingHours1->setOpeningTime(new Paysera_WalletApi_Entity_Time(9, 0));
        $workingHours1->setClosingTime(new Paysera_WalletApi_Entity_Time(18, 0));

        $workingHours2 = new Paysera_WalletApi_Entity_Location_DayWorkingHours();
        $workingHours2->setDay(Paysera_WalletApi_Entity_Location_DayWorkingHours::DAY_TUESDAY);
        $workingHours2->setOpeningTime(new Paysera_WalletApi_Entity_Time(9, 0));
        $workingHours2->setClosingTime(new Paysera_WalletApi_Entity_Time(18, 0));

        $workingHours4 = new Paysera_WalletApi_Entity_Location_DayWorkingHours();
        $workingHours4->setDay(Paysera_WalletApi_Entity_Location_DayWorkingHours::DAY_THURSDAY);
        $workingHours4->setOpeningTime(new Paysera_WalletApi_Entity_Time(9, 0));
        $workingHours4->setClosingTime(new Paysera_WalletApi_Entity_Time(18, 0));

        $workingHours5 = new Paysera_WalletApi_Entity_Location_DayWorkingHours();
        $workingHours5->setDay(Paysera_WalletApi_Entity_Location_DayWorkingHours::DAY_FRIDAY);
        $workingHours5->setOpeningTime(new Paysera_WalletApi_Entity_Time(22, 0));
        $workingHours5->setClosingTime(new Paysera_WalletApi_Entity_Time(3, 0));

        $workingHours7 = new Paysera_WalletApi_Entity_Location_DayWorkingHours();
        $workingHours7->setDay(Paysera_WalletApi_Entity_Location_DayWorkingHours::DAY_SUNDAY);
        $workingHours7->setOpeningTime(new Paysera_WalletApi_Entity_Time(0, 0));
        $workingHours7->setClosingTime(new Paysera_WalletApi_Entity_Time(0, 0));

        $location->setWorkingHours(array($workingHours1, $workingHours2, $workingHours4, $workingHours5, $workingHours7));

        $this->assertEquals($expected, $this->service->isLocationOpen($location, $date));
    }

    /**
     * @dataProvider dataProviderForTestIsLocationClosed
     */
    public function testIsLocationClosed_working_hours_empty($expected, $date)
    {
        $location = new Paysera_WalletApi_Entity_Location();

        //working days not defined at all:
        $location->setWorkingHours(array());
        //any day, any time:
        $this->assertEquals($expected, $this->service->isLocationOpen($location, $date));
    }

    public function dataProviderForTestIsLocationOpen1()
    {
        return array(
            //monday:
            array(true, new DateTime('2014-02-24 10:00')),
            array(true, new DateTime('2014-02-24 17:00')),
            array(true, new DateTime('2014-02-24 18:00')),
            array(false, new DateTime('2014-02-24 08:00')),
            array(false, new DateTime('2014-02-24 08:59')),
            array(false, new DateTime('2014-02-24 18:01')),
            //wednesday:
            array(false, new DateTime('2014-02-26 02:00')),
            array(false, new DateTime('2014-02-26 10:00')),
            array(false, new DateTime('2014-02-26 17:00')),
            //friday:
            array(true, new DateTime('2014-02-28 23:00')),
            array(true, new DateTime('2014-03-01 01:00')),
            array(true, new DateTime('2014-03-01 03:00')),
            array(false, new DateTime('2014-02-28 21:00')),
            array(false, new DateTime('2014-03-01 04:00')),
            //saturday (not defined day)
            array(false, new DateTime('2014-03-01 10:00')),
            //sunday:
            array(false, new DateTime('2014-03-01 23:59')),
            array(true, new DateTime('2014-03-02 00:00')),
            array(true, new DateTime('2014-03-02 10:00')),
            array(true, new DateTime('2014-03-03 00:00')),
            array(false, new DateTime('2014-03-03 01:00')),
        );
    }

    public function dataProviderForTestIsLocationClosed()
    {
        return array(
            array(false, new \DateTime('2014-03-01 10:00')),
            array(false, new \DateTime('2014-03-03 03:00')),
        );
    }

}
