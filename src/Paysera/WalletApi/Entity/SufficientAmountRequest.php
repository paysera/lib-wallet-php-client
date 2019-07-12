<?php

class Paysera_WalletApi_Entity_SufficientAmountRequest
{
    /**
     * @var Paysera_WalletApi_Entity_Money
     */
    private $amount;

    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param Paysera_WalletApi_Entity_Money $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }
}
