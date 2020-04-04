<?php

/**
 * Entity representing Balance in the Wallet
 */
class Paysera_WalletApi_Entity_Wallet_Balance
{
    /**
     * @var array
     */
    protected $balanceAtDisposal = array();

    /**
     * @var array
     */
    protected $reserved = array();

    protected $balanceAtDisposalDecimal = array();

    protected $reservedDecimal = array();

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
     * Sets balance for some currency. Overwrites any previous balance of same currency
     *
     * @param string  $currency
     * @param integer $amountAtDisposal
     * @param integer $amountReserved
     *
     * @return self
     */
    public function setCurrencyBalance($currency, $amountAtDisposal, $amountReserved)
    {
        $this->balanceAtDisposal[$currency] = $amountAtDisposal;
        $this->reserved[$currency] = $amountReserved;
        return $this;
    }

    /**
     * Sets balance for some currency. Overwrites any previous balance of same currency
     *
     * @param string $currency
     * @param string|null $amountAtDisposalDecimal
     * @param string|null $amountReservedDecimal
     *
     * @return self
     */
    public function setCurrencyBalanceDecimal($currency, $amountAtDisposalDecimal, $amountReservedDecimal)
    {
        $this->balanceAtDisposalDecimal[$currency] = $amountAtDisposalDecimal;
        $this->reservedDecimal[$currency] = $amountReservedDecimal;
        return $this;
    }

    /**
     * Gets balance at disposal for provided currency in cents
     *
     * @param string $currency
     *
     * @return integer
     */
    public function getBalanceAtDisposal($currency)
    {
        return isset($this->balanceAtDisposal[$currency]) ? $this->balanceAtDisposal[$currency] : 0;
    }

    /**
     * Gets balance at disposal for provided currency
     *
     * @param string $currency
     *
     * @return string|null
     */
    public function getBalanceAtDisposalDecimal($currency)
    {
        return isset($this->balanceAtDisposalDecimal[$currency]) ? $this->balanceAtDisposalDecimal[$currency] : null;
    }


    /**
     * Gets reserved amount for provided currency in cents
     *
     * @param string $currency
     *
     * @return integer
     */
    public function getReserved($currency)
    {
        return isset($this->reserved[$currency]) ? $this->reserved[$currency] : 0;
    }

    /**
     * Gets reserved amount for provided currency
     *
     * @param string $currency
     *
     * @return string|null
     */
    public function getReservedDecimal($currency)
    {
        return isset($this->reservedDecimal[$currency]) ? $this->reservedDecimal[$currency] : null;
    }


    /**
     * Gets all currently available currencies for this balance
     *
     * @return string[]
     */
    public function getCurrencies()
    {
        return array_keys($this->balanceAtDisposal + $this->reserved);
    }
}
