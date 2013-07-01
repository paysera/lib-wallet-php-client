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
     * @var integer    period in hours
     */
    protected $period;

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
     * Sets period
     *
     * @param integer $period

     * @return self
     */
    public function setPeriod($period)
    {
        Paysera_WalletApi_Util_Assert::isInt($period);
        $this->period = intval($period);
        return $this;
    }

    /**
     * Gets period
     *
     * @return integer
     */
    public function getPeriod()
    {
        return $this->period;
    }

}