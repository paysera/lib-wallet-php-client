<?php

/**
 * Entity representing identifier for wallet
 */
class Paysera_WalletApi_Entity_WalletIdentifier
{
    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Paysera_WalletApi_Entity_Card
     */
    protected $card;

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
     * Gets email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set card
     *
     * @param Paysera_WalletApi_Entity_Card $card
     *
     * @return Paysera_WalletApi_Entity_WalletIdentifier
     */
    public function setCard(Paysera_WalletApi_Entity_Card $card)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * Get card
     *
     * @return Paysera_WalletApi_Entity_Card
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * Sets email
     *
     * @param string $email

     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Gets phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets phone
     *
     * @param string $phone

     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
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
     * Sets id
     *
     * @param integer $id

     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Validation for wallet identifier entity
     *
     * @return boolean
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    public function validate()
    {
        $setValueCount = count(array_diff(
            array($this->getId(), $this->getCard(), $this->getPhone(), $this->getEmail()),
            array(null)
        ));

        if ($setValueCount == 0) {
            throw new Paysera_WalletApi_Exception_LogicException("At least one identifier must be set");
        } else if ($setValueCount > 1) {
            throw new Paysera_WalletApi_Exception_LogicException("Only one identifier can be set at the same time");
        }

        return true;
    }
}