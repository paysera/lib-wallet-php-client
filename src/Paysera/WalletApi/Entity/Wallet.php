<?php

/**
 * Entity representing Wallet
 */
class Paysera_WalletApi_Entity_Wallet
{
    /**
     * @var integer
     * @readonly
     */
    protected $id;

    /**
     * @var integer
     * @readonly
     */
    protected $owner;

    /**
     * @var Paysera_WalletApi_Entity_Wallet_Account
     * @readonly
     */
    protected $account;

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
     * Gets id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets owner
     *
     * @return integer
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return Paysera_WalletApi_Entity_Wallet_Account
     */
    public function getAccount()
    {
        return $this->account;
    }

}