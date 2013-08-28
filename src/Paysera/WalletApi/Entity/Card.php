<?php

/**
 * Entity representing one Item in a Payment
 */
class Paysera_WalletApi_Entity_Card
{
    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $issuer;

    /**
     * Set number
     *
     * @param string $number
     *
     * @return Paysera_WalletApi_Entity_Card
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set issuer
     *
     * @param string $issuer
     *
     * @return Paysera_WalletApi_Entity_Card
     */
    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;

        return $this;
    }

    /**
     * Get issuer
     *
     * @return string
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * @return self
     */
    static public function create()
    {
        return new static();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s:%s', $this->getIssuer(), $this->getNumber());
    }
}