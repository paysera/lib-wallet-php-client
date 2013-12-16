<?php

/**
 * Entity representing User
 */
class Paysera_WalletApi_Entity_User
{
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

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
     * @var string
     */
    protected $gender;

    /**
     * @var string
     */
    protected $dob;

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
     * @return Paysera_WalletApi_Entity_User_Address
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
     * @return Paysera_WalletApi_Entity_User_Identity
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

    /**
     * Setter of Id
     *
     * @param int $id
     *
     * @return Paysera_WalletApi_Entity_User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Setter of Address
     *
     * @param \Paysera_WalletApi_Entity_User_Address $address
     *
     * @return Paysera_WalletApi_Entity_User
     */
    public function setAddress(\Paysera_WalletApi_Entity_User_Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Setter of Email
     *
     * @param string $email
     *
     * @return Paysera_WalletApi_Entity_User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Setter of Identity
     *
     * @param \Paysera_WalletApi_Entity_User_Identity $identity
     *
     * @return Paysera_WalletApi_Entity_User
     */
    public function setIdentity(\Paysera_WalletApi_Entity_User_Identity $identity)
    {
        $this->identity = $identity;

        return $this;
    }

    /**
     * Setter of Phone
     *
     * @param string $phone
     *
     * @return Paysera_WalletApi_Entity_User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Gets dob
     *
     * @return string
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * Gets gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Returns null if gender is unknown, true/false if gender is known
     *
     * @return bool|null
     */
    public function isGenderMale()
    {
        return $this->gender !== null ? $this->gender === self::GENDER_MALE : null;
    }

    /**
     * Returns null if gender is unknown, true/false if gender is known
     *
     * @return bool|null
     */
    public function isGenderFemale()
    {
        return $this->gender !== null ? $this->gender === self::GENDER_FEMALE : null;
    }
}