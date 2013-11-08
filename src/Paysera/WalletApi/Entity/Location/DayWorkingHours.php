<?php

/**
 * Paysera_WalletApi_Entity_Location_DayWorkingHours
 */
class Paysera_WalletApi_Entity_Location_DayWorkingHours
{
    const DAY_MONDAY = 'monday';
    const DAY_TUESDAY = 'tuesday';
    const DAY_WEDNESDAY = 'wednesday';
    const DAY_THURSDAY = 'thursday';
    const DAY_FRIDAY = 'friday';
    const DAY_SATURDAY = 'saturday';
    const DAY_SUNDAY = 'sunday';

    /**
     * @var int
     */
    protected $day;

    /**
     * @var Paysera_WalletApi_Entity_Time
     */
    protected $openingTime;

    /**
     * @var Paysera_WalletApi_Entity_Time
     */
    protected $closingTime;

    /**
     * Set closingTime
     *
     * @param \Paysera_WalletApi_Entity_Time $closingTime
     *
     * @return Paysera_WalletApi_Entity_Location_DayWorkingHours
     */
    public function setClosingTime($closingTime)
    {
        $this->closingTime = $closingTime;

        return $this;
    }

    /**
     * Get closingTime
     *
     * @return \Paysera_WalletApi_Entity_Time
     */
    public function getClosingTime()
    {
        return $this->closingTime;
    }

    /**
     * Set openingTime
     *
     * @param \Paysera_WalletApi_Entity_Time $openingTime
     *
     * @return Paysera_WalletApi_Entity_Location_DayWorkingHours
     */
    public function setOpeningTime($openingTime)
    {
        $this->openingTime = $openingTime;

        return $this;
    }

    /**
     * Get openingTime
     *
     * @return \Paysera_WalletApi_Entity_Time
     */
    public function getOpeningTime()
    {
        return $this->openingTime;
    }

    /**
     * Get day
     *
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set day
     *
     * @param int $day
     *
     * @return Paysera_WalletApi_Entity_Location_DayWorkingHours
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Mark as monday
     *
     * @return Paysera_WalletApi_Entity_Location_DayWorkingHours
     */
    public function markAsMonday()
    {
        return $this->setDay(self::DAY_MONDAY);
    }

    /**
     * Mark as tuesday
     *
     * @return Paysera_WalletApi_Entity_Location_DayWorkingHours
     */
    public function markAsTuesday()
    {
        return $this->setDay(self::DAY_TUESDAY);
    }

    /**
     * Mark as wednesday
     *
     * @return Paysera_WalletApi_Entity_Location_DayWorkingHours
     */
    public function markAsWednesday()
    {
        return $this->setDay(self::DAY_WEDNESDAY);
    }

    /**
     * Mark as thursday
     *
     * @return Paysera_WalletApi_Entity_Location_DayWorkingHours
     */
    public function markAsThursday()
    {
        return $this->setDay(self::DAY_THURSDAY);
    }

    /**
     * Mark as friday
     *
     * @return Paysera_WalletApi_Entity_Location_DayWorkingHours
     */
    public function markAsFriday()
    {
        return $this->setDay(self::DAY_FRIDAY);
    }

    /**
     * Mark as saturday
     *
     * @return Paysera_WalletApi_Entity_Location_DayWorkingHours
     */
    public function markAsSaturday()
    {
        return $this->setDay(self::DAY_SATURDAY);
    }

    /**
     * Mark as sunday
     *
     * @return Paysera_WalletApi_Entity_Location_DayWorkingHours
     */
    public function markAsSunday()
    {
        return $this->setDay(self::DAY_SUNDAY);
    }

    /**
     * Creates object, used for fluent interface
     *
     * @return self
     */
    public static function create()
    {
        return new static();
    }
}
