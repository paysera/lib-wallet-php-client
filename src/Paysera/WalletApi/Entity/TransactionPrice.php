<?php

class Paysera_WalletApi_Entity_TransactionPrice
{
    /**
     * @var int
     */
    protected $paymentId;

    /**
     * @var Paysera_WalletApi_Entity_Money
     */
    protected $price;

    /**
     * Creates object, used for fluent interface
     *
     * @return self
     */
    static public function create()
    {
        return new static();
    }

    /**
     * Sets paymentId
     *
     * @param int $paymentId
     *
     * @return self
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
        return $this;
    }

    /**
     * Gets paymentId
     *
     * @return int
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * Sets price
     *
     * @param Paysera_WalletApi_Entity_Money $price

     * @return self
     */
    public function setPrice(Paysera_WalletApi_Entity_Money $price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Gets price
     *
     * @return Paysera_WalletApi_Entity_Money
     */
    public function getPrice()
    {
        return $this->price;
    }
}