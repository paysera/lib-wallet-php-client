<?php


class Paysera_WalletApi_Service_LocationManager
{
    /**
     * @param Paysera_WalletApi_Entity_Location $location
     * @param DateTime $date
     * @return bool
     */
    public function isLocationOpen(Paysera_WalletApi_Entity_Location $location, DateTime $date = null)
    {
        if (count($location->getWorkingHours()) === 0) {
            return false;
        }

        if ($date === null) {
            $date = new DateTime();
        }

        $dateYesterday = clone $date;
        $dateYesterday->sub(new DateInterval('P1D'));
        $dayOfWeek = strtolower(date('l', $date->getTimestamp()));
        $dayOfWeekYesterday = strtolower(date('l', $dateYesterday->getTimestamp()));
        foreach ($location->getWorkingHours() as $workingHours) {
            if (
                $workingHours->getDay() === $dayOfWeek
                && $this->isWorkingHoursActiveByDate($workingHours, $date, $date)
            ) {
                return true;
            }
            if (
                $workingHours->getDay() === $dayOfWeekYesterday
                && $this->isWorkingHoursActiveByDate($workingHours, $dateYesterday, $date)
            ) {
                return true;
            }
        }
        return false;
    }

    protected function isWorkingHoursActiveByDate(
        Paysera_WalletApi_Entity_Location_DayWorkingHours $workingHours,
        DateTime $initialDate,
        DateTime $date
    ) {
        $openingDate = clone $initialDate;
        $closingDate = clone $initialDate;
        $openingDate->setTime($workingHours->getOpeningTime()->getHours(), $workingHours->getOpeningTime()->getMinutes());
        $closingDate->setTime($workingHours->getClosingTime()->getHours(), $workingHours->getClosingTime()->getMinutes());
        if ($closingDate <= $openingDate) {
            $closingDate = $closingDate->add(new DateInterval('P1D'));
        }
        return $date >= $openingDate && $date <= $closingDate;
    }

}
