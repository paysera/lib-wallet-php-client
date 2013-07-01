<?php

/**
 * Entity representing Balance in the Wallet
 */
class Paysera_WalletApi_Entity_Wallet_Balance
{
    /**
     * @var array
     */
    protected $balanceAtDisposal;

    /**
     * @var array
     */
    protected $reserved;

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
     * Gets all currently available currencies for this balance
     *
     * @return string[]
     */
    public function getCurrencies()
    {
        return array_keys($this->balanceAtDisposal + $this->reserved);
    }
}