<?php

/**
 * Entity representing Limit for Allowance
 */
class Paysera_WalletApi_Entity_Limit
{
    /**
     * @var Paysera_WalletApi_Entity_Money
     */
    protected $maxPrice;

    /**
     * @var integer time in seconds
     */
    protected $time;

    /**
     * Creates object, used for fluent interface
     *
     * @return self
     */
    static public function create()
    {
        return new self();
    }

    /**
     * Sets maxPrice
     *
     * @param Paysera_WalletApi_Entity_Money $maxPrice

     * @return self
     */
    public function setMaxPrice(Paysera_WalletApi_Entity_Money $maxPrice)
    {
        $this->maxPrice = $maxPrice;
        return $this;
    }

    /**
     * Gets maxPrice
     *
     * @return Paysera_WalletApi_Entity_Money
     */
    public function getMaxPrice()
    {
        return $this->maxPrice;
    }

    /**
     * @param integer $time

     * @return self
     */
    public function setTime($time)
    {
        Paysera_WalletApi_Util_Assert::isInt($time);
        $this->time = intval($time);
        return $this;
    }

    /**
     * @return integer
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Sets period
     *
     * @param integer $period

     * @return self
     *
     * @deprecated use setTime()
     */
    public function setPeriod($period)
    {
        Paysera_WalletApi_Util_Assert::isInt($period);
        $this->time = intval($period) * 3600;
        return $this;
    }

    /**
     * Gets period
     *
     * @return integer
     *
     * @deprecated use getTime()
     */
    public function getPeriod()
    {
        return $this->time / 3600;
    }
}
