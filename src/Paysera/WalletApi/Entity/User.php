<?php

/**
 * Entity representing User
 */
class Paysera_WalletApi_Entity_User
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var Paysera_WalletApi_Entity_User_Address
     */
    protected $address;

    /**
     * @var Paysera_WalletApi_Entity_User_Identity
     */
    protected $identity;

    /**
     * @var int[]
     */
    protected $wallets;

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
     * @return \Paysera_WalletApi_Entity_User_Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return \Paysera_WalletApi_Entity_User_Identity
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return int[]
     */
    public function getWallets()
    {
        return $this->wallets;
    }
}