<?php

/**
 * Entity representing Account, related to Wallet
 */
class Paysera_WalletApi_Entity_Wallet_Account
{
    /**
     * @var string
     * @readonly
     */
    protected $number;

    /**
     * @var string|null
     * @readonly
     */
    protected $ownerTitle;

    /**
     * @var string
     * @readonly
     */
    protected $ownerDisplayName;

    /**
     * @var string
     * @readonly
     */
    protected $description;

    /**
     * @var string
     * @readonly
     */
    protected $type;

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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string|null
     */
    public function getOwnerTitle()
    {
        return $this->ownerTitle;
    }

    /**
     * @return string
     */
    public function getOwnerDisplayName()
    {
        return $this->ownerDisplayName;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
